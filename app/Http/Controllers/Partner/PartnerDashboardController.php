<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\PartnerDistributionShare;
use Illuminate\Support\Facades\Auth;

class PartnerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(feature_allowed('partner_share'), 404);
            return $next($request);
        });
    }

    public function index()
    {
        $partner = Auth::guard('partner')->user();

        $shares = PartnerDistributionShare::with('distribution')
            ->where('partner_id', $partner->id)
            ->get()
            ->sortByDesc(fn($s) => optional($s->distribution)->period_to);

        $totalEarned = (float) $shares->sum('amount');
        $totalPaid = (float) $shares->where('paid', true)->sum('amount');
        $totalPending = $totalEarned - $totalPaid;

        return view('partner.dashboard', compact('partner', 'shares', 'totalEarned', 'totalPaid', 'totalPending'));
    }
}
