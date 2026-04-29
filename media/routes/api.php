<?php

use Illuminate\Support\Facades\Route;
use Polirium\Core\Media\Http\Controllers\Api\MediaApiController;

Route::prefix('media')->name('api.media.')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/', [MediaApiController::class, 'index'])->middleware('can:media.index');
    Route::post('/upload', [MediaApiController::class, 'upload'])->middleware('can:media.upload');
    Route::post('/upload-from-url', [MediaApiController::class, 'uploadFromUrl'])->middleware('can:media.upload');
    Route::post('/upload-from-base64', [MediaApiController::class, 'uploadFromBase64'])->middleware('can:media.upload');
    Route::get('/{id}', [MediaApiController::class, 'show'])->middleware('can:media.index');
    Route::put('/{id}', [MediaApiController::class, 'update'])->middleware('can:media.update');
    Route::delete('/{id}', [MediaApiController::class, 'destroy'])->middleware('can:media.delete');
    Route::post('/bulk-delete', [MediaApiController::class, 'bulkDelete'])->middleware('can:media.delete');
});
