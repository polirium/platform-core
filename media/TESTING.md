# Media Module - Quick Test Guide

## ✅ Verification Checklist

### 1. Migration Status
```bash
php artisan migrate:status | grep media
```
Expected: `2024_11_26_000001_create_media_table` should show as "Ran"

### 2. Routes Registered
```bash
php artisan route:list --name=media
```
Expected: Should show 9 routes (web + API)

### 3. Service Provider Loaded
```bash
php artisan about
```
Check if `Polirium\Core\Media\Providers\MediaServiceProvider` is listed

### 4. Helper Functions Available
Create test route in `routes/web.php`:
```php
Route::get('/test-media-helpers', function() {
    // Test if helpers are loaded
    $functions = [
        'media_upload',
        'media_url',
        'media_get',
        'media_delete',
        'media_of',
        'media_download',
        'media_is_image',
        'media_thumbnail',
    ];
    
    $available = [];
    foreach ($functions as $func) {
        $available[$func] = function_exists($func);
    }
    
    return response()->json($available);
});
```

Visit: `http://localhost/test-media-helpers`
Expected: All functions should return `true`

### 5. Facade Available
Create test route:
```php
Route::get('/test-media-facade', function() {
    try {
        $service = app('media.service');
        return response()->json([
            'facade_available' => true,
            'service_class' => get_class($service),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'facade_available' => false,
            'error' => $e->getMessage(),
        ]);
    }
});
```

### 6. Test Upload (Manual)
Create simple upload form:
```html
<!DOCTYPE html>
<html>
<head>
    <title>Test Media Upload</title>
</head>
<body>
    <h1>Test Media Upload</h1>
    <form action="/api/media/upload" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <input type="text" name="collection" value="test" placeholder="Collection">
        <button type="submit">Upload</button>
    </form>
</body>
</html>
```

### 7. Test with Model
Create test in `routes/web.php`:
```php
Route::get('/test-media-model', function() {
    $user = \Polirium\Core\Base\Http\Models\User::first();
    
    if (!$user) {
        return 'No user found';
    }
    
    // Check if trait methods are available
    $methods = [
        'uploadMedia',
        'getFirstMediaUrl',
        'getAllMediaUrls',
        'hasMedia',
        'deleteAllMedia',
    ];
    
    $available = [];
    foreach ($methods as $method) {
        $available[$method] = method_exists($user, $method);
    }
    
    return response()->json([
        'user_id' => $user->id,
        'methods_available' => $available,
    ]);
});
```

## 🧪 API Testing with cURL

### Upload File
```bash
curl -X POST http://localhost/api/media/upload \
  -F "file=@/path/to/test-image.jpg" \
  -F "collection=test" \
  -F "name=Test Image"
```

### List Media
```bash
curl http://localhost/api/media
```

### Get Specific Media
```bash
curl http://localhost/api/media/1
```

### Delete Media
```bash
curl -X DELETE http://localhost/api/media/1
```

### Upload from URL
```bash
curl -X POST http://localhost/api/media/upload-from-url \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://via.placeholder.com/150",
    "collection": "test",
    "name": "Placeholder Image"
  }'
```

## ✅ Expected Results

All tests should pass with:
- ✅ Migration ran successfully
- ✅ 9 routes registered
- ✅ Service provider loaded
- ✅ 13 helper functions available
- ✅ Facade accessible
- ✅ Model trait methods available
- ✅ API endpoints responding

## 🎉 Success Indicators

If all above tests pass, the Media Management Module is **fully operational** and ready to use!
