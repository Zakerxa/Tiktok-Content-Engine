<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServerStatusController extends Controller
{
    public function index()
    {
        // 1. Active server list ယူမယ်
        $servers = Server::where('is_active', true)
            ->orderBy('priority')
            ->get(['name', 'url', 'role_access', 'priority','is_active']);

        // 2. Processing job count တွေ group by server ယူမယ်
        $loadRows = DB::table('recap_jobs')
            ->select('server', DB::raw('COUNT(*) as processing_count'), DB::raw('MIN(created_at) as oldest_started'))
            ->where('status', 'processing')
            ->groupBy('server')
            ->get()
            ->keyBy('server');

        // 3. ပေါင်းစပ်ပြီး response ပြန်ပေးမယ်
        $result = $servers->map(function ($server) use ($loadRows) {
            $load = $loadRows->get($server->name);
            $processingCount = $load->processing_count ?? 0;
            $ageMinutes = $load ? Carbon::parse($load->oldest_started)->diffInMinutes(now()) : 0;

            return [
                'name'          => $server->name,
                'url'           => $server->url,
                'role_access'   => $server->role_access,   // null = all role သုံးလို့ရ
                'priority'      => $server->priority,
                'is_active'     => $server->is_active,
                'processing_count' => $processingCount,
                'is_busy'       => $processingCount > 0,
                'is_stuck'      => $ageMinutes > 45,
            ];
        });

        return response()->json($result);
    }
}