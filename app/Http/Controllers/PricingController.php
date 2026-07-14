<?php

namespace App\Http\Controllers;

use App\Models\Role;

class PricingController extends Controller
{
    public function index()
    {
        $roles = Role::where('show_in_pricing', true)
        ->orderBy('sort_order')
        ->get();

        $plans = $roles->map(function (Role $role) {
            return [
                'name'     => $role->name,
                'price'    => $role->price,
                'tagline'  => $role->tagline,
                'features' => $this->buildFeatures($role),
            ];
        });

        return response()->json($plans);
    }

    private function buildFeatures(Role $role): array
    {
        $minutes = intdiv($role->max_video_seconds, 60);
        $seconds = $role->max_video_seconds % 60;
        $durationLabel = $seconds > 0 ? "{$minutes} min {$seconds}s" : "{$minutes} min";

        return [
            ['label' => "{$role->daily_limit} generation" . ($role->daily_limit > 1 ? 's' : '') . '/day', 'included' => true, 'tooltip' => 'Upgrade for more generation'],
            ['label' => "Auto Subtitles (+{$role->subtitle_limit})", 'included' => $role->can_subtitle],
            ['label' => "AI Voice Over (+{$role->voiceover_limit})", 'included' => $role->can_voiceover, 'tooltip' => 'Upgrade for more AI Voice'],
            ['label' => 'Custom Blur & Mosaic', 'included' => true],
            ['label' => "{$durationLabel} Video Max", 'included' => true, 'tooltip' => 'Upgrade for longer durations'],
            ['label' => "Copyright Protection ({$role->copyright_protection}%)", 'included' => true, 'type' => $role->copyright_protection < 50 ? 'warning' : null, 'tooltip' => 'Upgrade for longer durations'],
            ['label' => ucfirst($role->video_quality) . ' quality export', 'included' => true, 'type' => $role->video_quality === 'low' ? 'warning' : null],
            ['label' => ucfirst($role->processing_speed) . ' processing', 'included' => true, 'type' => $role->processing_speed === 'low' ? 'warning' : null],
            ['label' => 'Custom Logo', 'included' => $role->can_watermark],
        ];
    }
}