<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'name';
    public    $incrementing = false;
    protected $keyType      = 'string';

    protected $fillable = [
        'name',
        'daily_limit',
        'max_video_seconds',
        'can_watermark',
        'can_subtitle',
        'can_voiceover',
    ];

    protected $casts = [
        'can_watermark' => 'boolean',
        'can_subtitle'  => 'boolean',
        'can_voiceover' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_name', 'name');
    }
}