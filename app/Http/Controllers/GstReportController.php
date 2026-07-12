<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * GST reports (Plan D). Read-only aggregates over the GST columns already
 * captured on order lines (gst_rate, gst_amount) + product HSN codes.
 * Additive: no changes to checkout/tax logic.
 *
 * Note: order addresses do not reliably carry a "state" (place of supply), so
 * the IGST vs CGST/SGST split can't be derived per invoice. We report total GST
 * and, for filing convenience, split it as CGST+SGST (intra-state) which is the
 * common single-region case. Capture buyer state at checkout to enable full
 * GSTR-1 (B2B / place-of-supply) later.
 */
class GstReportController extends Controller
{
    public function __construct()
    {
        // License gate: GST reports require the 'gst_reports' feature.
        $this->middleware(function ($request, $next) {
            abort_unless(feature_allowed('gst_reports'), 404);
            return $next($request);
        });
    }

    private function range(Request $request): array
    {
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : Carbon::now()->startOfMonth();
        $to   = $request->to ? Carbon::parse($request->to)->endOfDay() : Carbon::now()->endOfDay();
        return [$from, $to];
    }

    private function baseQuery(Carbon $from, Carbon $to)
    {
        return DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where('orders.delivery_status', '!=', 'cancelled');
    }

    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);
        $base = $this->baseQuery($from, $to);

        $summary = (clone $base)->selectRaw('
            COALESCE(SUM(order_details.price),0) as taxable_value,
            COALESCE(SUM(order_details.gst_amount),0) as gst,
            COALESCE(SUM(order_details.tax),0) as other_tax,
            COALESCE(SUM(order_details.quantity),0) as qty,
            COUNT(DISTINCT order_details.order_id) as orders
        ')->first();

        $byRate = (clone $base)->selectRaw('
            order_details.gst_rate as rate,
            SUM(order_details.price) as taxable_value,
            SUM(order_details.gst_amount) as gst,
            SUM(order_details.quantity) as qty
        ')->groupBy('order_details.gst_rate')->orderBy('order_details.gst_rate')->get();

        $hsn = (clone $base)
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->selectRaw('
                COALESCE(NULLIF(products.hsn_code, ""), "—") as hsn,
                order_details.gst_rate as rate,
                SUM(order_details.quantity) as qty,
                SUM(order_details.price) as taxable_value,
                SUM(order_details.gst_amount) as gst
            ')->groupBy('hsn', 'order_details.gst_rate')->orderBy('hsn')->get();

        return view('backend.reports.gst_report', compact('summary', 'byRate', 'hsn', 'from', 'to'));
    }

    /** GSTR-1 Table 12 style HSN summary as CSV. */
    public function hsn_export(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rows = $this->baseQuery($from, $to)
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->selectRaw('
                COALESCE(NULLIF(products.hsn_code, ""), "") as hsn,
                order_details.gst_rate as rate,
                SUM(order_details.quantity) as qty,
                SUM(order_details.price) as taxable_value,
                SUM(order_details.gst_amount) as gst
            ')->groupBy('hsn', 'order_details.gst_rate')->orderBy('hsn')->get();

        $filename = 'hsn_summary_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['HSN', 'Rate %', 'Total Qty', 'Taxable Value', 'GST', 'CGST', 'SGST', 'Total Value']);
            foreach ($rows as $r) {
                $gst = (float) $r->gst;
                fputcsv($out, [
                    $r->hsn,
                    (float) $r->rate,
                    (int) $r->qty,
                    number_format((float) $r->taxable_value, 2, '.', ''),
                    number_format($gst, 2, '.', ''),
                    number_format($gst / 2, 2, '.', ''),
                    number_format($gst / 2, 2, '.', ''),
                    number_format((float) $r->taxable_value + $gst, 2, '.', ''),
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
