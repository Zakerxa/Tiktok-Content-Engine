<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Email လိပ်စာအတည်ပြုပါ - ' . config('app.name'))
                ->view('emails.verify-email', [
                    'url' => $url,
                ]);
        });

        RateLimiter::for('downloads', function (Request $request) {
            return Limit::perDay(2)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'ဒီနေ့အတွက် Download limit (2 ကြိမ်) ပြည့်သွားပါပြီ။ မနက်ဖြန် ထပ်ကြိုးစားပါ။'
                    ], 429);
                });
        });
    }
}
