<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'google_id',
        'avatar',
        'role_name',
        'is_active',
        'recap_limit',
        'email_verified_at',
        'total_recap_used',
        'plan_expires_at',
        'session_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active'          => 'boolean',
            'plan_expires_at'    => 'datetime',
            'session_expires_at' => 'datetime',
            'email_verified_at'  => 'datetime',
            'password' => 'hashed',
        ];
    }


    // ── Relationships ──────────────────────────────
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_name', 'name');
    }
 
    public function usageLogs()
    {
        return $this->hasMany(UsageLog::class);
    }
 
    public function planHistories()
    {
        return $this->hasMany(PlanHistory::class, 'username', 'username');
    }
 
    // ── Helpers ────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role_name === 'admin';
    }
 
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }
    
}
