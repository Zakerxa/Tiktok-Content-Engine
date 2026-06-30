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
            ->through(fn ($post) => [
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
            ->map(fn ($stat) => [
                'clean' => str_replace('TOPICS_', '', $stat->topic),
                'total' => $stat->total,
            ]);

        $jobs = DB::table('recap_jobs')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate(5, ['*'], 'jobs_page')
            ->withQueryString()
            ->through(fn ($job) => [
                'id'         => $job->id,
                'status'     => $job->status,
                'step'       => $job->step,
                'progress'   => (int) $job->progress,
                'error'      => $job->status === 'failed' ? $job->error : null,
                'started_at' => $job->created_at ? Carbon::parse($job->created_at)->format('M d, Y h:i A') : null,
                'duration'   => $this->formatDuration($job->created_at, $job->updated_at),
            ]);

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
                'recap_limit'      => $request->user()->recap_limit,
                'total_recap_used' => $request->user()->total_recap_used,
                'plan_expires_at'  => $request->user()->plan_expires_at,
                'is_active'        => $request->user()->is_active,
            ],
        ]);
    }

    private function formatDuration(?string $start, ?string $end): ?string
    {
        if (!$start || !$end) {
            return null;
        }

        $startAt = Carbon::parse($start);
        $endAt   = Carbon::parse($end);
        $seconds = $endAt->diffInSeconds($startAt);

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