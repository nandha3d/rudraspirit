<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
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

    public function movements(Request $request)
    {
        $movements = StockMovement::with('product')->orderByDesc('id')->paginate(30);
        $products = Product::orderBy('name')->select('id', 'name')->get();
        return view('backend.inventory.movements', compact('movements', 'products'));
    }

    /** Manual stock adjustment (correction). Signed qty; updates stock + logs it. */
    public function adjust(Request $request)
    {
        if ($this->demoBlocked()) return back();
        $request->validate([
            'product_id' => 'required',
            'qty' => 'required|integer',
        ]);
        $qty = (int) $request->qty;
        if ($qty === 0) {
            flash(translate('Quantity cannot be zero'))->warning();
            return back();
        }

        DB::transaction(function () use ($request, $qty) {
            $variant = $request->variant ?? '';
            $stock = ProductStock::where('product_id', $request->product_id)->where('variant', $variant)->first();
            if ($stock) {
                $stock->qty = max(0, (int) $stock->qty + $qty);
                $stock->save();
            }
            $product = Product::find($request->product_id);
            if ($product) {
                $product->current_stock = max(0, (int) $product->current_stock + $qty);
                $product->save();
            }
            StockMovement::create([
                'product_id' => $request->product_id,
                'variant' => $request->variant,
                'type' => 'adjustment',
                'qty' => $qty,
                'reference' => $request->reference,
                'note' => $request->note,
                'created_by' => auth()->id(),
            ]);
        });

        flash(translate('Stock adjusted'))->success();
        return back();
    }

    public function low_stock(Request $request)
    {
        $threshold = $request->filled('threshold') ? (int) $request->threshold : null;
        $query = Product::query()->select('id', 'name', 'current_stock', 'low_stock_quantity', 'unit');
        if ($threshold !== null) {
            $query->where('current_stock', '<=', $threshold);
        } else {
            $query->whereColumn('current_stock', '<=', 'low_stock_quantity');
        }
        $products = $query->orderBy('current_stock')->paginate(25)->appends($request->all());
        return view('backend.inventory.low_stock', compact('products', 'threshold'));
    }
}
