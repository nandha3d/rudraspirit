<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicenseActivation;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total'      => License::count(),
            'active'     => License::where('status', 'active')
                ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now()))
                ->count(),
            'suspended'  => License::where('status', 'suspended')->count(),
            'revoked'    => License::where('status', 'revoked')->count(),
            'expired'    => License::whereNotNull('expires_at')->where('expires_at', '<=', Carbon::now())->count(),
            'activations' => LicenseActivation::count(),
        ];

        $recent = License::latest()->take(10)->get();

        $expiringSoon = License::whereNotNull('expires_at')
            ->whereBetween('expires_at', [Carbon::now(), Carbon::now()->addDays(30)])
            ->orderBy('expires_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent', 'expiringSoon'));
    }
}
