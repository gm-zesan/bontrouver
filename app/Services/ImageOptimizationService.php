<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    /**
     * Optimize an uploaded file, convert to WebP format, and save to storage.
     */
    public function optimizeUploadedFile(
        UploadedFile $file,
        string $directory = 'listings',
        int $maxWidth = 1600,
        int $maxHeight = 1600,
        int $quality = 85
    ): string {
        $realPath = $file->getRealPath();
        if (!$realPath || !file_exists($realPath)) {
            $saved = $file->store($directory, 'public');
            return '/storage/' . $saved;
        }

        try {
            return $this->processAndSaveWebp($realPath, $directory, $maxWidth, $maxHeight, $quality);
        } catch (\Throwable $e) {
            Log::warning("WebP optimization failed, falling back to original upload: " . $e->getMessage());
            $saved = $file->store($directory, 'public');
            return '/storage/' . $saved;
        }
    }

    /**
     * Optimize a base64 encoded data URL and save as WebP.
     */
    public function optimizeBase64Image(
        string $base64Data,
        string $directory = 'listings',
        int $maxWidth = 1600,
        int $maxHeight = 1600,
        int $quality = 85
    ): ?string {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            return null;
        }

        $data = substr($base64Data, strpos($base64Data, ',') + 1);
        $decoded = base64_decode($data);
        if ($decoded === false) {
            return null;
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'bt_img_');
        file_put_contents($tempPath, $decoded);

        try {
            $result = $this->processAndSaveWebp($tempPath, $directory, $maxWidth, $maxHeight, $quality);
            @unlink($tempPath);
            return $result;
        } catch (\Throwable $e) {
            @unlink($tempPath);
            Log::warning("Base64 WebP optimization failed: " . $e->getMessage());
            
            // Fallback: save raw png/jpg
            $filename = $directory . '/' . Str::random(40) . '.png';
            Storage::disk('public')->put($filename, $decoded);
            return '/storage/' . $filename;
        }
    }

    /**
     * Core processing: Load GD resource, correct EXIF orientation, scale down, and write WebP.
     */
    protected function processAndSaveWebp(
        string $sourcePath,
        string $directory,
        int $maxWidth,
        int $maxHeight,
        int $quality
    ): string {
        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo) {
            throw new \Exception("Invalid image file or unsupported format.");
        }

        $mime = $imageInfo['mime'] ?? '';
        $srcWidth = $imageInfo[0];
        $srcHeight = $imageInfo[1];

        // Create GD image resource based on mime type
        $srcImage = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($sourcePath),
            'image/png'               => @imagecreatefrompng($sourcePath),
            'image/webp'              => @imagecreatefromwebp($sourcePath),
            'image/gif'               => @imagecreatefromgif($sourcePath),
            'image/bmp', 'image/x-ms-bmp' => @imagecreatefrombmp($sourcePath),
            default                   => null,
        };

        if (!$srcImage) {
            throw new \Exception("Could not create GD image from mime: {$mime}");
        }

        // Auto-orient based on EXIF orientation (JPEG only)
        if (function_exists('exif_read_data') && in_array($mime, ['image/jpeg', 'image/jpg'])) {
            try {
                $exif = @exif_read_data($sourcePath);
                if (!empty($exif['Orientation'])) {
                    $srcImage = match ($exif['Orientation']) {
                        3 => imagerotate($srcImage, 180, 0),
                        6 => imagerotate($srcImage, -90, 0),
                        8 => imagerotate($srcImage, 90, 0),
                        default => $srcImage,
                    };
                    $srcWidth = imagesx($srcImage);
                    $srcHeight = imagesy($srcImage);
                }
            } catch (\Throwable $e) {
                // Ignore exif parsing errors
            }
        }

        // Calculate scaled dimensions
        $targetWidth = $srcWidth;
        $targetHeight = $srcHeight;

        if ($srcWidth > $maxWidth || $srcHeight > $maxHeight) {
            $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight);
            $targetWidth = (int) round($srcWidth * $ratio);
            $targetHeight = (int) round($srcHeight * $ratio);
        }

        // Create target canvas with alpha channel support
        $dstImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
        $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
        imagefilledrectangle($dstImage, 0, 0, $targetWidth, $targetHeight, $transparent);

        // High-quality resample
        imagecopyresampled(
            $dstImage,
            $srcImage,
            0, 0, 0, 0,
            $targetWidth,
            $targetHeight,
            $srcWidth,
            $srcHeight
        );

        // Ensure target directory exists on disk
        $filename = Str::random(40) . '.webp';
        $relativeDir = trim($directory, '/');
        $relativeStoragePath = $relativeDir . '/' . $filename;
        
        $storageDisk = Storage::disk('public');
        $absoluteDirPath = $storageDisk->path($relativeDir);
        if (!file_exists($absoluteDirPath)) {
            @mkdir($absoluteDirPath, 0755, true);
        }

        $absoluteTargetFile = $storageDisk->path($relativeStoragePath);

        // Save as WebP
        $success = imagewebp($dstImage, $absoluteTargetFile, $quality);

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        if (!$success || !file_exists($absoluteTargetFile)) {
            throw new \Exception("Failed to write WebP file to disk.");
        }

        return '/storage/' . $relativeStoragePath;
    }
}
