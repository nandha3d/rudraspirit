<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\PartnerDistributionShare;
use Illuminate\Support\Facades\Auth;

class PartnerDashboardController extends Controller
{
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
