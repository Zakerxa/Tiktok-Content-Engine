<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use App\Services\PlanService;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            $request->user()->update([
                'role_name' => 'tester',
                'is_active' => true,
            ]);

            // ★ Promo — Google login flow အတိုင်းပဲ, 1 email = 1 time ချည်း
            // ★ Promo — user အသစ်တိုင်းကို Normal role, 1 day အခမဲ့ ပေး (1 email = 1 time ချည်း)
            PlanService::grantPromoOnce(
                $request->user(),
                request()->ip(),
                request()->userAgent() ?? ''
            );
        }

        return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
    }
}
