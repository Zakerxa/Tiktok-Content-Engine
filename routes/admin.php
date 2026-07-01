<?php

use App\Http\Controllers\Admin\ServerController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| All routes protected by auth + admin middleware.
| Include this file in your main web.php:
|   require __DIR__.'/admin.php';
*/

Route::prefix('admin')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');

        // User management
        Route::get('/users',                    [AdminController::class, 'users'])->name('admin.users');
        Route::post('/users/{id}/renew',        [AdminController::class, 'renew'])->name('admin.users.renew');
        Route::post('/users/{id}/toggle',       [AdminController::class, 'toggleActive'])->name('admin.users.toggle');
        Route::post('/users/{id}/password',     [AdminController::class, 'resetPassword'])->name('admin.users.password');
        Route::delete('/users/{id}',            [AdminController::class, 'deleteUser'])->name('admin.users.delete');

        // Role management
        Route::get('/roles',                    [AdminController::class, 'roles'])->name('admin.roles');
        Route::post('/roles/{name}',            [AdminController::class, 'updateRole'])->name('admin.roles.update');

        // Plan history
        Route::get('/history/{username}',       [AdminController::class, 'history'])->name('admin.history');

        // Server management
        Route::get('/servers',                  [AdminController::class, 'servers'])->name('admin.servers');
        Route::post('/servers',                 [AdminController::class, 'storeServer'])->name('admin.servers.store');
        Route::post('/servers/{server}',         [AdminController::class, 'updateServer'])->name('admin.servers.update');
        Route::delete('/servers/{server}',       [AdminController::class, 'deleteServer'])->name('admin.servers.delete');
        // ✅ Route::resource(...) line ကို ဖျက်လိုက်ပါ
    });
