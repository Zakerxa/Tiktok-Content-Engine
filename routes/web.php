<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TikTokPostController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [TikTokPostController::class, 'index'])->name('home');
Route::get('/posts/{post}', [TikTokPostController::class, 'show'])->name('posts.show');
Route::get('/topics/{topic}', [TikTokPostController::class, 'category'])->name('topics.show');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
