# Media Management Module - Usage Examples

## Quick Start

### 1. Upload File Using Helper Function

```php
use Illuminate\Http\Request;

public function uploadAvatar(Request $request)
{
    $file = $request->file('avatar');
    
    // Simple upload
    $media = media_upload($file);
    
    // Upload to specific collection
    $media = media_upload($file, 'avatars');
    
    // Upload with custom options
    $media = media_upload($file, 'documents', null, [
        'name' => 'My Document',
        'custom_properties' => ['category' => 'important']
    ]);
    
    return $media->getUrl();
}
```

### 2. Upload File Using Facade

```php
use Polirium\Core\Media\Facades\Media;

// Upload file
$media = Media::upload($request->file('file'));

// Upload from URL
$media = Media::uploadFromUrl('https://example.com/image.jpg');

// Upload from base64
$media = Media::uploadFromBase64($base64String);

// Get media
$media = Media::get($id);

// Delete media
Media::delete($id);
```

### 3. Upload File Using Service

```php
use Polirium\Core\Media\Services\MediaService;

class DocumentController extends Controller
{
    protected $mediaService;
    
    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }
    
    public function upload(Request $request)
    {
        $media = $this->mediaService->upload($request->file('document'), [
            'collection' => 'documents',
            'name' => $request->input('name'),
        ]);
        
        return response()->json([
            'id' => $media->id,
            'url' => $media->getUrl(),
        ]);
    }
}
```

## Working with Models

### 1. Add Media Trait to Your Model

```php
use Illuminate\Database\Eloquent\Model;
use Polirium\Core\Media\Traits\HasMediaTrait;

class Product extends Model
{
    use HasMediaTrait;
    
    // Optional: Define custom conversions
    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150);
            
        $this->addMediaConversion('large')
            ->width(1920)
            ->height(1080);
    }
}
```

### 2. Upload Media to Model

```php
$product = Product::find(1);

// Upload single file
$product->uploadMedia($request->file('image'), 'images');

// Upload multiple files
$product->uploadMultipleMedia($request->file('images'), 'gallery');

// Upload from URL
$product->uploadMediaFromUrl('https://example.com/image.jpg', 'images');

// Replace existing media
$product->replaceMedia($request->file('new_image'), 'images');
```

### 3. Retrieve Media from Model

```php
$product = Product::find(1);

// Get first media URL
$imageUrl = $product->getFirstMediaUrl('images');

// Get first media URL with conversion
$thumbUrl = $product->getFirstMediaUrl('images', 'thumb');

// Get all media URLs
$urls = $product->getAllMediaUrls('images');

// Get first media item
$media = $product->getFirstMediaItem('images');

// Get all media items
$mediaCollection = $product->getMedia('images');

// Check if has media
if ($product->hasMedia('images')) {
    // Do something
}

// Get media count
$count = $product->getMediaCount('images');
```

### 4. Delete Media from Model

```php
$product = Product::find(1);

// Delete all media from collection
$product->deleteAllMedia('images');

// Delete all media
$product->deleteAllMedia();
```

## Advanced Usage

### 1. Search and Filter Media

```php
use Polirium\Core\Media\Facades\Media;

// Search by name
$results = media_search('invoice');

// Search with filters
$results = media_search('document', [
    'type' => 'document',
    'collection' => 'invoices',
    'from_date' => '2024-01-01',
    'to_date' => '2024-12-31',
]);

// Paginate media
$media = media_paginate(20, [
    'type' => 'image',
    'collection' => 'products',
]);
```

### 2. Generate Temporary URLs

```php
// Get temporary URL (expires in 1 hour by default)
$url = media_temporary_url($media);

// Custom expiration
$url = media_temporary_url($media, now()->addDays(7));

// With conversion
$url = media_temporary_url($media, now()->addHour(), 'thumb');
```

### 3. Download Media

```php
// Download using helper
return media_download($media);

// Download with conversion
return media_download($media, 'large');

// Download using facade
return Media::download($mediaId);
```

### 4. Update Media Metadata

