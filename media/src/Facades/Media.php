<?php

namespace Polirium\Core\Media\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Polirium\Core\Media\Models\Media upload(\Illuminate\Http\UploadedFile $file, array $options = [])
 * @method static array uploadMultiple(array $files, array $options = [])
 * @method static \Polirium\Core\Media\Models\Media uploadFromUrl(string $url, array $options = [])
 * @method static \Polirium\Core\Media\Models\Media uploadFromBase64(string $base64, array $options = [])
 * @method static \Polirium\Core\Media\Models\Media|null get(int $id)
 * @method static bool delete(int $id)
 * @method static int deleteMultiple(array $ids)
 * @method static bool attachToModel(int $mediaId, \Illuminate\Database\Eloquent\Model $model, string $collection = 'default')
 * @method static bool detachFromModel(int $mediaId, \Illuminate\Database\Eloquent\Model $model)
 * @method static bool updateMetadata(int $mediaId, array $metadata)
 * @method static bool generateConversions(int $mediaId)
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse download(int $id, string $conversion = '')
 * @method static \Illuminate\Support\Collection getByCollection(string $collection)
 * @method static \Illuminate\Support\Collection search(string $query, array $filters = [])
 * @method static \Illuminate\Pagination\LengthAwarePaginator paginate(int $perPage = 15, array $filters = [])
 *
 * @see \Polirium\Core\Media\Services\MediaService
 */
class Media extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'media.service';
    }
}
