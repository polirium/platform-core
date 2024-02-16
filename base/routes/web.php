<?php

use Illuminate\Support\Facades\Route;
use Polirium\Core\Base\Http\Controllers\DashboadController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['web', 'auth', 'can:core.index'])
    ->prefix(admin_prefix())
    ->name('core.')
    ->group(function () {
        Route::get('/', [DashboadController::class, 'index'])->name('index');
    });

require __DIR__.'/auth.php';
