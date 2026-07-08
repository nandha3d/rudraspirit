<?php

namespace App\Http\Controllers;

use App\Models\RefundReason;
use App\Models\RefundReasonTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class RefundReasonController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_refund_reason'])->only('index');
        $this->middleware(['permission:add_refund_reason'])->only('create');
        $this->middleware(['permission:add_refund_reason'])->only('store');
        $this->middleware(['permission:delete_refund_reason'])->only('delete');
        $this->middleware(['permission:edit_refund_reason'])->only('edit');
        $this->middleware(['permission:edit_refund_reason'])->only('update');

        $this->refund_reason_rules = [
            'type' => ['required'],
            'reason' => ['required', 'max:120'],
        ];

        $this->refund_reason_messages = [
            'type.required' => translate('Type is required'),
            'description.required' => translate('Reason is required'),
            'description.max'  => translate('Max 120 character'),
        ];
    }

    public function index(Request $request)
    {
        $refund_reason_tabs = ['Customer Refund Reason', 'Admin/Seller Reject Refund Reason'];
        return view('refund_request.reason.index', compact('refund_reason_tabs'));
    }

    public function create(Request $request)
    {
        $type = $request->type;
        return view('refund_request.reason.create', compact('type'));
    }

    public function store(Request $request)
    {
        $rules      = $this->refund_reason_rules;
        $messages   = $this->refund_reason_messages;
        $validator  = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            flash(translate('Sorry! Something went wrong'))->error();
            return Redirect::back()->withErrors($validator);
        }

        $refund_reason = new RefundReason();
        $refund_reason->type = $request->type;
        $refund_reason->reason = $request->reason;
        $refund_reason->save();

        $refund_reason_translation = RefundReasonTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'refund_reason_id' => $refund_reason->id]);
        $refund_reason_translation->reason = $request->reason;
        $refund_reason_translation->save();

        flash(translate('Refund Reason has been created successfully!'))->success();
        return redirect()->route('refund_reason_index');
    }

    public function edit(Request $request, $id)
    {
        $lang   = $request->lang;
        $refund_reason  = RefundReason::findOrFail($id);
        return view('refund_request.reason.edit', compact('refund_reason', 'lang'));
    }

    public function update(Request $request, $id)
    {
        $rules      = $this->refund_reason_rules;
        $messages   = $this->refund_reason_messages;
        $validator  = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            flash(translate('Sorry! Something went wrong'))->error();
            return Redirect::back()->withErrors($validator);
        }

        $refund_reason = RefundReason::findOrFail($id);
        $refund_reason->type = $request->type;
        $refund_reason->reason = $request->reason;
        $refund_reason->save();

        $refund_reason_translation = RefundReasonTranslation::firstOrNew(['lang' => $request->lang, 'refund_reason_id' => $refund_reason->id]);
        $refund_reason_translation->reason = $request->reason;
        $refund_reason_translation->save();

        flash(translate('Refund Reason has been updated successfully!'))->success();
        return redirect()->route('refund_reason_index');
    }

    public function filter(Request $request)
    {
        $sort_search = $request->search ?? null;

        $refund_reasons = RefundReason::orderBy('created_at', 'desc');

        if ($request->filled('refund_reason_type')) {
            if ($request->refund_reason_type == 'customer_refund_reason') {
                $refund_reasons = $refund_reasons->where('type', 'customer_refund_reason');
            } elseif ($request->refund_reason_type == 'admin_seller_reject_refund_reason') {
                $refund_reasons = $refund_reasons->where('type', 'admin/seller_reject_refund_reason');
            }
        }

        if ($sort_search) {
            $refund_reasons = $refund_reasons->where(function ($q) use ($sort_search) {
                $q->where('type', 'like', "%{$sort_search}%")
                    ->orWhere('reason', 'like', "%{$sort_search}%");
            });
        }

        $refund_reasons = $refund_reasons->paginate(15);

        $view = view('refund_request.reason.table', compact('refund_reasons', 'sort_search'))->render();

        return response()->json(['html' => $view]);
    }

    public function bulk_update(Request $request)
    {
        if (!$request->has('id') || count($request->id) == 0) {
            return 0;
        }

        $refundReasons = RefundReason::whereIn('id', $request->id)->get();

        foreach ($refundReasons as $reason) {
            $reason->status = $reason->status == 1 ? 0 : 1;
            $reason->save();
        }

        return 1;
    }

    public function storeAjax(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:120',
            'type' => 'required|string'
        ]);

        $refundReason = new RefundReason();
        $refundReason->type = $request->type;
        $refundReason->reason = $request->reason;
        $refundReason->save();

        return response()->json([
            'success' => true,
            'reason' => $refundReason->reason
        ]);
    }

    public function update_status(Request $request)
    {
        $refundReason = RefundReason::findOrFail($request->id);
        $refundReason->status = $request->status;
        if($refundReason->save()){
            return 1;
        }
        return 0;
    }
}
