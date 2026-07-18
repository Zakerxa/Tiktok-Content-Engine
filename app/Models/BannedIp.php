<?php

// app/Models/BannedIp.php
namespace App\Models;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class BannedIp extends Model
{
    protected $fillable = ['ip_address', 'user_id', 'reason'];

    public function unbanUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_banned' => false, 'banned_reason' => null]);

        return back()->with('success', "User #{$userId} unbanned.");
    }

    public function unbanIp(Request $request, $bannedIpId)
    {
        $banned = BannedIp::findOrFail($bannedIpId);
        cache()->forget("banned_ip:{$banned->ip_address}");
        $banned->delete();

        return back()->with('success', "IP {$banned->ip_address} unbanned.");
    }
}
