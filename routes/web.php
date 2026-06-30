<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TikTokPostController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// ─── Home ───
Route::get('/', function () { return Inertia::render('Home');})->name('home');
Route::get('/movie-recap', function () { return Inertia::render('MovieRecap/Show');})->name('recap.index');

// ─── Blogs ───
Route::get('/blogs', [TikTokPostController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{post:slug}', [TikTokPostController::class, 'show'])->name('posts.show');
Route::get('/blogs/topics/{topic}', [TikTokPostController::class, 'category'])->name('topics.show');


// ─── Google OAuth ───
Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// ─── Manual Login / Logout ───
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, remember: true)) {
        $request->session()->regenerate();
        return redirect()->intended(route('recap.show'));
    }

    return back()->withErrors([
        'email' => 'Email သို့မဟုတ် Password မှားနေသည်။',
    ]);
})->name('login');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('recap.show');
})->name('logout');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/posts/{id}', [TikTokPostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/edit/{id}', [TikTokPostController::class, 'edit'])->name('posts.edit');
    Route::post('/posts/{id}', [TikTokPostController::class, 'update'])->name('posts.update');

    // routes/api.php
    Route::get('/jobs/status/{jobId}', [JobController::class, 'status']);
    
    // ─── Dashboard ───
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['verified'])->name('dashboard');

    Route::get('/dashboard/recap', function (Request $request) {
        $userData = $request->user() ? [
            'username'  => $request->user()->username,
            'role_name' => $request->user()->role_name,
        ] : null;

        return Inertia::render('MovieRecap/DashboardRecap', [
            'user' => $userData,
        ]);
    })->name('recap.dashboardrecap');

    Route::get('/dashboard/posts', [TikTokPostController::class, 'dashboardIndex'])->name('blogs.dashboardshow');
});


collect(['About' => 'About','ContactUs' => 'ContactUs','Policy' => 'Policy','Disclaimer' => 'Disclaimer',])->each(fn ($view, $uri) => Route::inertia("/{$uri}", $view)->name($uri));

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
