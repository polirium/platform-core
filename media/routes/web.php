<?php

use Illuminate\Support\Facades\Route;
use Polirium\Core\Media\Http\Controllers\MediaController;
use Polirium\Core\Media\Http\Controllers\MediaServeController;

// Public routes for secure media serving (no auth required)
Route::middleware(['web'])->group(function () {
    // Serve media file: /media/{uuid}.{ext}
    Route::get('/media/{slug}.{extension}', [MediaServeController::class, 'serve'])
        ->name('media.serve')
        ->where('slug', '[a-zA-Z0-9\-]+')
        ->where('extension', '[a-zA-Z0-9]+');

    // Download media file: /media/{uuid}/download.{ext}
    Route::get('/media/{slug}/download.{extension}', [MediaServeController::class, 'download'])
        ->name('media.download.secure')
        ->where('slug', '[a-zA-Z0-9\-]+')
        ->where('extension', '[a-zA-Z0-9]+');

    // Serve by ID (shorthand): /m/{id}
    Route::get('/m/{id}', [MediaServeController::class, 'serveById'])
        ->name('media.serve.id')
        ->where('id', '[0-9]+');
});

Route::middleware(['web', 'auth'])->prefix('admin/media')->name('media.')->group(function () {
    Route::get('/', [MediaController::class, 'index'])->name('index')->middleware('can:media.index');
    Route::get('/settings', [MediaController::class, 'settings'])->name('settings')->middleware('can:media.settings');
    Route::post('/upload', [MediaController::class, 'store'])->name('upload')->middleware('can:media.upload');
    Route::get('/{id}', [MediaController::class, 'show'])->name('show')->middleware('can:media.index');
    Route::put('/{id}', [MediaController::class, 'update'])->name('update')->middleware('can:media.update');
    Route::delete('/{id}', [MediaController::class, 'destroy'])->name('delete')->middleware('can:media.delete');
    Route::get('/{id}/download', [MediaController::class, 'download'])->name('download')->middleware('can:media.download');
    Route::post('/bulk-delete', [MediaController::class, 'bulkDelete'])->name('bulk-delete')->middleware('can:media.delete');
    Route::post('/upload-from-url', [MediaController::class, 'uploadFromUrl'])->name('upload-from-url')->middleware('can:media.upload');
});
