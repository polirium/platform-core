<?php

use Illuminate\Support\Facades\Route;
use Polirium\Core\Settings\Http\Controllers\SettingsController;

Route::prefix(admin_prefix())
    ->middleware(['web', 'auth'])
    ->name('core.settings.')
    ->group(function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('index');
    });
