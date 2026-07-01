<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServerController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Servers/Index', [
            'servers' => Server::orderBy('priority')->get(),
        ]);
    }

    public function store(Request $request)
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

    public function update(Request $request, Server $server)
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

    public function destroy(Server $server)
    {
        $server->delete();
        return back()->with('success', 'Server ဖျက်ပြီးပါပြီ။');
    }
}