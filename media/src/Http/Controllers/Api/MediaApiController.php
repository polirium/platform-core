<?php

namespace Polirium\Core\Media\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Polirium\Core\Media\Services\MediaService;

class MediaApiController extends Controller
{
    /**
     * @var MediaService
     */
    protected $mediaService;

    /**
     * MediaApiController constructor.
     *
     * @param MediaService $mediaService
     */
    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Display a listing of media.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = $request->only(['collection', 'type', 'mime_type', 'from_date', 'to_date']);

            $media = $this->mediaService->paginate($perPage, $filters);

            return response()->json([
                'success' => true,
                'data' => $media->items(),
                'pagination' => [
                    'total' => $media->total(),
                    'per_page' => $media->perPage(),
                    'current_page' => $media->currentPage(),
                    'last_page' => $media->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload a file.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file',
                'collection' => 'nullable|string',
                'name' => 'nullable|string',
            ]);

            $file = $request->file('file');
            $options = [
                'collection' => $request->get('collection', 'default'),
                'name' => $request->get('name'),
                'custom_properties' => $request->get('custom_properties', []),
            ];

            $media = $this->mediaService->upload($file, $options);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'url' => $media->getUrl(),
                    'size' => $media->size,
                    'formatted_size' => $media->formatted_size,
                    'mime_type' => $media->mime_type,
                    'type' => $media->is_image ? 'image' : ($media->is_video ? 'video' : 'document'),
                    'created_at' => $media->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified media.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $media = $this->mediaService->get($id);

            if (! $media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Media not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'url' => $media->getUrl(),
                    'size' => $media->size,
                    'formatted_size' => $media->formatted_size,
                    'mime_type' => $media->mime_type,
                    'collection' => $media->collection_name,
                    'custom_properties' => $media->custom_properties,
                    'created_at' => $media->created_at,
                    'updated_at' => $media->updated_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified media.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        try {
            $request->validate([
                'name' => 'nullable|string',
                'custom_properties' => 'nullable|array',
            ]);

            $metadata = $request->only(['name', 'custom_properties']);
            $updated = $this->mediaService->updateMetadata($id, $metadata);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Media updated successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Media not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update media: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified media.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $deleted = $this->mediaService->delete($id);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Media deleted successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Media not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk delete media.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer',
            ]);

            $ids = $request->get('ids', []);
            $deleted = $this->mediaService->deleteMultiple($ids);

            return response()->json([
                'success' => true,
                'message' => "{$deleted} media files deleted successfully",
                'deleted_count' => $deleted,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload media from URL.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFromUrl(Request $request)
    {
        try {
            $request->validate([
                'url' => 'required|url',
                'collection' => 'nullable|string',
                'name' => 'nullable|string',
            ]);

            $options = [
                'collection' => $request->get('collection', 'default'),
                'name' => $request->get('name'),
            ];

            $media = $this->mediaService->uploadFromUrl($request->get('url'), $options);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded from URL successfully',
                'data' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'url' => $media->getUrl(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload from URL: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload media from base64.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFromBase64(Request $request)
    {
        try {
            $request->validate([
                'base64' => 'required|string',
                'collection' => 'nullable|string',
                'name' => 'nullable|string',
            ]);

            $options = [
                'collection' => $request->get('collection', 'default'),
                'filename' => $request->get('name'),
            ];

            $media = $this->mediaService->uploadFromBase64($request->get('base64'), $options);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded from base64 successfully',
                'data' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'url' => $media->getUrl(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload from base64: ' . $e->getMessage(),
            ], 500);
        }
    }
}
