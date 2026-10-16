<?php

use Illuminate\Support\Facades\Route;
use Polirium\Core\Media\Http\Controllers\Api\MediaApiController;

Route::prefix('media')->name('api.media.')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/', [MediaApiController::class, 'index'])->middleware('can:media.index')->name('index');
    Route::post('/upload', [MediaApiController::class, 'upload'])->middleware('can:media.upload')->name('upload');
    Route::post('/upload-from-url', [MediaApiController::class, 'uploadFromUrl'])->middleware('can:media.upload')->name('upload-from-url');
    Route::post('/upload-from-base64', [MediaApiController::class, 'uploadFromBase64'])->middleware('can:media.upload')->name('upload-from-base64');
    Route::get('/{id}', [MediaApiController::class, 'show'])->middleware('can:media.index')->name('show');
    Route::put('/{id}', [MediaApiController::class, 'update'])->middleware('can:media.update')->name('update');
    Route::delete('/{id}', [MediaApiController::class, 'destroy'])->middleware('can:media.delete')->name('destroy');
    Route::post('/bulk-delete', [MediaApiController::class, 'bulkDelete'])->middleware('can:media.delete')->name('bulk-delete');
});
