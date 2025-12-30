<?php

use Illuminate\Support\Facades\Route;
use Polirium\Core\Media\Http\Controllers\MediaController;

Route::middleware(['web', 'auth'])->prefix('admin/media')->name('media.')->group(function () {
    Route::get('/', [MediaController::class, 'index'])->name('index');
    Route::post('/upload', [MediaController::class, 'store'])->name('upload');
    Route::get('/{id}', [MediaController::class, 'show'])->name('show');
    Route::put('/{id}', [MediaController::class, 'update'])->name('update');
    Route::delete('/{id}', [MediaController::class, 'destroy'])->name('delete');
    Route::get('/{id}/download', [MediaController::class, 'download'])->name('download');
    Route::post('/bulk-delete', [MediaController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('/upload-from-url', [MediaController::class, 'uploadFromUrl'])->name('upload-from-url');
});
