<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoClaim extends Model
{
    protected $fillable = ['user_id', 'ip_address', 'ua_hash', 'claimed_at'];

    protected $casts = [
        'claimed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
