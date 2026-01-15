<?php

use Illuminate\Support\Facades\Route;
use Polirium\Core\Base\Http\Controllers\BranchController;
use Polirium\Core\Base\Http\Controllers\BrandController;
use Polirium\Core\Base\Http\Controllers\DashboadController;
use Polirium\Core\Base\Http\Controllers\LocationController;
use Polirium\Core\Base\Http\Controllers\RoleManagerController;
use Polirium\Core\Base\Http\Controllers\UsersManagerController;
use Polirium\Core\Base\Http\Controllers\UserProfileController;

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

/**
 * Keep-Alive Routes (Session & CSRF Token Refresh)
 * These routes are used by the POS system to prevent session timeout
 */
Route::middleware(['web'])->prefix(admin_prefix())->group(function () {
    // Heartbeat - Keep session alive and refresh CSRF token
    Route::post('/heartbeat', function () {
        if (auth()->check()) {
            // Touch session to extend lifetime
            session()->regenerate(false);
            return response()->json([
                'status' => 'ok',
                'csrf_token' => csrf_token(),
                'authenticated' => true,
                'session_lifetime' => config('session.lifetime'),
            ]);
        }
        return response()->json([
            'status' => 'unauthenticated',
            'csrf_token' => csrf_token(),
            'authenticated' => false,
        ], 401);
    })->name('heartbeat');

    // CSRF Token refresh only (lightweight)
    Route::get('/csrf-token', function () {
        return response()->json([
            'csrf_token' => csrf_token(),
        ]);
    })->name('csrf-token');
});

Route::middleware(['web', 'auth'])
    ->prefix(admin_prefix())
    ->name('core.')
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboadController::class, 'index'])
            ->name('index')
            ->middleware('can:dashboard.index');

        /**
         * User Manager
         */
        Route::prefix("users")->name("users.")
            ->controller(UsersManagerController::class)
            ->middleware('can:users.index')
            ->group(function () {
                Route::get('', 'index')->name('index');
            });

        /**
         * Role Permission Manager
         */
        Route::prefix("role")->name("roles.")
            ->controller(RoleManagerController::class)
            ->middleware('can:roles.index')
            ->group(function () {
                Route::get('', 'index')->name('index');
            });

        /**
         * Branch Manager
         */
        Route::prefix("branches")->name("branches.")
            ->controller(BranchController::class)
            ->middleware('can:branches.index')
            ->group(function () {
                Route::get('', 'index')->name('index');
            });

        /**
         * Brand Manager
         */
        Route::prefix("brands")->name("brands.")
            ->controller(BrandController::class)
            ->middleware('can:brands.index')
            ->group(function () {
                Route::get('', 'index')->name('index');
            });

        /**
         * User Profile & Settings (No permission needed - user's own profile)
         */
        Route::prefix("user")->name("user.")
            ->controller(UserProfileController::class)
            ->group(function () {
                Route::get('/profile', 'profile')->name('profile.view');
                Route::post('/profile', 'updateProfile')->name('profile.update');
                Route::get('/settings', 'settings')->name('settings');
            });

        /**
         * Module Manager
         */
        Route::get('/modules', function () {
            return view('core/base::modules.index');
        })->name('modules.index')->middleware('can:modules.index');

        /**
         * Activity Log
         */
        Route::get('/activity-logs', [\Polirium\Core\Base\Http\Controllers\ActivityLogController::class, 'index'])
            ->name('activity-logs.index');
    });

Route::get('/', function () {
    return view('f');
});

require __DIR__.'/auth.php';
