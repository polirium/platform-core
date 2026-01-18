<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Allowed File Extensions
    |--------------------------------------------------------------------------
    |
    | Define which file extensions are allowed to be uploaded.
    |
    */
    'allowed_extensions' => [
        // Images
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
        'svg',
        'bmp',
        'ico',

        // Documents
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'ppt',
        'pptx',
        'txt',
        'csv',

        // Archives
        'zip',
        'rar',
        '7z',
        'tar',
        'gz',

        // Videos
        'mp4',
        'avi',
        'mov',
        'wmv',
        'flv',
        'mkv',

        // Audio
        'mp3',
        'wav',
        'ogg',
        'wma',
        'aac',
    ],

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size in bytes. Default is 10MB.
    |
    */
    'max_file_size' => env('MEDIA_MAX_FILE_SIZE', 10 * 1024 * 1024), // 10MB

    /*
    |--------------------------------------------------------------------------
    | Maximum Files Per Upload
    |--------------------------------------------------------------------------
    |
    | Maximum number of files allowed in a single upload batch.
    |
    */
    'max_files_per_upload' => env('MEDIA_MAX_FILES_PER_UPLOAD', 20),

    /*
    |--------------------------------------------------------------------------
    | Blocked Extensions (Security)
    |--------------------------------------------------------------------------
    |
    | These extensions are ALWAYS blocked for security reasons.
    | Cannot be overridden by user settings.
    |
    */
    'blocked_extensions' => [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phar',
        'exe', 'sh', 'bat', 'cmd', 'com', 'scr', 'msi', 'dll',
        'pl', 'cgi', 'py', 'rb', 'htaccess', 'htpasswd', 'asp', 'aspx', 'jsp'
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Conversions
    |--------------------------------------------------------------------------
    |
    | Define image conversion sizes for thumbnails and responsive images.
    |
    */
    'image_conversions' => [
        'thumb' => [
            'width' => 150,
            'height' => 150,
            'fit' => 'crop',
        ],
        'small' => [
            'width' => 300,
            'height' => 300,
            'fit' => 'contain',
        ],
        'medium' => [
            'width' => 800,
            'height' => 600,
            'fit' => 'contain',
        ],
        'large' => [
            'width' => 1920,
            'height' => 1080,
            'fit' => 'contain',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Disk
    |--------------------------------------------------------------------------
    |
    | The default disk to use for media storage.
    |
    */
    'default_disk' => env('MEDIA_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Default Collection
    |--------------------------------------------------------------------------
    |
    | The default collection name for media.
    |
    */
    'default_collection' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Chunk Size
    |--------------------------------------------------------------------------
    |
    | Chunk size in bytes for chunked uploads. Default is 1MB.
    |
    */
    'chunk_size' => 1024 * 1024, // 1MB

    /*
    |--------------------------------------------------------------------------
    | Enable Image Optimization
    |--------------------------------------------------------------------------
    |
    | Enable automatic image optimization after upload.
    |
    */
    'optimize_images' => env('MEDIA_OPTIMIZE_IMAGES', true),

    /*
    |--------------------------------------------------------------------------
    | Generate Responsive Images
    |--------------------------------------------------------------------------
    |
    | Automatically generate responsive images for uploaded images.
    |
    */
    'generate_responsive_images' => env('MEDIA_GENERATE_RESPONSIVE', true),

    /*
    |--------------------------------------------------------------------------
    | Temporary Upload Expiration
    |--------------------------------------------------------------------------
    |
    | Time in minutes before temporary uploads are deleted.
    |
    */
    'temporary_upload_expiration' => 60, // 1 hour

    /*
    |--------------------------------------------------------------------------
    | Media URL Prefix
    |--------------------------------------------------------------------------
    |
    | URL prefix for media files.
    |
    */
    'url_prefix' => env('MEDIA_URL_PREFIX', '/media'),

    /*
    |--------------------------------------------------------------------------
    | Enable Watermark
    |--------------------------------------------------------------------------
    |
    | Enable watermark for images.
    |
    */
    'enable_watermark' => env('MEDIA_ENABLE_WATERMARK', false),

    /*
    |--------------------------------------------------------------------------
    | Watermark Settings
    |--------------------------------------------------------------------------
    |
    | Watermark configuration.
    |
    */
    'watermark' => [
        'image' => null, // Path to watermark image
        'position' => 'bottom-right', // top-left, top-right, bottom-left, bottom-right, center
        'opacity' => 50, // 0-100
        'padding' => 10, // pixels
    ],

    /*
    |--------------------------------------------------------------------------
    | MIME Types
    |--------------------------------------------------------------------------
    |
    | Define MIME types for file validation.
    |
    */
    'mime_types' => [
        'images' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
        'documents' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'videos' => ['video/mp4', 'video/avi', 'video/quicktime'],
        'audio' => ['audio/mpeg', 'audio/wav', 'audio/ogg'],
    ],
];
