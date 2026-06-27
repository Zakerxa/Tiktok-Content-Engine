<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TikTokPostController;
use App\Models\TikTokPost;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// ─── Home ───
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// ─── Blogs ───
Route::get('/blogs', [TikTokPostController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{post:slug}', [TikTokPostController::class, 'show'])->name('posts.show');
Route::get('/blogs/topics/{topic}', [TikTokPostController::class, 'category'])->name('topics.show');

// ─── Recap Main Page ───
Route::get('/recap', function (Request $request) {
   $userData = $request->user() ? [
        'username'  => $request->user()->username,
        'role_name' => $request->user()->role_name,
    ] : null;

    return Inertia::render('MovieRecap/Show', [
        'user' => $userData,
    ]);
})->name('recap.show');

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


// ─── Dashboard ───
Route::get('/dashboard', function (Request $request) {
    $userId = $request->user()->id;

    $query = TikTokPost::query()->where('user_id', $userId);

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('cover_title_burmese', 'like', '%' . $request->search . '%');
        });
    }
    if ($request->filled('topic')) {
        $rawTopic = str_starts_with($request->topic, 'TOPICS_') ? $request->topic : 'TOPICS_' . $request->topic;
        $query->where('topic', $rawTopic);
    }

    $posts = $query->orderBy('id', 'desc')
        ->paginate(6)
        ->withQueryString()
        ->through(fn($post) => [
            'id'                  => $post->id,
            'title'               => $post->title,
            'slug'                => $post->slug,
            'cover_title_burmese' => $post->cover_title_burmese,
            'content'             => $post->content,
            'topic'               => str_replace('TOPICS_', '', $post->topic),
            'image_path'          => $post->image_path,
            'model_used'          => $post->model_used,
            'created_at'          => $post->created_at?->format('M d, Y') ?? 'Recent',
        ]);

    $stats = TikTokPost::select('topic', DB::raw('count(*) as total'))
        ->where('user_id', $userId)
        ->whereNotNull('topic')
        ->groupBy('topic')
        ->get()
        ->map(fn($stat) => [
            'clean' => str_replace('TOPICS_', '', $stat->topic),
            'total' => $stat->total,
        ]);

    return Inertia::render('Dashboard', [
        'posts'   => $posts,
        'stats'   => $stats,
        'filters' => $request->only(['search', 'topic']),
        'user'    => [
            'username'         => $request->user()->username,
            'email'            => $request->user()->email,
            'avatar'           => $request->user()->avatar,
            'role_name'        => $request->user()->role_name,
            'recap_limit'      => $request->user()->recap_limit,
            'total_recap_used' => $request->user()->total_recap_used,
            'plan_expires_at'  => $request->user()->plan_expires_at,
            'is_active'        => $request->user()->is_active,
        ],
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/posts/{id}', [TikTokPostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/edit/{id}', [TikTokPostController::class, 'edit'])->name('posts.edit');
    Route::post('/posts/{id}', [TikTokPostController::class, 'update'])->name('posts.update');
});

require __DIR__ . '/auth.php';
