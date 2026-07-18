<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\UsageLog;
use App\Models\PlanHistory;
use App\Models\PromoClaim;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlanService
{
    /**
     * User တစ်ယောက်ကို role/plan အသစ် assign လုပ်ပေးတယ်.
     * Admin manual renew ရော, system auto-grant (promo signup) ရော,
     * auto-rollback ရော — ဒီ function တစ်ခုတည်းကိုပဲ သုံးရမယ်.
     * (logic ၂ နေရာ ခွဲရေးရင် အရင် bug လို drift ပြန်ဖြစ်နိုင်လို့ပါ)
     */
    public static function grant(User $user, string $roleName, int $days, string $renewedBy = 'system'): void
    {
        $role       = Role::where('name', $roleName)->firstOrFail();
        $oldRole    = $user->role_name;
        $recapLimit = $days * $role->daily_limit;
        $expiresAt  = $days > 0 ? Carbon::now()->addDays($days) : null;

        DB::transaction(function () use ($user, $role, $oldRole, $recapLimit, $expiresAt, $renewedBy) {
            $user->update([
                'role_name'         => $role->name,
                'recap_limit'       => $recapLimit,
                'recap_limit_total' => $recapLimit,
                'plan_expires_at'   => $expiresAt,
            ]);

            // Role ပြောင်းတိုင်း ဒီနေ့ usage_log ကို fresh reset (carry-over bug ကို ကာကွယ်)
            UsageLog::where('user_id', $user->id)
                ->where('used_date', Carbon::today()->toDateString())
                ->delete();

            PlanHistory::create([
                'username'         => $user->username,
                'old_role'         => $oldRole,
                'new_role'         => $role->name,
                'recap_limit'      => $recapLimit,
                'renewed_at'       => Carbon::now(),
                'plan_expires_at'  => $expiresAt,
                'renewed_by'       => $renewedBy,
            ]);
        });
    }

    /**
     * Promo plan (quota ကုန်တာဖြစ်ဖြစ်, ရက်ကုန်တာဖြစ်ဖြစ်) ကို Tester ပြန်ချသွားတဲ့အခါ သုံးမယ်.
     */
    public static function rollbackToTester(User $user): void
    {
        self::grant($user, 'tester', 1, 'system-auto-rollback');
    }

    /**
     * Signup promo ကို user တစ်ယောက်ကို 1 email = 1 time ချည်းသာ ပေးဖို့.
     * `promo_claimed` column ကို guard အနေနဲ့ သုံးထားလို့ ဒီ function ကို
     * ဘယ်နေရာက ဘယ်နှခေါ်ခေါ် (double-click, retry, race condition) —
     * user တစ်ယောက်ကို တစ်ကြိမ်ထက်ပိုပြီး promo ဘယ်တော့မှ ရနိုင်မှာမဟုတ်ပါ.
     *
     * ထပ်ဖြည့်ထားတာက — Google account အများကြီးနဲ့ promo ထပ်ခံတာကို ကာကွယ်ဖို့
     * IP address / User-Agent fingerprint ကို secondary guard အနေနဲ့ သုံးထားပါတယ်.
     * (email/google_id အသစ်ပြန်ဖန်တီးလို့ရနေတာမို့ email guard တစ်ခုတည်းနဲ့ မလုံလောက်ပါ)
     *
     * @param  User    $user
     * @param  string  $ip          Request IP (controller ကနေ $request->ip() ပို့ပေးရမည်)
     * @param  string  $userAgent   Raw user-agent string ($request->userAgent())
     * @param  int     $windowDays  ဒီရက်အတွင်း claim မှတ်တမ်းကို ပြန်စစ်မည့် window
     * @param  int     $maxPerIp    window အတွင်း IP/UA တစ်ခုက claim ယူနိုင်မယ့် အများဆုံးအကြိမ်
     *
     * @return bool true = promo ရသွားပြီ, false = ရပြီးသား (သို့) abuse pattern ဖြစ်လို့ skip
     */
    public static function grantPromoOnce(User $user, string $ip, string $userAgent, int $windowDays = 30, int $maxPerIp = 1): bool
    {
        return DB::transaction(function () use ($user, $ip, $userAgent, $windowDays, $maxPerIp) {
            $locked = User::whereKey($user->id)->lockForUpdate()->first();

            if ($locked->promo_claimed) {
                return false;
            }

            $uaHash    = hash('sha256', $userAgent);
            $hasRealUa = trim($userAgent) !== '';

            $query = PromoClaim::where('ip_address', $ip)
                ->where('claimed_at', '>=', Carbon::now()->subDays($windowDays));

            if ($hasRealUa) {
                // UA header ရှိတဲ့ request — IP ရော UA ရော အတူတူဖြစ်မှသာ
                // "တစ်ယောက်တည်းက ပြန်လာနေတာ" လို့ယူဆ (fingerprint ပိုတိကျ)
                $query->where('ua_hash', $uaHash);
            }
            // UA မရှိရင် (webview/empty) — IP ချည်းသာ အားကိုးမယ်, ua_hash collision
            // ကြောင့် မတူတဲ့ user တွေ block မဖြစ်အောင်

            $recentClaims = $query->lockForUpdate()->count();

            if ($recentClaims >= $maxPerIp) {
                $locked->update(['promo_claimed' => true]);
                return false;
            }

            self::grant($locked, 'normal', 1, 'promo-signup');
            $locked->update(['promo_claimed' => true]);

            PromoClaim::create([
                'user_id'    => $locked->id,
                'ip_address' => $ip,
                'ua_hash'    => $uaHash,
                'claimed_at' => Carbon::now(),
            ]);

            return true;
        });
    }
}
