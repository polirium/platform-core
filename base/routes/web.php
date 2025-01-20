<?php

use Illuminate\Support\Facades\Route;
use Polirium\Core\Base\Http\Controllers\BranchController;
use Polirium\Core\Base\Http\Controllers\BrandController;
use Polirium\Core\Base\Http\Controllers\DashboadController;
use Polirium\Core\Base\Http\Controllers\LocationController;
use Polirium\Core\Base\Http\Controllers\UsersManagerController;

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

// Route::middleware(['web', 'auth', 'can:core.index']) // Nào a sửa lại phân quyền thì anh sửa lại cái comment này giúp e cái =.=
Route::middleware(['web', 'auth'])
    ->prefix(admin_prefix())
    ->name('core.')
    ->group(function () {
        Route::get('/', [DashboadController::class, 'index'])->name('index');

        /**
         * User Manager Menu
         */
        Route::prefix("users")->name("users.")
        ->controller(UsersManagerController::class)
        ->group(function () {
            Route::get('', 'index')->name('index');
        });

        // Chi nhánh
        Route::prefix("branches")->name("branches.")
        ->controller(BranchController::class)
        ->group(function () {
            Route::get('', 'index')->name('index');
        });
        // End Chi nhánh

        // Thương hiệu
        Route::prefix("brands")->name("brands.")
        ->controller(BrandController::class)
        ->group(function () {
            Route::get('', 'index')->name('index');
        });
        // End Thương hiệu
    });

Route::get('/', function () {
    return view('f');
});

require __DIR__.'/auth.php';
