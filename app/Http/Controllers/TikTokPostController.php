<?php

namespace App\Http\Controllers;

use App\Models\TikTokPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TikTokPostController extends Controller
{
    /**
     * Helper to get category/topic statistics
     */
    private function getTopicStats()
    {
        return TikTokPost::select('topic', DB::raw('count(*) as total'))
            ->whereNotNull('topic')
            ->groupBy('topic')
            ->get()
            ->map(function ($stat) {
                return [
                    'raw' => $stat->topic,
                    'clean' => str_replace('TOPICS_', '', $stat->topic),
                    'total' => $stat->total
                ];
            });
    }

    public function index()
    {
        $posts = TikTokPost::orderBy('created_at', 'desc')
            ->paginate(6) // 3-column layout grid friendly
            ->through(fn ($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'cover_title_burmese' => $post->cover_title_burmese,
                'content' => $post->content,
                'topic' => str_replace('TOPICS_', '', $post->topic),
                'image_path' => $post->image_path,
                'model_used' => $post->model_used,
                'created_at' => $post->created_at?->format('M d, Y') ?? 'Recent',
            ]);

        return Inertia::render('Home', [
            'posts' => $posts,
            'stats' => $this->getTopicStats(),
            'currentTopic' => null
        ]);
    }

    public function category($topic)
    {
        // Reconstruct the raw topic string if database prefixes it
        $rawTopic = str_starts_with($topic, 'TOPICS_') ? $topic : 'TOPICS_' . $topic;

        $posts = TikTokPost::where('topic', $rawTopic)
            ->orderBy('created_at', 'desc')
            ->paginate(6)
            ->through(fn ($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'cover_title_burmese' => $post->cover_title_burmese,
                'content' => $post->content,
                'topic' => str_replace('TOPICS_', '', $post->topic),
                'image_path' => $post->image_path,
                'model_used' => $post->model_used,
                'created_at' => $post->created_at?->format('M d, Y') ?? 'Recent',
            ]);

        return Inertia::render('Home', [
            'posts' => $posts,
            'stats' => $this->getTopicStats(),
            'currentTopic' => str_replace('TOPICS_', '', $rawTopic)
        ]);
    }

    public function show(TikTokPost $post)
    {
        return Inertia::render('Posts/Show', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'cover_title_burmese' => $post->cover_title_burmese,
                'content' => $post->content,
                'image_prompt' => $post->image_prompt,
                'b_roll_animation_suggestion' => $post->b_roll_animation_suggestion,
                'hashtags' => $post->hashtags ? explode(' ', $post->hashtags) : [],
                'topic' => str_replace('TOPICS_', '', $post->topic),
                'image_path' => $post->image_path,
                'model_used' => $post->model_used,
                'created_at' => $post->created_at?->format('F d, Y') ?? 'Recent',
            ]
        ]);
    }

    public function upload(Request $request)
    {
        // Token စစ်ဆေးခြင်း (Security အတွက်)
        $token = $request->bearerToken();
        if ($token !== 'ZAKERXA_SECURE_SECRET_TOKEN_HERE') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Validation စစ်ဆေးခြင်း
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // ပုံကို storage/app/public/posts ထဲသို့ သိမ်းဆည်းခြင်း
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public'); 
            // ဒါဆိုရင် $imagePath က "posts/filename.jpg" ဖြစ်သွားမယ်
            // Frontend မှာ ခေါ်ပြရင် asset('storage/' . $post->image_path) ဆိုပြီး ခေါ်သုံးရုံပါပဲ
        }

        // Database ထဲသို့ ပို့လိုက်သော Data များ သိမ်းဆည်းခြင်း
        $post = TikTokPost::create([
            'title' => $request->title,
            'cover_title_burmese' => $request->cover_title_burmese,
            'content' => $request->content,
            'topic' => $request->topic,
            'model_used' => $request->model_used,
            'b_roll_animation_suggestion' => $request->b_roll_animation_suggestion,
            'hashtags' => $request->hashtags, // Array ပြောင်းသိမ်းချင်ရင်လည်း အဆင်ပြေအောင် လုပ်ပါ
            'image_prompt' => $request->image_prompt,
            'image_path' => '/storage/' . $imagePath, // လမ်းကြောင်းအမှန် ထည့်ပေးလိုက်ခြင်း
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post and Image created successfully!',
            'data' => $post
        ], 201);
    }
}