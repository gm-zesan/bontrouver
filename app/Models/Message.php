<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'attachments',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'read_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getAttachmentsAttribute($value)
    {
        $attachments = is_string($value) ? json_decode($value, true) : $value;
        if (empty($attachments) || !is_array($attachments)) {
            return [];
        }

        return array_map(function ($item) {
            if (is_string($item)) {
                $path = $item;
                $url = self::resolveFileUrl($path);
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $type = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']) ? 'image' : 'file';
                return [
                    'path' => $path,
                    'type' => $type,
                    'url' => $url,
                ];
            }
            if (is_array($item)) {
                $path = $item['path'] ?? ($item['url'] ?? '');
                $item['url'] = self::resolveFileUrl($item['url'] ?? $path);
                if (empty($item['type'])) {
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $item['type'] = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']) ? 'image' : 'file';
                }
                return $item;
            }
            return $item;
        }, $attachments);
    }

    public static function resolveFileUrl(?string $pathOrUrl): string
    {
        if (empty($pathOrUrl)) {
            return '';
        }

        if (preg_match('/^https?:\/\/(?!localhost|127\.0\.0\.1)/i', $pathOrUrl)) {
            return $pathOrUrl;
        }

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
}
