<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RollbackExpiredPlans extends Command
{
    /**
     * php artisan plans:rollback-expired
     */
    protected $signature = 'plans:rollback-expired';

    protected $description = 'Plan ကုန်သွားသော Pro/Normal user များကို Tester ဆီ auto-rollback လုပ်မည် (database.py _auto_rollback_expired() logic အတိုင်း)';

    public function handle()
    {
        $now = Carbon::now(); // Server/Laravel timezone (Asia/Yangon ဖြစ်အောင် config/app.php မှာ သတ်မှတ်ထားရမည်)

        // Pro သို့မဟုတ် Normal ဖြစ်ပြီး plan ကုန်သွားသော user အားလုံးကို ရှာမည်
        $expiredUsers = DB::table('users')
            ->whereIn('role_name', ['pro', 'normal'])
            ->whereNotNull('plan_expires_at')
            ->where('plan_expires_at', '<', $now)
            ->get(['id', 'username', 'role_name']);

        if ($expiredUsers->isEmpty()) {
            $this->info('Rollback လိုအပ်သော user မရှိပါ။');
            return;
        }

        foreach ($expiredUsers as $user) {
            DB::transaction(function () use ($user) {
                $oldRole = $user->role_name;

                // recap_limit=0 ဆိုရင် Admin panel/dashboard UI က "∞" (admin
                // unlimited sentinel) အနေနဲ့ ပြသနေတာမို့ Tester ကို 0 ပေးလို့
                // မသင့်ပါ — roles table ကနေ tester ရဲ့ daily_limit ကို dynamic ယူမည်
                $testerDailyLimit = DB::table('roles')
                    ->where('name', 'tester')
                    ->value('daily_limit') ?? 1;

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'role_name'       => 'tester',
                        'recap_limit'     => $testerDailyLimit,
                        'plan_expires_at' => null,
                    ]);

                DB::table('plan_history')->insert([
                    'username'    => $user->username,
                    'old_role'    => $oldRole,
                    'new_role'    => 'tester',
                    'recap_limit' => $testerDailyLimit,
                    'renewed_at'  => now(),
                    'renewed_by'  => 'system_auto_rollback',
                ]);
            });

            $this->info("Rollback: {$user->username} ({$user->role_name} → tester)");
        }

        $this->info(count($expiredUsers) . ' user(s) rollback ပြီးပါပြီ။');
    }
}