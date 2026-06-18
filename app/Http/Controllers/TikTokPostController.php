<?php

namespace App\Http\Controllers;

use App\Models\TikTokPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

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
            ->orderBy('id', 'desc')
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
                'slug' => $post->slug,
                'cover_title_burmese' => $post->cover_title_burmese,
                'content' => $post->content,
                'image_prompt' => $post->image_prompt,
                'b_roll_animation_suggestion' => $post->b_roll_animation_suggestion,
                'hashtags' => $post->hashtags ? explode(' ', $post->hashtags) : [],
                'topic' => str_replace('TOPICS_', '', $post->topic),
                'image_path' => $post->image_path,
                'model_used' => $post->model_used,
                'created_at' => $post->created_at?->format('F d, Y') ?? 'Recent'
            ]
        ]);
    }

    // TikTokPostController.php ထဲတွင် ထည့်ရန်

    public function edit(int $id)
    {
        $post = TikTokPost::findOrFail($id);

        return Inertia::render('Posts/Edit', [
            'post' => $post
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = TikTokPost::findOrFail($id);

        // Validation စစ်ခြင်း
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB Max
        ]);

        // ပြင်ဆင်မည့် Data များကို စုစည်းခြင်း
        $data = [
            'title' => $request->title,
            'slug' => $request->slug,
            'cover_title_burmese' => $request->cover_title_burmese,
            'content' => $request->content,
            'topic' => $request->topic,
            'model_used' => $request->model_used,
            'b_roll_animation_suggestion' => $request->b_roll_animation_suggestion,
            'hashtags' => $request->hashtags,
            'image_prompt' => $request->image_prompt,
        ];

        // ပုံအသစ်ပါလာခဲ့လျှင် ပုံအဟောင်းကိုဖျက်ပြီး ပုံအသစ်လဲမည်
        if ($request->hasFile('image')) {
            if ($post->image_path) {
                $oldImage = str_replace('/storage/', '', $post->image_path);
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $imagePath = $request->file('image')->store('images', 'public');
            $data['image_path'] = '/storage/' . $imagePath;
        }

        $post->update($data);

        return redirect()->route('dashboard')->with('success', 'Post updated successfully!');
    }

    public function destroy($id)
    {
        $post = TikTokPost::findOrFail($id);

        // ၁။ Database ထဲမှာ ပုံလမ်းကြောင်း (image_path) ရှိမရှိ စစ်ဆေးခြင်း
        if ($post->image_path) {
            // Upload လုပ်တုန်းက '/storage/images/...' လို့ သိမ်းထားတဲ့အတွက် 
            // တကယ်ဖျက်မယ့်အခါ ရှေ့က '/storage/' ကို ဖယ်ပြီးမှ ဖျက်ရပါတယ်။
            $imageName = str_replace('/storage/', '', $post->image_path);

            // Storage ထဲမှာ တကယ်ရှိမရှိစစ်ပြီးမှ ဖျက်ပါမယ်
            if (Storage::disk('public')->exists($imageName)) {
                Storage::disk('public')->delete($imageName);
            }
        }

        // ၂။ Database ထဲက Record ကို ဖျက်ခြင်း
        $post->delete();

        // API ကနေ လှမ်းဖျက်တာဆိုရင် JSON ပြန်ပေးပါ
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Post and image deleted successfully'
            ]);
        }

        // Web (Vue) ကနေ ဖျက်တာဆိုရင် Home (သို့) Dashboard ကို redirect ပြန်လုပ်ပါ
        return redirect()->back();
    }

    public function checkStatus($slug)
    {
        // Database ထဲမှာ အဲဒီ slug နဲ့ Post ရှိ/မရှိ စစ်ဆေးပါမယ်
        $exists = TikTokPost::where('slug', $slug)->exists();
        return response()->json([
            'exists' => $exists
        ]);
    }

    public function upload(Request $request)
    {
        // 1. Token စစ်ဆေးခြင်း (Security အတွက်)
        $token = $request->bearerToken();
        if ($token !== 'ZAKERXA_SECURE_SECRET_TOKEN_HERE') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // 2. Validation တစ်ခါတည်း စစ်ဆေးခြင်း
        $request->validate([
            'title' => 'required|string',
            'slug' => 'required|string|unique:tiktok_posts,slug',
            'cover_title_burmese' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB
            'image_prompt' => 'nullable|string',
            'b_roll_animation_suggestion' => 'nullable|string',
            'hashtags' => 'nullable|string',
            'topic' => 'nullable|string',
            'model_used' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $post = TikTokPost::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'cover_title_burmese' => $request->cover_title_burmese,
            'content' => $request->content,
            'topic' => $request->topic,
            'model_used' => $request->model_used,
            'b_roll_animation_suggestion' => $request->b_roll_animation_suggestion,
            'hashtags' => $request->hashtags,
            'image_prompt' => $request->image_prompt,
            'image_path' => $imagePath ? '/storage/' . $imagePath : null,
        ]);

        // 5. Response ပြန်ပို့ခြင်း
        return response()->json([
            'success' => true,
            'message' => 'Post and Image created successfully!',
            'post' => $post // main.sh ထဲက '.post.image_path' နဲ့ ကိုက်ညီအောင် 'post' ဟု ပြင်ထားပါသည်
        ], 201);
    }
}
