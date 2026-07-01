<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TikTokPostController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ServerStatusController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

// ─── Home ───
Route::inertia('/', 'Home')->name('home');
Route::get('/movie-recap', [DashboardController::class, 'homeRecap'])->name('recap.index');

// ─── Blogs ───
Route::get('/blogs', [TikTokPostController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{post:slug}', [TikTokPostController::class, 'show'])->name('posts.show');
Route::get('/blogs/topics/{topic}', [TikTokPostController::class, 'category'])->name('topics.show');


Route::middleware(['auth','verified'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/posts/{id}', [TikTokPostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/edit/{id}', [TikTokPostController::class, 'edit'])->name('posts.edit');
    Route::post('/posts/{id}', [TikTokPostController::class, 'update'])->name('posts.update');
    //  Server status
    Route::get('/jobs/status/{jobId}', [JobController::class, 'status']);
    Route::get('/server-status', [ServerStatusController::class, 'index']); 
    // ─── Dashboard ───
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['verified'])->name('dashboard');
    Route::get('/dashboard/posts', [TikTokPostController::class, 'dashboardIndex'])->name('blogs.dashboardshow');
    Route::get('/dashboard/recap', [DashboardController::class, 'dashboardRecap'])->name('recap.dashboardrecap');

});



// ─── Google OAuth ───
Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// ─── Manual Login / Logout ───
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


collect(['About' => 'About', 'ContactUs' => 'ContactUs', 'Policy' => 'Policy', 'Disclaimer' => 'Disclaimer',])->each(fn($view, $uri) => Route::inertia("/{$uri}", $view)->name($uri));

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
