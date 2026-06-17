<?php

namespace Database\Seeders;

use App\Models\TikTokPost;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TikTokPostSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $posts = [
            [
                'title' => 'AI-Powered Video Prompt for Fashion Drops',
                'cover_title_burmese' => 'အဝတ်အထည်ဒီဇိုင်းနဲ့ Viral TikTok',
                'content' => 'Generate a short looping video featuring modelled streetwear, rapid transitions between outfits, and energetic beats for a trending fashion drop.',
                'image_prompt' => 'futuristic streetwear photoshoot with neon accents, motion blur, and high contrast lighting',
                'b_roll_animation_suggestion' => 'Add quick spin transitions with lens flares to emphasize outfit changes.',
                'hashtags' => '#fashion #tiktokstyle #aioutfit #trendalert',
                'topic' => 'Fashion',
                'model_used' => 'GPT-4.1',
                'image_path' => 'https://images.pexels.com/photos/2983464/pexels-photo-2983464.jpeg',
                'created_at' => now()->subDays(1),
            ],
            [
                'title' => 'Burmese Food TikTok Recipe Hook',
                'cover_title_burmese' => 'မြန်မာအစားအသောက် TikTok အတွက် Recipe Hook',
                'content' => 'Showcase a step-by-step quick recipe with close-ups of sizzling ingredients, a final plated dish, and text overlays to drive engagement.',
                'image_prompt' => 'close-up of delicious Burmese street food with vibrant spices and steam rising',
                'b_roll_animation_suggestion' => 'Use stop-motion style ingredient reveals and slow-motion sauce pouring.',
                'hashtags' => '#foodtok #burmesecuisine #recipevideo #viralrecipe',
                'topic' => 'Food',
                'model_used' => 'GPT-4.1',
                'image_path' => 'https://images.pexels.com/photos/1437267/pexels-photo-1437267.jpeg',
                'created_at' => now()->subDays(3),
            ],
            [
                'title' => 'Quick Productivity Tips for Remote Workers',
                'cover_title_burmese' => 'အလုပ်ကနေ အိမ်မှ ကြိုးစားနိုင်ဖို့ Productivity Tips',
                'content' => 'Create a fast-paced listicle video with animated text callouts, workstation shots, and a strong CTA to save the post.',
                'image_prompt' => 'minimalist home office setup with a laptop, coffee, and productivity planner',
                'b_roll_animation_suggestion' => 'Insert floating text bubbles over each workspace clip to highlight productivity tips.',
                'hashtags' => '#productivity #remotejobs #workfromhome #tiktoktips',
                'topic' => 'Lifestyle',
                'model_used' => 'GPT-4.1',
                'image_path' => 'https://images.pexels.com/photos/374074/pexels-photo-374074.jpeg',
                'created_at' => now()->subDays(5),
            ],
            [
                'title' => 'Short Tech Review Script for Viral Clips',
                'cover_title_burmese' => 'နည်းပညာ Review TikTok ဗီဒီယိုဖန်တီးမှု',
                'content' => 'Highlight a product’s top three features, include a quick comparison overlay, and end with a swipe-up call to action.',
                'image_prompt' => 'sleek tech gadget review set with glowing peripherals and modern studio lighting',
                'b_roll_animation_suggestion' => 'Use kinetic text animations paired with close-up product shots.',
                'hashtags' => '#techreview #gadgettok #viraltech #shorts',
                'topic' => 'Tech',
                'model_used' => 'GPT-4.1',
                'image_path' => 'https://images.pexels.com/photos/3861972/pexels-photo-3861972.jpeg',
                'created_at' => now()->subDays(2),
            ],
            [
                'title' => 'Beauty Routine Reel Idea',
                'cover_title_burmese' => 'အလှအပ Routine နဲ့ TikTok Reels',
                'content' => 'Film a morning beauty routine with close-ups of skincare products, text tips, and a soft aesthetic filter.',
                'image_prompt' => 'luxury skincare flat lay with soft pastel colors and gentle lighting',
                'b_roll_animation_suggestion' => 'Layer subtle shimmer animations over product reveal shots.',
                'hashtags' => '#beautytok #skincareroutine #reelsoftheday #glowup',
                'topic' => 'Beauty',
                'model_used' => 'GPT-4.1',
                'image_path' => 'https://images.pexels.com/photos/374623/pexels-photo-374623.jpeg',
                'created_at' => now()->subDays(4),
            ],
        ];

        foreach ($posts as $post) {
            TikTokPost::create($post);
        }
    }
}
