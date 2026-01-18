<?php

namespace Polirium\Core\Media\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Polirium\Core\Media\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaServeController extends Controller
{
    /**
     * List of blocked extensions for security.
     */
    protected array $blockedExtensions = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phar',
        'exe', 'sh', 'bat', 'cmd', 'com', 'scr', 'msi', 'dll',
        'pl', 'cgi', 'py', 'rb', 'js', 'htaccess', 'htpasswd'
    ];

    /**
     * Serve a media file securely.
     *
     * @param string $slug UUID or ID of the media
     * @param string $extension File extension
     */
    public function serve(string $slug, string $extension)
    {
        // 1. Block dangerous extensions
        if ($this->isBlockedExtension($extension)) {
            abort(403, 'Forbidden file type');
        }

        // 2. Find media by UUID or ID
        $media = Media::where('uuid', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (!$media) {
            abort(404, 'Media not found');
        }

        // 3. Validate extension matches the file
        $fileExtension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
        if ($fileExtension !== strtolower($extension)) {
            abort(404, 'Extension mismatch');
        }

        // 4. Get the file path
        $disk = $media->disk ?? config('media.default_disk', 'public');
        $path = $media->getPath();

        if (!Storage::disk($disk)->exists($path)) {
            abort(404, 'File not found');
        }

        // 5. Serve file with proper headers
        $fullPath = Storage::disk($disk)->path($path);
        $mimeType = $media->mime_type ?? mime_content_type($fullPath);

        // Build headers
        $headers = [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ];

        // Add extra security headers for SVG files to prevent script execution
        if ($mimeType === 'image/svg+xml') {
            $headers['Content-Security-Policy'] = "default-src 'none'; style-src 'unsafe-inline'; sandbox";
            $headers['X-XSS-Protection'] = '1; mode=block';
        }

        return response()->file($fullPath, $headers);
    }

    /**
     * Serve a media file by ID (alternative route).
     */
    public function serveById(int $id)
    {
        $media = Media::find($id);

        if (!$media) {
            abort(404, 'Media not found');
        }

        $extension = pathinfo($media->file_name, PATHINFO_EXTENSION);

        // Block dangerous extensions
        if ($this->isBlockedExtension($extension)) {
            abort(403, 'Forbidden file type');
        }

        $disk = $media->disk ?? config('media.default_disk', 'public');
        $path = $media->getPath();

        if (!Storage::disk($disk)->exists($path)) {
            abort(404, 'File not found');
        }

        $fullPath = Storage::disk($disk)->path($path);
        $mimeType = $media->mime_type ?? mime_content_type($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Download a media file.
     */
    public function download(string $slug, string $extension)
    {
        // Block dangerous extensions
        if ($this->isBlockedExtension($extension)) {
            abort(403, 'Forbidden file type');
        }

        $media = Media::where('uuid', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (!$media) {
            abort(404, 'Media not found');
        }

        $disk = $media->disk ?? config('media.default_disk', 'public');
        $path = $media->getPath();

        if (!Storage::disk($disk)->exists($path)) {
            abort(404, 'File not found');
        }

        return Storage::disk($disk)->download($path, $media->file_name);
    }

    /**
     * Check if an extension is blocked.
     */
    protected function isBlockedExtension(string $extension): bool
    {
        // Merge with config-based blocked extensions
        $configBlocked = config('media.blocked_extensions', []);
        $allBlocked = array_merge($this->blockedExtensions, $configBlocked);

        return in_array(strtolower($extension), array_map('strtolower', $allBlocked));
    }
}
