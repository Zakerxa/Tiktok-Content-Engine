<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TikTokPostController;
use App\Models\TikTokPost;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

Route::get('/', [TikTokPostController::class, 'index'])->name('home');
Route::get('/posts/{post:slug}', [TikTokPostController::class, 'show'])->name('posts.show');
Route::get('/topics/{topic}', [TikTokPostController::class, 'category'])->name('topics.show');

Route::get('/dashboard', function (Request $request) {
    $query = TikTokPost::query();

    // 1. Search Query
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('cover_title_burmese', 'like', '%' . $request->search . '%');
    }

    // 2. Category / Topic Query
    if ($request->filled('topic')) {
        $rawTopic = str_starts_with($request->topic, 'TOPICS_') ? $request->topic : 'TOPICS_' . $request->topic;
        $query->where('topic', $rawTopic);
    }

    // Paginate လုပ်ပြီး ယူခြင်း
    $posts = TikTokPost::orderBy('id', 'desc')
            ->paginate(6)
            ->withQueryString()
            ->through(fn($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'cover_title_burmese' => $post->cover_title_burmese,
                'content' => $post->content,
                'topic' => str_replace('TOPICS_', '', $post->topic),
                'image_path' => $post->image_path,
                'model_used' => $post->model_used,
                'created_at' => $post->created_at?->format('M d, Y') ?? 'Recent',
            ]);

    // Topics Stats ယူခြင်း
    $stats = TikTokPost::select('topic', DB::raw('count(*) as total'))
        ->whereNotNull('topic')
        ->groupBy('topic')
        ->get()
        ->map(function ($stat) {
            return [
                'clean' => str_replace('TOPICS_', '', $stat->topic),
                'total' => $stat->total
            ];
        });

    return Inertia::render('Dashboard', [
        'posts' => $posts,
        'stats' => $stats,
        'filters' => $request->only(['search', 'topic'])
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // ဒီနေရာမှာ Delete Route အသစ်ထည့်ပါ
    Route::delete('/posts/{id}', [TikTokPostController::class, 'destroy'])->name('posts.destroy');
    // 👇 ဒီနှစ်ကြောင်းအသစ်ထည့်ပါ
    Route::get('/posts/edit/{id}', [TikTokPostController::class, 'edit'])->name('posts.edit');
    Route::post('/posts/{id}', [TikTokPostController::class, 'update'])->name('posts.update'); // Image ပါလို့ POST သုံးတာဖြစ်ပါတယ်
});

require __DIR__.'/auth.php';
