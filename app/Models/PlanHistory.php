<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanHistory extends Model
{
    const UPDATED_AT = null; // only renewed_at, no updated_at

    protected $table = 'plan_history';
    public $timestamps = false;
    protected $fillable = [
        'username',
        'old_role',
        'new_role',
        'recap_limit',
        'plan_expires_at',
        'renewed_by',
    ];
    protected $guarded = [];

    protected $casts = [
        'plan_expires_at' => 'datetime',
        'renewed_at'      => 'datetime',
    ];

    // renewed_at acts as created_at
    const CREATED_AT = 'renewed_at';

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
}
