<?php
namespace App\Http\Middleware;

use App\Models\BannedIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user?->is_banned) {
            return $this->kick($request, 'သင့် account ကို ဆိုင်းငံ့ထားပါသည်။ Admin ကို ဆက်သွယ်ပါ');
        }

        $ip = $request->ip();
        $isBanned = cache()->remember("banned_ip:{$ip}", 300, fn () =>
            BannedIp::where('ip_address', $ip)->exists()
        );

        if ($isBanned) {
            return $this->kick($request, 'သင့် account ကို ဝင်ရောက်ခွင့် ပိတ်ပင်ထားပါသည်။ Admin ကို ဆက်သွယ်ပါ');
        }

        return $next($request);
    }

    private function kick(Request $request, string $message)
    {
        // Session ကို ချက်ချင်း invalidate — user ရဲ့ browser ဘက်က
        // request နောက်တစ်ခုမှာတင် login page ကို ပြန်ပို့ပါတယ်
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->header('X-Inertia')) {
            // Inertia က redirect status 303 လိုအပ်ပါတယ် (409 conflict-free)
            return redirect()->guest(route('login'))->with('error', $message);
        }

        return redirect()->route('login')->with('error', $message);
    }
}