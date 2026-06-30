<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TikTokPost extends Model
{
    protected $table = 'tiktok_posts';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'slug',
        'cover_title_burmese',
        'content',
        'image_prompt',
        'b_roll_animation_suggestion',
        'hashtags',
        'topic',
        'model_used',
        'image_path',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $appends = [
        'created_at_human',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * "2 hours ago", "3 days ago" စသည်ဖြင့် ပြလို့ရတဲ့ relative time string.
     * Frontend (Vue) ကို JSON response ရဲ့ created_at_human field ထဲက တိုက်ရိုက်ယူပြီး
     * ပြလို့ရအောင် accessor အနေနဲ့ ထည့်ပေးထားတယ်.
     */
    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at?->diffForHumans();
    }
}