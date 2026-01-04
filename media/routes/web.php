<?php

use Illuminate\Support\Facades\Route;
use Polirium\Core\Media\Http\Controllers\MediaController;

Route::middleware(['web', 'auth'])->prefix('admin/media')->name('media.')->group(function () {
    Route::get('/', [MediaController::class, 'index'])->name('index')->middleware('can:media.index');
    Route::post('/upload', [MediaController::class, 'store'])->name('upload')->middleware('can:media.upload');
    Route::get('/{id}', [MediaController::class, 'show'])->name('show')->middleware('can:media.index');
    Route::put('/{id}', [MediaController::class, 'update'])->name('update')->middleware('can:media.update');
    Route::delete('/{id}', [MediaController::class, 'destroy'])->name('delete')->middleware('can:media.delete');
    Route::get('/{id}/download', [MediaController::class, 'download'])->name('download')->middleware('can:media.download');
    Route::post('/bulk-delete', [MediaController::class, 'bulkDelete'])->name('bulk-delete')->middleware('can:media.delete');
    Route::post('/upload-from-url', [MediaController::class, 'uploadFromUrl'])->name('upload-from-url')->middleware('can:media.upload');
});
