<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UsageLog;
use App\Models\PlanHistory;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminController extends Controller
{
    // ─────────────────────────────────────────────
    // GET /admin  — Dashboard stats
    // ─────────────────────────────────────────────
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $stats = [
            'total_users'        => User::count(),
            'active_users'       => User::where('is_active', 1)->count(),
            'disabled_users'     => User::where('is_active', 0)->count(),
            'total_recaps_today' => UsageLog::where('used_date', $today)->sum('run_count'),
            'users_by_role'      => User::select('role_name', DB::raw('count(*) as count'))
                ->groupBy('role_name')
                ->pluck('count', 'role_name'),
            'new_users_today'    => User::whereDate('created_at', $today)->count(),
        ];

        return Inertia::render('Admin/Index', compact('stats'));
    }

    // ─────────────────────────────────────────────
    // GET /admin/users  — User list with usage data
    // ─────────────────────────────────────────────
    public function users(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $query = User::select(
            'users.*',
            DB::raw("COALESCE((SELECT run_count FROM usage_log WHERE user_id = users.id AND used_date = '{$today}'), 0) as today_used"),
            'roles.daily_limit'
        )
            ->leftJoin('roles', 'users.role_name', '=', 'roles.name')
            ->orderBy('users.created_at', 'desc');

        // Optional search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('users.username', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        // Optional role filter
        if ($role = $request->input('role')) {
            $query->where('users.role_name', $role);
        }

        $users = $query->paginate(20)->withQueryString();

        $roles = Role::all();

        return Inertia::render('Admin/Users', [
            'users'   => $users,
            'roles'   => $roles,
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    // ─────────────────────────────────────────────
    // POST /admin/users/{id}/renew  — Renew plan
    // ─────────────────────────────────────────────
    public function renew(Request $request, $id)
    {
        $request->validate([
            'new_role' => 'required|in:normal,pro,vip',
            'days'     => 'required|integer|min:0',
        ]);

        $user    = User::findOrFail($id);
        $role    = Role::where('name', $request->new_role)->firstOrFail();
        $days    = (int) $request->days;
        $oldRole = $user->role_name;

        // Mirror FastAPI admin_renew_plan() logic:
        // recap_limit = days × daily_limit
        // VIP with days=0 → expires = NULL
        $recapLimit  = $days * $role->daily_limit;
        $expiresAt   = $days > 0 ? Carbon::now()->addDays($days) : null;

        DB::transaction(function () use ($user, $role, $oldRole, $recapLimit, $expiresAt, $request) {
            $user->update([
                'role_name'       => $role->name,
                'recap_limit'     => $recapLimit,
                'plan_expires_at' => $expiresAt,
            ]);

            PlanHistory::create([
                'username'         => $user->username,
                'old_role'       => $oldRole,
                'new_role'       => $role->name,
                'recap_limit'     => $recapLimit,
                'renewed_at'      => Carbon::now(),
                'plan_expires_at' => $expiresAt,
                'renewed_by'      => Auth::user()->username,
            ]);
        });

        return back()->with('success', "Plan renewed for {$user->username}.");
    }

    // ─────────────────────────────────────────────
    // POST /admin/users/{id}/toggle  — Enable/disable
    // ─────────────────────────────────────────────
    public function toggleActive($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from disabling themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot disable your own account.');
        }

        $user->update(['is_active' => $user->is_active ? 0 : 1]);

        $state = $user->is_active ? 'enabled' : 'disabled';

        return back()->with('success', "User {$user->username} has been {$state}.");
    }

    // ─────────────────────────────────────────────
    // POST /admin/users/{id}/password  — Reset password
    // ─────────────────────────────────────────────
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Password reset for {$user->username}.");
    }

    // ─────────────────────────────────────────────
    // DELETE /admin/users/{id}  — Delete user
    // ─────────────────────────────────────────────
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $username = $user->username;

        DB::transaction(function () use ($user) {
            UsageLog::where('user_id', $user->id)->delete();
            $user->delete();
        });

        return back()->with('success', "User {$username} has been deleted.");
    }

    // ─────────────────────────────────────────────
    // GET /admin/roles  — Role list
    // ─────────────────────────────────────────────
    public function roles()
    {
        $roles = Role::orderBy('daily_limit')->get();

        return Inertia::render('Admin/Roles', compact('roles'));
    }

    // ─────────────────────────────────────────────
    // POST /admin/roles/{name}  — Update role settings
    // ─────────────────────────────────────────────
    public function updateRole(Request $request, $name)
    {
        // Protect admin role from edits
        if ($name === 'admin') {
            return back()->with('error', 'Admin role cannot be modified.');
        }

        $request->validate([
            'daily_limit'       => 'required|integer|min:0',
            'max_video_seconds' => 'required|integer|min:0',
            'can_watermark'     => 'required|boolean',
            'can_subtitle'      => 'required|boolean',
            'can_voiceover'     => 'required|boolean',
        ]);

        $role = Role::where('name', $name)->firstOrFail();

        $role->update($request->only([
            'daily_limit',
            'max_video_seconds',
            'can_watermark',
            'can_subtitle',
            'can_voiceover',
        ]));

        return back()->with('success', "Role [{$name}] updated.");
    }

    // ─────────────────────────────────────────────
    // GET /admin/history/{username}  — Plan history
    // ─────────────────────────────────────────────
    public function history($username)
    {
        // Verify user exists
        $user = User::where('username', $username)->firstOrFail();

        $history = PlanHistory::where('username', $user->username)
            ->orderBy('renewed_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/History', [
            'user'    => $user,
            'history' => $history,
        ]);
    }






    //////////////////////////// SERVER MANAGEMENT   ////////////////////////////////

    public function servers()
    {
        $servers = Server::orderBy('priority')->get();

        // Busy/stuck status ပေါင်းထည့်မယ်
        $loadRows = DB::table('recap_jobs')
            ->select('server', DB::raw('COUNT(*) as processing_count'), DB::raw('MIN(created_at) as oldest_started'))
            ->where('status', 'processing')
            ->groupBy('server')
            ->get()
            ->keyBy('server');

        $servers = $servers->map(function ($server) use ($loadRows) {
            $load = $loadRows->get($server->name);
            $processingCount = $load->processing_count ?? 0;
            $ageMinutes = $load ? Carbon::parse($load->oldest_started)->diffInMinutes(now()) : 0;

            $server->processing_count = $processingCount;
            $server->is_busy = $processingCount > 0;
            $server->is_stuck = $ageMinutes > 30;

            return $server;
        });

        return Inertia::render('Admin/Servers', [
            'servers' => $servers,
        ]);
    }

    public function storeServer(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|unique:servers,name',
            'url'         => 'required|url',
            'role_access' => 'nullable|array',
            'priority'    => 'required|integer',
        ]);

        Server::create($request->all());

        return back()->with('success', 'Server ထည့်ပြီးပါပြီ။');
    }

    public function updateServer(Request $request, Server $server)
    {
        $request->validate([
            'name'        => 'required|string|unique:servers,name,' . $server->id,
            'url'         => 'required|url',
            'role_access' => 'nullable|array',
            'priority'    => 'required|integer',
            'is_active'   => 'boolean',
        ]);

        $server->update($request->all());

        return back()->with('success', 'Server update ပြီးပါပြီ။');
    }

    public function deleteServer(Server $server)
    {
        $server->delete();
        return back()->with('success', 'Server ဖျက်ပြီးပါပြီ။');
    }
}
