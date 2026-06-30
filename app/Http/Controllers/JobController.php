<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
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