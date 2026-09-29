<?php

namespace App\Console\Commands;

use App\Models\ListingImage;
use App\Services\ImageOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConvertListingImagesToWebp extends Command
{
    protected $signature = 'listings:convert-images-webp {--force : Reconvert even if already webp}';
    protected $description = 'Batch optimizes and converts all existing listing images to WebP format for fast page loads.';

    public function handle(ImageOptimizationService $optimizer): int
    {
        $this->info("Scanning listing images for WebP optimization...");

        $images = ListingImage::all();
        $converted = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($images as $img) {
            $path = $img->image_path;

            if (empty($path)) {
                $skipped++;
                continue;
            }

            // If already .webp and not forcing, skip
            if (str_ends_with(strtolower($path), '.webp') && !$this->option('force')) {
                $skipped++;
                continue;
            }

            // Resolve disk path
            $storageDisk = Storage::disk('public');
            $relative = str_starts_with($path, '/storage/') ? substr($path, 9) : ltrim($path, '/');
            
            if (!$storageDisk->exists($relative)) {
                $skipped++;
                continue;
            }

            $absolutePath = $storageDisk->path($relative);

            try {
                // Optimize and convert
                $imageInfo = @getimagesize($absolutePath);
                if (!$imageInfo) {
                    $skipped++;
                    continue;
                }

                $mime = $imageInfo['mime'] ?? '';
                $src = match ($mime) {
                    'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($absolutePath),
                    'image/png'               => @imagecreatefrompng($absolutePath),
                    'image/webp'              => @imagecreatefromwebp($absolutePath),
                    default                   => null,
                };

                if (!$src) {
                    $skipped++;
                    continue;
                }

                $newFilename = 'listings/' . Str::random(40) . '.webp';
                $newAbsPath = $storageDisk->path($newFilename);

                $width = imagesx($src);
                $height = imagesy($src);

                $targetWidth = min($width, 1600);
                $targetHeight = (int) round($height * ($targetWidth / $width));

                $dst = imagecreatetruecolor($targetWidth, $targetHeight);
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

                imagewebp($dst, $newAbsPath, 85);
                imagedestroy($src);
                imagedestroy($dst);

                if (file_exists($newAbsPath)) {
                    $oldPath = $img->image_path;
                    $img->update(['image_path' => '/storage/' . $newFilename]);
                    $this->line("✔ Converted ID #{$img->id}: {$oldPath} → /storage/{$newFilename}");
                    $converted++;
                }

            } catch (\Throwable $e) {
                $this->error("Failed converting image #{$img->id}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("Done! Converted: {$converted}, Skipped: {$skipped}, Errors: {$errors}");
        return Command::SUCCESS;
    }
}
