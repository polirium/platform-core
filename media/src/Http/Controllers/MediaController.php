<?php

namespace Polirium\Core\Media\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Polirium\Core\Media\Http\Requests\UploadMediaRequest;
use Polirium\Core\Media\Http\Requests\UpdateMediaRequest;
use Polirium\Core\Media\Services\MediaService;

class MediaController extends Controller
{
    /**
     * @var MediaService
     */
    protected $mediaService;

    /**
     * MediaController constructor.
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
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        \Polirium\Core\UI\Facades\Assets::loadJs(['media-manager']);
        \Polirium\Core\UI\Facades\Assets::loadCss(['media-manager']);

        return view('core/media::index');
    }

    /**
     * Display media settings page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function settings()
    {
        return view('core/media::settings');
    }

    /**
     * Store a newly uploaded media.
     *
     * @param UploadMediaRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UploadMediaRequest $request)
    {
        try {
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
                    'size' => $media->formatted_size,
                    'mime_type' => $media->mime_type,
                ],
            ]);
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
     * @return \Illuminate\Contracts\View\View
     */
    public function show(int $id)
    {
        $media = $this->mediaService->get($id);

        if (!$media) {
            abort(404, 'Media not found');
        }

        return view('media::show', compact('media'));
    }

    /**
     * Update the specified media.
     *
     * @param UpdateMediaRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateMediaRequest $request, int $id)
    {
        try {
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
     * Download the specified media.
     *
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(int $id, Request $request)
    {
        $conversion = $request->get('conversion', '');
        return $this->mediaService->download($id, $conversion);
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
            $ids = $request->get('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No media IDs provided',
                ], 400);
            }

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
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload from URL: ' . $e->getMessage(),
            ], 500);
        }
    }
}
