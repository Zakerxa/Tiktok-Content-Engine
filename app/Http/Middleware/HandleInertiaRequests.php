<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id'               => $request->user()->id,
                    'username'         => $request->user()->username,
                    'email'            => $request->user()->email,
                    'avatar'           => $request->user()->avatar,
                    'role_name'        => $request->user()->role_name,
                    'recap_limit'      => $request->user()->recap_limit,
                    'total_recap_used' => $request->user()->total_recap_used,
                    'plan_expires_at'  => $request->user()->plan_expires_at,
                    'is_active'        => $request->user()->is_active,
                    'google_id'        => $request->user()->google_id ? True : False,
                    'email_verified_at'=> $request->user()->email_verified_at ? True : False
                ] : null,
            ],
            'flash' => ['error' => fn () => $request->session()->get('error')],
        ];
    }
}
