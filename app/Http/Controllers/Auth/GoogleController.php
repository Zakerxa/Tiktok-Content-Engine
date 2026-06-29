<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // Google OAuth redirect
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Google callback — user create or login
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Google login မအောင်မြင်ပါ။ ထပ်ကြိုးစားပါ။');
        }

        // Email နဲ့ google_id စစ်ပြီး user ရှာမည်
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // ရှိပြီးသား user — google_id မပါသေးရင် update
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            }

            if (!$user->email_verified_at) {
                $user->markEmailAsVerified();
            }
        } else {
            // အသစ် — tester role နဲ့ create
            $user = User::create([
                'username'  => $this->makeUsername($googleUser->getName()),
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'password'  => bcrypt(Str::random(32)),
                'role_name' => 'tester',
                'is_active' => true,
                'recap_limit' => 1,
                'email_verified_at' => now(),
            ]);
        }

        if (!$user->is_active) {
            return redirect()->route('login')
                ->with('error', 'Account ပိတ်ထားသည်။ Admin ကို ဆက်သွယ်ပါ။');
        }

        Auth::login($user, remember: true);

        return redirect()->route('dashboard')->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma'        => 'no-cache',
        ]);
    }

    // Google name → unique username
    private function makeUsername(string $name): string
    {
        $base = preg_replace('/[^a-zA-Z0-9]/', '', $name) ?: 'user';
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }
        return $username;
    }
}
