<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(feature_allowed('purchase_inventory'), 404);
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

    public function index()
    {
        $orders = PurchaseOrder::with('supplier')->withCount('items')->orderByDesc('id')->paginate(20);
        return view('backend.inventory.purchase_orders', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $products = Product::orderBy('name')->select('id', 'name')->get();
        return view('backend.inventory.purchase_order_create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        if ($this->demoBlocked()) return back();
        $request->validate([
            'order_date' => 'required',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required',
            'qty' => 'required|array',
        ]);

        $po = PurchaseOrder::create([
            'supplier_id' => $request->supplier_id ?: null,
            'reference' => $request->reference,
            'order_date' => $request->order_date,
            'status' => 'ordered',
            'note' => $request->note,
        ]);

        $total = 0;
        foreach ($request->product_id as $i => $pid) {
            $qty = (int) ($request->qty[$i] ?? 0);
            if (!$pid || $qty <= 0) continue;
            $cost = (float) ($request->unit_cost[$i] ?? 0);
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_id' => $pid,
                'variant' => $request->variant[$i] ?? null,
                'qty' => $qty,
                'unit_cost' => $cost,
            ]);
            $total += $qty * $cost;
        }
        $po->update(['total' => $total]);

        flash(translate('Purchase order created'))->success();
        return redirect()->route('purchase_orders.show', $po->id);
    }

    public function show($id)
    {
        $order = PurchaseOrder::with(['supplier', 'items.product'])->findOrFail($id);
        return view('backend.inventory.purchase_order_show', compact('order'));
    }

    /**
     * Receive a PO: add each item's qty to stock, log a movement, and refresh
     * the product cost to the latest purchase price.
     */
    public function receive($id)
    {
        if ($this->demoBlocked()) return back();
        $order = PurchaseOrder::with('items')->findOrFail($id);
        if ($order->status === 'received') {
            flash(translate('Already received'))->warning();
            return back();
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $variant = $item->variant ?? '';

                // per-variant stock row (create if missing)
                $stock = ProductStock::where('product_id', $item->product_id)
                    ->where('variant', $variant)->first();
                if ($stock) {
                    $stock->qty = (int) $stock->qty + (int) $item->qty;
                    $stock->save();
                }

                // product-level aggregate + latest cost
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->current_stock = (int) $product->current_stock + (int) $item->qty;
                    if ($item->unit_cost > 0) {
                        $product->purchase_price = $item->unit_cost;
                    }
                    $product->save();
                }

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'variant' => $item->variant,
                    'type' => 'purchase',
                    'qty' => (int) $item->qty,
                    'reference' => 'PO#' . $order->id,
                    'note' => 'Purchase order received',
                    'created_by' => auth()->id(),
                ]);
            }

            $order->status = 'received';
            $order->received_at = Carbon::now();
            $order->save();
        });

        flash(translate('Stock received and updated'))->success();
        return back();
    }

    public function destroy($id)
    {
        if ($this->demoBlocked()) return back();
        $order = PurchaseOrder::findOrFail($id);
        if ($order->status === 'received') {
            flash(translate('Cannot delete a received purchase order'))->error();
            return back();
        }
        PurchaseOrderItem::where('purchase_order_id', $id)->delete();
        $order->delete();
        flash(translate('Purchase order deleted'))->success();
        return redirect()->route('purchase_orders.index');
    }
}
