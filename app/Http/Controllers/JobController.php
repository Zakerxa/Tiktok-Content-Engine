<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class JobController extends Controller
{


    /**
     * GET /jobs/history
     * Returns paginated job history for the logged-in user, formatted for JobHistory.vue
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $jobs = DB::table('recap_jobs')
            ->leftJoin('servers', 'servers.name', '=', 'recap_jobs.server')
            ->where('recap_jobs.user_id', $user->id)
            ->orderByDesc('recap_jobs.created_at')
            ->select([
                'recap_jobs.id',
                'recap_jobs.status',
                'recap_jobs.error',
                'recap_jobs.created_at',
                'recap_jobs.updated_at',
                'recap_jobs.expires_at',
                'recap_jobs.completed_at',
                // Falls back to raw value (e.g. "ACER") if no match in servers table yet
                DB::raw('COALESCE(servers.name, recap_jobs.server) as server_name'),
            ])
            ->paginate(10)
            ->withQueryString()
            ->through(function ($job) {
                return [
                    'id'         => $job->id,
                    'status'     => $job->status,
                    'error'      => $job->status === 'failed' ? $job->error : null,
                    'created_at' => $job->created_at,
                    'expires_at' => $job->expires_at,
                    'is_expired' => $job->expires_at ? now()->greaterThan($job->expires_at) : false,
                    'started_at' => $job->created_at ? Carbon::parse($job->created_at)->diffForHumans() : null,
                    'duration'   => $this->formatDuration($job->created_at, $job->updated_at),
                ];
            });

        $todayUsed = DB::table('recap_jobs')
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        return Inertia::render('JobHistory', [
            'jobs'        => $jobs,
            'is_paid'     => $user->is_paid,        // adjust to your actual paid-status logic
            'role_name'   => $user->role?->name,    // adjust to your role relation
            'today_used'  => $todayUsed,
            'daily_limit' => $user->daily_limit ?? 0, // adjust to wherever the plan limit lives
        ]);
    }


    /**
     * GET /jobs/{jobId}/download
     * Resolves the job's server (v1/v2/v3...) to its real URL and redirects
     * the browser there to fetch the actual file.
     */
    public function download($jobId)
    {
        $job = DB::table('recap_jobs')->where('id', $jobId)->first();

        if (!$job) abort(404, 'Job not found');
        if ($job->user_id !== Auth::id()) abort(403);
        if ($job->status !== 'success') abort(404, 'Job not ready');
        if ($job->expires_at && now()->greaterThan($job->expires_at)) abort(410, 'Download link expired');

        $server = DB::table('servers')->where('name', $job->server)->first();
        if (!$server) abort(404, 'Server not found for this job');

        $downloadUrl = rtrim($server->url, '/') . '/download/' . $job->id;

        try {
            $upstream = Http::withHeaders([
                'X-Session-ID' => request()->header('X-Session-ID'),
            ])->withOptions(['stream' => true])->timeout(120)->get($downloadUrl);
        } catch (\Throwable $e) {
            return response()->json(['detail' => 'Upstream connection failed'], 502);
        }

        if (!$upstream->successful()) {
            // Pass status through; don't leak upstream body verbatim to logs-facing users,
            // but the frontend maps by status code anyway, not by this text.
            return response()->json(['detail' => 'Download failed'], $upstream->status());
        }

        $stream = $upstream->toPsrResponse()->getBody();

        return new StreamedResponse(function () use ($stream) {
            while (!$stream->eof()) {
                echo $stream->read(1024 * 512);
                flush();
            }
        }, 200, [
            'Content-Type'        => $upstream->header('Content-Type') ?: 'application/octet-stream',
            'Content-Disposition' => $upstream->header('Content-Disposition') ?: 'attachment; filename="Recap_Ready.mp4"',
            'Content-Length'      => $upstream->header('Content-Length'),
        ]);
    }

    /**
     * GET /jobs/status/{jobId}
     *
     * recap_jobs table ကနေ job status ကို DB တိုက်ရိုက်ဖတ်ပြီး frontend (Vue) ကို
     * per-step progress breakdown အနေနဲ့ ပြန်ပေးတယ်. Python servers (v1/v2/v3) ထဲက
     * ဘယ်ဟာက render လုပ်နေနေ, ဒီ endpoint ကတော့ DB ကိုပဲ မှီခိုလို့ အမြဲတမ်း လက်ရှိ
     * အခြေအနေကို ပြန်ပေးနိုင်တယ်.
     */
    public function status($jobId)
    {
        $job = DB::table('recap_jobs')
            ->where('id', $jobId)
            ->first();

        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        return response()->json([
            'step'         => (int)$job->step,
            'progress'     => $this->buildProgressBreakdown($job->step, $job->progress),
            'done'         => in_array($job->status, ['success', 'failed']),
            'error'        => $job->status === 'failed' ? $job->error : null,
            'download_url' => $jobId,
        ]);
    }

    /**
     * Formats elapsed time between two timestamps as "Xm Ys" (or "Xh Ym" for
     * longer jobs). Returns null if either timestamp is missing so the
     * frontend can show a dash / "Running..." for in-progress jobs.
     */
    private function formatDuration($start, $end)
    {
        if (!$start || !$end) return null;

        $seconds = Carbon::parse($start)->diffInSeconds(Carbon::parse($end));

        if ($seconds < 60) {
            return "{$seconds}s";
        }

        $minutes = intdiv($seconds, 60);
        $remSeconds = $seconds % 60;

        if ($minutes < 60) {
            return "{$minutes}m {$remSeconds}s";
        }

        $hours = intdiv($minutes, 60);
        $remMinutes = $minutes % 60;
        return "{$hours}h {$remMinutes}m";
    }

    /**
     * DB မှာ step (single number) + progress (single number) ပဲ ရှိလို့,
     * frontend လိုချင်တဲ့ {1:.., 2:.., 3:.., 4:.., 5:..} breakdown ပုံစံ
     * ပြန်တည်ဆောက်ပေးတာ.
     * - လက်ရှိ step ထက်ငယ်တဲ့ step တွေ -> ပြီးနှင့်ပြီးသားမို့ 100
     * - လက်ရှိ step             -> DB ထဲက progress value (live)
     * - လာမယ့် step တွေ          -> 0
     */
    private function buildProgressBreakdown($currentStep, $currentProgress)
    {
        $breakdown = [];
        for ($s = 1; $s <= 5; $s++) {
            if ($s < $currentStep) {
                $breakdown[(string) $s] = 100;
            } elseif ($s === (int) $currentStep) {
                $breakdown[(string) $s] = max(0, min(100, (int) $currentProgress));
            } else {
                $breakdown[(string) $s] = 0;
            }
        }
        return $breakdown;
    }
}