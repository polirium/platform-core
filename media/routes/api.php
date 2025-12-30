<?php

use Illuminate\Support\Facades\Route;
use Polirium\Core\Media\Http\Controllers\Api\MediaApiController;

Route::prefix('media')->name('api.media.')->middleware(['api'])->group(function () {
    Route::get('/', [MediaApiController::class, 'index']);
    Route::post('/upload', [MediaApiController::class, 'upload']);
    Route::get('/{id}', [MediaApiController::class, 'show']);
    Route::put('/{id}', [MediaApiController::class, 'update']);
    Route::delete('/{id}', [MediaApiController::class, 'destroy']);
    Route::post('/bulk-delete', [MediaApiController::class, 'bulkDelete']);
    Route::post('/upload-from-url', [MediaApiController::class, 'uploadFromUrl']);
    Route::post('/upload-from-base64', [MediaApiController::class, 'uploadFromBase64']);
});