```php
use Polirium\Core\Media\Facades\Media;

Media::updateMetadata($mediaId, [
    'name' => 'New Name',
    'custom_properties' => [
        'category' => 'updated',
        'tags' => ['important', 'featured'],
    ],
]);
```

### 5. Bulk Operations

```php
use Polirium\Core\Media\Facades\Media;

// Upload multiple files
$files = $request->file('files');
$uploadedMedia = Media::uploadMultiple($files, [
    'collection' => 'gallery',
]);

// Delete multiple media
$ids = [1, 2, 3, 4, 5];
$deletedCount = Media::deleteMultiple($ids);
```

## API Usage

### Upload via API

```bash
# Upload file
curl -X POST http://your-domain.com/api/media/upload \
  -H "Content-Type: multipart/form-data" \
  -F "file=@/path/to/file.jpg" \
  -F "collection=images" \
  -F "name=My Image"

# Upload from URL
curl -X POST http://your-domain.com/api/media/upload-from-url \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://example.com/image.jpg",
    "collection": "images",
    "name": "Downloaded Image"
  }'

# Upload from base64
curl -X POST http://your-domain.com/api/media/upload-from-base64 \
  -H "Content-Type: application/json" \
  -d '{
    "base64": "data:image/png;base64,iVBORw0KGgoAAAANS...",
    "collection": "images",
    "name": "Base64 Image"
  }'
```

### Get Media via API

```bash
# Get all media (paginated)
curl http://your-domain.com/api/media?per_page=20&type=image

# Get specific media
curl http://your-domain.com/api/media/1

# Delete media
curl -X DELETE http://your-domain.com/api/media/1

# Bulk delete
curl -X POST http://your-domain.com/api/media/bulk-delete \
  -H "Content-Type: application/json" \
  -d '{"ids": [1, 2, 3]}'
```

## Configuration

### Customize Media Config

Publish the config file:

```bash
php artisan vendor:publish --tag=media-config
```

Edit `config/media.php`:

```php
return [
    'allowed_extensions' => ['jpg', 'png', 'pdf', 'doc', 'docx'],
    'max_file_size' => 10 * 1024 * 1024, // 10MB
    'image_conversions' => [
        'thumb' => ['width' => 150, 'height' => 150, 'fit' => 'crop'],
        'medium' => ['width' => 800, 'height' => 600, 'fit' => 'contain'],
    ],
    'default_disk' => 'public',
    'optimize_images' => true,
];
```

## Helper Functions Reference

| Function | Description |
|----------|-------------|
| `media_upload($file, $collection, $model, $options)` | Upload a file |
| `media_url($media, $conversion)` | Get media URL |
| `media_get($id)` | Get media by ID |
| `media_delete($id)` | Delete media |
| `media_of($model, $collection)` | Get all media of model |
| `media_download($media, $conversion)` | Download media |
| `media_is_image($media)` | Check if media is image |
| `media_thumbnail($media, $conversion)` | Get thumbnail URL |
| `media_upload_from_url($url, $options)` | Upload from URL |
| `media_upload_from_base64($base64, $options)` | Upload from base64 |
| `media_search($query, $filters)` | Search media |
| `media_paginate($perPage, $filters)` | Paginate media |
| `media_temporary_url($media, $expiration, $conversion)` | Get temporary URL |

## Best Practices

1. **Always validate files before upload**
   ```php
   $request->validate([
       'file' => 'required|file|max:10240|mimes:jpg,png,pdf',
   ]);
   ```

2. **Use collections to organize media**
   ```php
   $user->uploadMedia($avatar, 'avatars');
   $user->uploadMedia($document, 'documents');
   ```

3. **Define conversions for images**
   ```php
   public function registerMediaConversions($media = null): void
   {
       $this->addMediaConversion('thumb')->width(150)->height(150);
   }
   ```

4. **Clean up media when deleting models**
   ```php
   protected static function boot()
   {
       parent::boot();
       
       static::deleting(function ($model) {
           $model->deleteAllMedia();
       });
   }
   ```

5. **Use temporary URLs for private files**
   ```php
   $url = media_temporary_url($media, now()->addMinutes(30));
   ```
