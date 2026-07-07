<?php

namespace App\Http\Controllers;

use App\Models\OfflinePayoutMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class OfflinePayoutMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_all_payout_method_for_customer'])->only('index');
        $this->middleware(['permission:add_payout_method_for_customer'])->only('create');
        $this->middleware(['permission:add_payout_method_for_customer'])->only('store');
        $this->middleware(['permission:delete_payout_method_for_customer'])->only('delete');
        $this->middleware(['permission:edit_payout_method_for_customer'])->only('edit');
        $this->middleware(['permission:edit_payout_method_for_customer'])->only('update');

        $this->payout_method_rules = [
            'type' => ['required'],
            'name' => ['required'],
            'logo' => ['required'],
        ];

        $this->payout_method_messages = [
            'name.required' => translate('Payout method name is required'),
            'type.required' => translate('Payout method type is required'),
            'logo.required' => translate('Payout method logo is required'),
        ];
    }

    public function index(Request $request)
    {
        $payout_method_tabs = ['All Methods'];
        return view('refund_request.offline.index', compact('payout_method_tabs'));
    }

    public function create(Request $request)
    {
        return view('refund_request.offline.create');
    }

    public function store(Request $request)
    {
        $rules      = $this->payout_method_rules;
        $messages   = $this->payout_method_messages;
        $validator  = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            flash(translate($errorMessage))->error();
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $payout_method = new OfflinePayoutMethod();
        $payout_method->type = $request->type;
        $payout_method->name = $request->name;
        $payout_method->image = $request->logo;
        $payout_method->save();

        flash(translate('Offline Payout Method has been created successfully!'))->success();
        return redirect()->route('payout_method_for_customer_index');
    }

    public function edit(Request $request, $id)
    {
        $payout_method  = OfflinePayoutMethod::findOrFail($id);
        return view('refund_request.offline.edit', compact('payout_method'));
    }

    public function update(Request $request, $id)
    {
        $rules      = $this->payout_method_rules;
        $messages   = $this->payout_method_messages;
        $validator  = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            flash(translate($errorMessage))->error();
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $payout_method = OfflinePayoutMethod::findOrFail($id);
        $payout_method->name = $request->name;
        $payout_method->type = $request->type;
        $payout_method->image = $request->logo;
        $payout_method->save();

        flash(translate('Offline Payout Method has been updated successfully!'))->success();
        return redirect()->route('payout_method_for_customer_index');
    }

    public function delete($id)
    {
        $payout_method = OfflinePayoutMethod::findOrFail($id);
        OfflinePayoutMethod::destroy($id);
        return 1;
    }

    public function filter(Request $request)
    {
        $sort_search = $request->search ?? null;

        $payout_methods = OfflinePayoutMethod::orderBy('created_at', 'desc');

        if ($sort_search) {
            $payout_methods = $payout_methods->where(function ($q) use ($sort_search) {
                $q->where('name', 'like', "%{$sort_search}%");
            });
        }

        $payout_methods = $payout_methods->paginate(15);

        $view = view('refund_request.offline.table', compact('payout_methods', 'sort_search'))->render();

        return response()->json(['html' => $view]);
    }

    public function bulk_delete(Request $request)
    {
        $payout_method_ids = $request->id;
        foreach ($payout_method_ids as $id) {
            $payout_method = OfflinePayoutMethod::findOrFail($id);
            OfflinePayoutMethod::destroy($id);
        }
        return 1;
    }
}
