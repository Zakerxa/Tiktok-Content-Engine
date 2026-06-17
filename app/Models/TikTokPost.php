<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TikTokPost extends Model
{
    protected $table = 'tiktok_posts';

    public $timestamps = false;

    protected $fillable = [
        'title',
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
}
