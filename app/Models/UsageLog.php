<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageLog extends Model
{
    public $timestamps = false;

    protected $table = 'usage_log';

    protected $fillable = [
        'user_id',
        'used_date',
        'run_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}