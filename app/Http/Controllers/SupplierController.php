<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
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
        $suppliers = Supplier::orderBy('name')->paginate(20);
        return view('backend.inventory.suppliers', compact('suppliers'));
    }

    public function store(Request $request)
    {
        if ($this->demoBlocked()) return back();
        $request->validate(['name' => 'required|max:191']);
        Supplier::create($request->only('name', 'email', 'phone', 'gstin', 'address', 'note') + ['status' => 'active']);
        flash(translate('Supplier added'))->success();
        return back();
    }

    public function update(Request $request, $id)
    {
        if ($this->demoBlocked()) return back();
        $supplier = Supplier::findOrFail($id);
        $request->validate(['name' => 'required|max:191']);
        $supplier->update($request->only('name', 'email', 'phone', 'gstin', 'address', 'note', 'status'));
        flash(translate('Supplier updated'))->success();
        return back();
    }

    public function destroy($id)
    {
        if ($this->demoBlocked()) return back();
        Supplier::destroy($id);
        flash(translate('Supplier deleted'))->success();
        return back();
    }
}
