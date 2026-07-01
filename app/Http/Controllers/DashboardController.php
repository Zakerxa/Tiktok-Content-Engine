<?php

namespace App\Http\Controllers;

use App\Models\TikTokPost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $today  = Carbon::today()->toDateString();
        $query = TikTokPost::query()->where('user_id', $userId);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('cover_title_burmese', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('topic')) {
            $rawTopic = str_starts_with($request->topic, 'TOPICS_') ? $request->topic : 'TOPICS_' . $request->topic;
            $query->where('topic', $rawTopic);
        }

        $posts = $query->orderBy('id', 'desc')
            ->paginate(6)
            ->withQueryString()
            ->through(fn($post) => [
                'id'                  => $post->id,
                'title'               => $post->title,
                'slug'                => $post->slug,
                'cover_title_burmese' => $post->cover_title_burmese,
                'content'             => $post->content,
                'topic'               => str_replace('TOPICS_', '', $post->topic),
                'image_path'          => $post->image_path,
                'model_used'          => $post->model_used,
                'created_at'          => $post->created_at?->format('M d, Y') ?? 'Recent',
            ]);

        $stats = TikTokPost::select('topic', DB::raw('count(*) as total'))
            ->where('user_id', $userId)
            ->whereNotNull('topic')
            ->groupBy('topic')
            ->get()
            ->map(fn($stat) => [
                'clean' => str_replace('TOPICS_', '', $stat->topic),
                'total' => $stat->total,
            ]);

        $jobs = DB::table('recap_jobs')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate(5, ['*'], 'jobs_page')
            ->withQueryString()
            ->through(fn($job) => [
                'id'         => $job->id,
                'status'     => $job->status,
                'step'       => $job->step,
                'progress'   => (int) $job->progress,
                'error'      => $job->status === 'failed' ? $job->error : null,
                'started_at' => $job->created_at ? Carbon::parse($job->created_at)->format('M d, Y') : null,
                'duration'   => $this->formatDuration($job->created_at, $job->updated_at),
            ]);

        // ✅ ဒီနေ့ တကယ်သုံးထားတဲ့ ဂဏန်း (usage_log — daily reset)
        $todayUsed = DB::table('usage_log')
            ->where('user_id', $userId)
            ->where('used_date', $today)
            ->value('run_count') ?? 0;

        // ✅ Role ရဲ့ fixed daily cap (roles table)
        $dailyLimit = DB::table('roles')
            ->where('name', $request->user()->role_name)
            ->value('daily_limit') ?? 0;

        return Inertia::render('Dashboard', [
            'posts'   => $posts,
            'stats'   => $stats,
            'jobs'    => $jobs,
            'filters' => $request->only(['search', 'topic']),
            'user'    => [
                'username'         => $request->user()->username,
                'email'            => $request->user()->email,
                'avatar'           => $request->user()->avatar,
                'role_name'        => $request->user()->role_name,
                'daily_limit'      => $dailyLimit,     // ← အသစ်ထည့်
                'today_used'       => $todayUsed,
                'recap_limit'      => $request->user()->recap_limit,
                'total_recap_used' => $request->user()->total_recap_used,
                'plan_expires_at'  => $request->user()->plan_expires_at,
                'is_active'        => $request->user()->is_active,
            ],
        ]);
    }

    public function dashboardRecap(Request $request)
    {
        $userId = $request->user()->id;
        $today  = Carbon::today()->toDateString();
        // ✅ ဒီနေ့ တကယ်သုံးထားတဲ့ ဂဏန်း (usage_log — daily reset)
        $todayUsed = DB::table('usage_log')
            ->where('user_id', $userId)
            ->where('used_date', $today)
            ->value('run_count') ?? 0;

        // ✅ Role ရဲ့ fixed daily cap (roles table)
        $dailyLimit = DB::table('roles')
            ->where('name', $request->user()->role_name)
            ->value('daily_limit') ?? 0;

        return Inertia::render('MovieRecap/DashboardRecap', [
            'todayUsed' => $todayUsed,
            'dailyLimit' => $dailyLimit,
        ]);
    }

    public function homeRecap(Request $request)
    {
        $userId = $request->user()->id;
        $today  = Carbon::today()->toDateString();
        // ✅ ဒီနေ့ တကယ်သုံးထားတဲ့ ဂဏန်း (usage_log — daily reset)
        $todayUsed = DB::table('usage_log')
            ->where('user_id', $userId)
            ->where('used_date', $today)
            ->value('run_count') ?? 0;

        // ✅ Role ရဲ့ fixed daily cap (roles table)
        $dailyLimit = DB::table('roles')
            ->where('name', $request->user()->role_name)
            ->value('daily_limit') ?? 0;

        return Inertia::render('MovieRecap/Show', [
            'todayUsed' => $todayUsed,
            'dailyLimit' => $dailyLimit,
        ]);
    }

    private function formatDuration(?string $start, ?string $end): ?string
    {
        if (!$start || !$end) {
            return null;
        }

        $startAt = Carbon::parse($start);
        $endAt   = Carbon::parse($end);
        $seconds = abs($endAt->diffInSeconds($startAt));   // ← abs() ထည့်လိုက်တယ်

        if ($seconds < 60) {
            return $seconds . 's';
        }

        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes < 60) {
            return $remainingSeconds > 0
                ? "{$minutes}m {$remainingSeconds}s"
                : "{$minutes}m";
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return $remainingMinutes > 0
            ? "{$hours}h {$remainingMinutes}m"
            : "{$hours}h";
    }
}
