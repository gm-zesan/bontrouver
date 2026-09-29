<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_conversation_id',
        'sender_id',
        'sender_type',
        'message',
        'attachments',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(SupportConversation::class, 'support_conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getAttachmentUrlsAttribute(): array
    {
        if (empty($this->attachments) || !is_array($this->attachments)) {
            return [];
        }

        return array_map(function ($item) {
            $path = is_array($item) ? ($item['path'] ?? ($item['url'] ?? '')) : $item;
            return self::resolveFileUrl($path);
        }, $this->attachments);
    }

    public function getAttachmentFilesAttribute(): array
    {
        if (empty($this->attachments) || !is_array($this->attachments)) {
            return [];
        }

        return array_map(function ($item) {
            $path = is_array($item) ? ($item['path'] ?? ($item['url'] ?? '')) : $item;
            $name = is_array($item) ? ($item['name'] ?? basename($path)) : basename($path);
            $size = is_array($item) ? ($item['size'] ?? null) : null;
            $cleanName = basename($name ?: $path);
            $ext = strtolower(pathinfo($cleanName, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
            $isPdf = $ext === 'pdf';
            $isDoc = in_array($ext, ['doc', 'docx', 'txt', 'rtf']);
            $isSpreadsheet = in_array($ext, ['xls', 'xlsx', 'csv']);

            $url = self::resolveFileUrl($path);

            if (!$size && Storage::disk('public')->exists($path)) {
                $size = Storage::disk('public')->size($path);
            }

            return [
                'path' => $path,
                'name' => $cleanName,
                'url' => $url,
                'extension' => $ext,
                'is_image' => $isImage,
                'is_pdf' => $isPdf,
                'is_doc' => $isDoc,
                'is_spreadsheet' => $isSpreadsheet,
                'size_human' => $size ? self::formatBytes((int) $size) : null,
                'size_bytes' => $size,
            ];
        }, $this->attachments);
    }

    /**
     * Resolve any disk path or absolute local URL to a reliable root-relative URL.
     */
    public static function resolveFileUrl(?string $pathOrUrl): string
    {
        if (empty($pathOrUrl)) {
            return '';
        }

        // If it's an external URL (e.g. S3, Unsplash), preserve it
        if (preg_match('/^https?:\/\/(?!localhost|127\.0\.0\.1)/i', $pathOrUrl)) {
            return $pathOrUrl;
        }

        // Strip localhost / 127.0.0.1 and port to guarantee it works on any dev or prod port
        if (preg_match('/^https?:\/\/(?:localhost|127\.0\.0\.1)(?::\d+)?(\/.*)$/i', $pathOrUrl, $matches)) {
            return $matches[1];
        }

        if (str_starts_with($pathOrUrl, '/storage/')) {
            return $pathOrUrl;
        }

        if (str_starts_with($pathOrUrl, 'storage/')) {
            return '/' . $pathOrUrl;
        }

        return '/storage/' . ltrim($pathOrUrl, '/');
    }

    public static function formatBytes(int $bytes, int $precision = 1): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
