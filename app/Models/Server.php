<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = ['name', 'url', 'role_access', 'priority', 'is_active'];

    protected function casts(): array
    {
        return [
            'role_access' => 'array',
            'is_active'   => 'boolean',
        ];
    }
}