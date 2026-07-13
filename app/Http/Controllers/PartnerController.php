<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerDistribution;
use App\Models\PartnerDistributionShare;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Partner profit-share admin (Plan C). Manage partners, run profit
 * distributions for a period, and mark payouts. Net profit reuses the same
 * calculation as the P&L (Plan A cost snapshot + Plan B expenses).
 */
class PartnerController extends Controller
{
    public function __construct()
    {
        // License gate: partner profit-share requires the 'partner_share' feature.
        $this->middleware(function ($request, $next) {
            abort_unless(feature_allowed('partner_share'), 404);
            return $next($request);
        });
    }

    private function demoBlocked(): bool
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('This action is disabled in demo mode'))->warning();
            return true;
        }
        return false;
    }

    /** Net business profit for a period (same formula as Profit & Loss). */
    public static function computeNetProfit(Carbon $from, Carbon $to): float
    {
        $sales = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where('orders.delivery_status', '!=', 'cancelled')
            ->selectRaw('
                COALESCE(SUM(order_details.price),0) as revenue,
                COALESCE(SUM(order_details.cost_price),0) as cogs,
                COALESCE(SUM(order_details.coupon_discount),0) as discount
            ')->first();

        $expenses = (float) DB::table('expenses')
            ->whereBetween('date', [$from, $to])
            ->sum(DB::raw('amount + tax'));

        return (float) $sales->revenue - (float) $sales->cogs - (float) $sales->discount - $expenses;
    }

    // ---------------- Partners ----------------
    public function index()
    {
        $partners = Partner::orderBy('name')->get();
        foreach ($partners as $p) {
            $p->total_earned = (float) PartnerDistributionShare::where('partner_id', $p->id)->sum('amount');
            $p->total_paid = (float) PartnerDistributionShare::where('partner_id', $p->id)->where('paid', 1)->sum('amount');
        }
        return view('backend.partners.index', compact('partners'));
    }

    public function store(Request $request)
    {
        if ($this->demoBlocked()) return back();
        $request->validate([
            'name' => 'required|max:191',
            'email' => 'required|email|unique:partners,email',
            'password' => 'required|min:6',
            'share_percent' => 'required|numeric|min:0|max:100',
        ]);
        Partner::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'share_percent' => $request->share_percent,
            'status' => 'active',
            'note' => $request->note,
        ]);
        flash(translate('Partner added'))->success();
        return back();
    }

    public function update(Request $request, $id)
    {
        if ($this->demoBlocked()) return back();
        $partner = Partner::findOrFail($id);
        $request->validate([
            'name' => 'required|max:191',
            'email' => 'required|email|unique:partners,email,' . $partner->id,
            'share_percent' => 'required|numeric|min:0|max:100',
        ]);
        $partner->name = $request->name;
        $partner->email = $request->email;
        $partner->phone = $request->phone;
        $partner->share_percent = $request->share_percent;
        $partner->status = $request->status ?: 'active';
        $partner->note = $request->note;
        if ($request->filled('password')) {
            $partner->password = Hash::make($request->password);
        }
        $partner->save();
        flash(translate('Partner updated'))->success();
        return back();
    }

    public function destroy($id)
    {
        if ($this->demoBlocked()) return back();
        Partner::destroy($id);
        flash(translate('Partner deleted'))->success();
        return back();
    }

    // ---------------- Distributions ----------------
    public function distributions()
    {
        $distributions = PartnerDistribution::withCount('shares')->orderByDesc('period_to')->paginate(20);
        return view('backend.partners.distributions', compact('distributions'));
    }

    public function run_distribution(Request $request)
    {
        if ($this->demoBlocked()) return back();
        $request->validate(['from' => 'required', 'to' => 'required']);
        $from = Carbon::parse($request->from)->startOfDay();
        $to = Carbon::parse($request->to)->endOfDay();

        $netProfit = self::computeNetProfit($from, $to);

        $distribution = PartnerDistribution::create([
            'period_from' => $from->toDateString(),
            'period_to' => $to->toDateString(),
            'net_profit' => $netProfit,
            'status' => 'draft',
            'note' => $request->note,
        ]);

        foreach (Partner::where('status', 'active')->get() as $partner) {
            PartnerDistributionShare::create([
                'partner_distribution_id' => $distribution->id,
                'partner_id' => $partner->id,
                'share_percent' => $partner->share_percent,
                'amount' => round($netProfit * $partner->share_percent / 100, 2),
                'paid' => false,
            ]);
        }

        flash(translate('Distribution created'))->success();
        return redirect()->route('partners.distribution.show', $distribution->id);
    }

    public function show_distribution($id)
    {
        $distribution = PartnerDistribution::with('shares.partner')->findOrFail($id);
        return view('backend.partners.distribution_show', compact('distribution'));
    }

    public function mark_paid(Request $request, $shareId)
    {
        if ($this->demoBlocked()) return back();
        $share = PartnerDistributionShare::findOrFail($shareId);
        $share->paid = !$share->paid;
        $share->paid_at = $share->paid ? now() : null;
        $share->method = $request->method;
        $share->reference = $request->reference;
        $share->save();
        flash(translate('Payout status updated'))->success();
        return back();
    }

    public function destroy_distribution($id)
    {
        if ($this->demoBlocked()) return back();
        PartnerDistributionShare::where('partner_distribution_id', $id)->delete();
        PartnerDistribution::destroy($id);
        flash(translate('Distribution deleted'))->success();
        return redirect()->route('partners.distributions');
    }
}
