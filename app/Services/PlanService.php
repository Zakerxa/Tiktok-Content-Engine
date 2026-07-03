<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\UsageLog;
use App\Models\PlanHistory;
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
     * @return bool true = promo ရသွားပြီ, false = ရပြီးသားဖြစ်လို့ skip လုပ်လိုက်
     */
    public static function grantPromoOnce(User $user): bool
    {
        if ($user->promo_claimed) {
            return false;
        }

        self::grant($user, 'normal', 1, 'promo-signup');
        $user->update(['promo_claimed' => true]);

        return true;
    }
}