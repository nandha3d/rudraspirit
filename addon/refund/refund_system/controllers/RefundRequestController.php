<?php

namespace App\Http\Controllers;

use App\Models\PaymentInformation;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\ClubPoint;
use App\Models\ClubPointDetail;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\RefundReason;
use App\Models\RefundRequest;
use App\Models\Shop;
use App\Models\Wallet;
use App\Models\User;
use App\Utility\EmailUtility;
use Artisan;
use Auth;

class RefundRequestController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_refund_requests'])->only('admin_index');
        $this->middleware(['permission:refund_request_configuration'])->only('refund_config');
    }

    //Store Customer Refund Request
    public function request_store(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|max:120',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp',
        ], [
            'reason.required' => 'Please select or write a refund reason',
            'images.*.image' => 'Each file must be an image',
            'images.*.mimes' => 'Only JPG, JPEG, PNG, WEBP, images are allowed'
        ]);

        $existingRefund = RefundRequest::where('order_detail_id', $id)->first();

        if ($existingRefund) {
            flash(translate("Refund request already submitted for this product"))->warning();
            return redirect()->route('customer_refund_request');
        }

        $user = auth()->user();
        $order_detail = OrderDetail::where('id', $id)->first();
        $refund = new RefundRequest;
        $refund->user_id = $user->id;
        $refund->order_id = $order_detail->order_id;
        $refund->order_detail_id = $order_detail->id;
        $refund->seller_id = $order_detail->seller_id;
        $refund->seller_approval = 0;
        $refund->reason = $request->reason;
        $refund->refund_code = date('Ymd-His') . $refund->id . rand(10, 99);

        $image_ids = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $id = custom_upload_file($image);
                if ($id) {
                    $image_ids[] = $id;
                }
            }
        }

        $refund->images = implode(',', $image_ids);

        $refund->admin_approval = 0;
        $refund->preferred_payment_channel = 'wallet';
        $refund->admin_seen = 0;
        if (is_numeric($order_detail->gst_amount)) {
            $refund->refund_amount = round($order_detail->price + get_gst_by_price_and_rate($order_detail->price, $order_detail->gst_rate), 2);
        } else {
            $refund->refund_amount = $order_detail->price + $order_detail->tax;
        }

        $refund->refund_status = 0;
        if (addon_is_activated('offline_payment') && addon_is_activated(identifier: 'refund_request')) {
            $refund->preferred_payment_channel = $request->preferred_payment_channel;
            if ($request->preferred_payment_channel == 'offline') {
                if($request->payment_information_id == 0 || $request->payment_information_id == null){
                    flash(translate("Please Select the Payment Information"))->error();
                    return back();
                }else{
                    $refund->payment_information_id = $request->payment_information_id;
                }
            }
        }
        if ($refund->save()) {

            $admin = get_admin();
            $emailIdentifiers = array('refund_request_email_to_admin');
            if ($order_detail->order->user->email != null) {
                array_push($emailIdentifiers, 'refund_request_email_to_customer');
            }
            if ($order_detail->order->seller_id != $admin->id) {
                array_push($emailIdentifiers, 'refund_request_email_to_seller');
            }

            EmailUtility::refundEmail($emailIdentifiers, $refund);

            flash(translate("Refund Request has been sent successfully"))->success();
            return redirect()->route('customer_refund_request');
        } else {
            flash(translate("Something went wrong"))->error();
            return back();
        }
    }
    public function dispute_request_store(Request $request, $id)
    {

        $request->validate([
            'reason' => 'required|max:120',
            'dispute_images.*' => 'image|mimes:jpg,jpeg,png,webp',
        ], [
            'reason.required' => 'Please select or write a refund reason',
            'dispute_images.*.image' => 'Each file must be an image',
            'dispute_images.*.mimes' => 'Only JPG, JPEG, PNG, WEBP images are allowed'
        ]);
        
        $refund = RefundRequest::with('orderDetail.product', 'orderDetail.order')
                    ->where('id', $id)
                    ->firstOrFail();

        if($refund->dispute_refund_status == 1){
            flash(translate("Dispute Refund request already submitted for this product"))->warning();
            return redirect()->route('customer_refund_request');
        }            

        $order_detail = $refund->orderDetail;

        $refund->dispute_reason = $request->reason;

        $image_ids = [];
        if ($request->hasFile('dispute_images')) {
            foreach ($request->file('dispute_images') as $image) {
                $id = custom_upload_file($image);
                if ($id) {
                    $image_ids[] = $id;
                }
            }
        }

        $refund->dispute_images = implode(',', $image_ids);

        $refund->dispute_refund_status = 1;
        $refund->dispute_refund_created_at = now();

        if ($refund->save()) {

            $admin = get_admin();
            $emailIdentifiers = array('dispute_refund_request_email_to_admin');
            if ($order_detail->order->user->email != null) {
                array_push($emailIdentifiers, 'dispute_refund_request_email_to_customer');
            }
            if ($order_detail->order->seller_id != $admin->id) {
                array_push($emailIdentifiers, 'dispute_refund_request_email_to_seller');
            }

            EmailUtility::disputeRefundEmail($emailIdentifiers, $refund);
        
            flash(translate("Dispute Refund Request has been sent successfully"))->success();
            return redirect()->route('customer_refund_request');
        } else {
            flash(translate("Something went wrong"))->error();
            return back();
        }
    }

    public function vendor_index(Request $request)
    {
        $sort_search = null;
        $refund_tabs = ['All Refunds', 'Pending', 'Approved', 'Rejected', 'Wallet'];
        if (addon_is_activated('offline_payment')) {
            $refund_tabs[] = 'Offline';
        }
        $refunds = RefundRequest::where('seller_id', Auth::user()->id)->with(['order', 'orderDetail.product'])->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $sort_search = $request->search;

            $refunds->where(function ($query) use ($sort_search) {

                $query->whereHas('order', function ($q) use ($sort_search) {
                    $q->where('code', 'like', '%' . $sort_search . '%');
                })
                    ->orWhereHas('orderDetail.product', function ($q) use ($sort_search) {
                        $q->where('name', 'like', '%' . $sort_search . '%');
                    });
            });
        }

        $refunds = $refunds->paginate(15);

        return view('refund_request.frontend.recieved_refund_request.index', compact('refunds', 'sort_search', 'refund_tabs'));
    }

    public function seller_filter(Request $request)
    {
        $refund_requests = RefundRequest::where('seller_id', Auth::user()->id)->with(['order', 'orderDetail.product'])->orderBy('created_at', 'desc');
        $sort_search = null;

        if ($request->refund_request_status == "approved") {
            $refund_requests = $refund_requests->where('seller_approval', 1);
        } else if ($request->refund_request_status == 'rejected') {
            $refund_requests = $refund_requests->where('seller_approval', 2);
        } else if ($request->refund_request_status == 'pending') {
            $refund_requests = $refund_requests
                ->where('seller_approval', 0)
                ->where('admin_approval', 0);
        } else if ($request->refund_request_status == 'wallet') {
            $refund_requests = $refund_requests->where('preferred_payment_channel', 'wallet')->orWhereNull('preferred_payment_channel');
        } else if ($request->refund_request_status == 'offline') {
            $refund_requests = $refund_requests->where('preferred_payment_channel', 'offline');
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $refund_requests->where(function ($query) use ($sort_search) {

                $query->where('refund_code', 'like', '%' . $sort_search . '%')

                    ->orWhereHas('order', function ($q) use ($sort_search) {
                        $q->where('code', 'like', '%' . $sort_search . '%');
                    })

                    ->orWhereHas('orderDetail.product', function ($q) use ($sort_search) {
                        $q->where('name', 'like', '%' . $sort_search . '%');
                    });

            });
        }

        $refund_requests = $refund_requests->paginate(15);
        $view = view(
            'refund_request.frontend.recieved_refund_request.table',
            compact('refund_requests', 'sort_search')
        )->render();
        return response()->json(['html' => $view]);
    }

    public function seller_refund_configuration()
    {
        return view('refund_request.frontend.configuration');
    }

    public function customer_index()
    {
        $refunds = RefundRequest::where('user_id', Auth::user()->id)->latest()->paginate(10);
        return view('refund_request.frontend.refund_request.index', compact('refunds'));
    }

    public function refund_config()
    {
        return view('refund_request.config');
    }

    public function refund_time_update(Request $request)
    {
        $business_settings = BusinessSetting::where('type', $request->type)->first();
        if ($business_settings != null) {
            $business_settings->value = $request->value;
            $business_settings->save();
        } else {
            $business_settings = new BusinessSetting;
            $business_settings->type = $request->type;
            $business_settings->value = $request->value;
            $business_settings->save();
        }
        Artisan::call('cache:clear');
        flash(translate("Refund Request sending time has been updated successfully"))->success();
        return back();
    }

    public function refund_sticker_update(Request $request)
    {
        $business_settings = BusinessSetting::where('type', $request->type)->first();
        if ($business_settings != null) {
            $business_settings->value = $request->logo;
            $business_settings->save();
        } else {
            $business_settings = new BusinessSetting;
            $business_settings->type = $request->type;
            $business_settings->value = $request->logo;
            $business_settings->save();
        }
        Artisan::call('cache:clear');
        flash(translate("Refund Sticker has been updated successfully"))->success();
        return back();
    }

    public function admin_index(Request $request)
    {
        $sort_search = null;
        $refund_tabs = ['All Refunds', 'Admin Refunds', 'Seller Refunds', 'Pending', 'Approved', 'Rejected', 'Wallet'];
        if (addon_is_activated('offline_payment')) {
            $refund_tabs[] = 'Offline';
        }

        $refunds = RefundRequest::with(['order', 'seller', 'user', 'orderDetail.product'])->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $sort_search = $request->search;

            $refunds->where(function ($query) use ($sort_search) {

                $query->whereHas('order', function ($q) use ($sort_search) {
                    $q->where('code', 'like', '%' . $sort_search . '%');
                })
                    ->orWhereHas('seller', function ($q) use ($sort_search) {
                        $q->where('name', 'like', '%' . $sort_search . '%');
                    })
                    ->orWhereHas('orderDetail.product', function ($q) use ($sort_search) {
                        $q->where('name', 'like', '%' . $sort_search . '%');
                    });
            });
        }

        $refunds = $refunds->paginate(15);

        return view('refund_request.index', compact('refunds', 'sort_search', 'refund_tabs'));
    }

    public function admin_dispute_index(Request $request)
    {
        $sort_search = null;
        $refund_tabs = ['All Dispute Refunds', 'Admin Dispute Refunds', 'Seller Dispute Refunds', 'Pending', 'Approved', 'Rejected', 'Wallet'];
        if (addon_is_activated('offline_payment')) {
            $refund_tabs[] = 'Offline';
        }

        $refunds = RefundRequest::with(['order', 'seller', 'user', 'orderDetail.product'])->where('dispute_refund_status', '!=', 0)->orderBy('dispute_refund_created_at', 'desc');

        if ($request->filled('search')) {
            $sort_search = $request->search;

            $refunds->where(function ($query) use ($sort_search) {

                $query->whereHas('order', function ($q) use ($sort_search) {
                    $q->where('code', 'like', '%' . $sort_search . '%');
                })
                    ->orWhereHas('seller', function ($q) use ($sort_search) {
                        $q->where('name', 'like', '%' . $sort_search . '%');
                    })
                    ->orWhereHas('orderDetail.product', function ($q) use ($sort_search) {
                        $q->where('name', 'like', '%' . $sort_search . '%');
                    });
            });
        }

        $refunds = $refunds->paginate(15);

        return view('refund_request.dispute_index', compact('refunds', 'sort_search', 'refund_tabs'));
    }

    public function filter_refund_request(Request $request)
    {
        $refund_requests = RefundRequest::with(['order', 'seller', 'user', 'orderDetail.product'])->where('dispute_refund_status', null)->orderBy('created_at', 'desc');
        $sort_search = null;

        if ($request->refund_request_status == "approved") {
            $refund_requests = $refund_requests->where('refund_status', 1);
        } else if ($request->refund_request_status == 'rejected') {
            $refund_requests = $refund_requests->where('refund_status', 2);
        } else if ($request->refund_request_status == 'wallet') {
            $refund_requests = $refund_requests->where('preferred_payment_channel', 'wallet')->orWhereNull('preferred_payment_channel');
        } else if ($request->refund_request_status == 'offline') {
            $refund_requests = $refund_requests->where('preferred_payment_channel', 'offline');
        } else if ($request->refund_request_status == 'pending') {
            $refund_requests = $refund_requests->where('refund_status', 0);
        } else if ($request->refund_request_status == 'admin_refunds') {
            $refund_requests = $refund_requests->whereHas('seller', function ($q) {
                $q->where('user_type', 'admin');
            });
        } else if ($request->refund_request_status == 'seller_refunds') {
            $refund_requests = $refund_requests->whereHas('seller', function ($q) {
                $q->where('user_type', 'seller');
            });
        }

        if ($request->search != null) {
            $sort_search = $request->search;
            $refund_requests->where(function ($query) use ($sort_search) {

                $query->where('refund_code', 'like', '%' . $sort_search . '%')

                    ->orWhereHas('order', function ($q) use ($sort_search) {
                        $q->where('code', 'like', '%' . $sort_search . '%');
                    })

                    ->orWhereHas('seller', function ($q) use ($sort_search) {
                        $q->where('name', 'like', '%' . $sort_search . '%');
                    })

                    ->orWhereHas('orderDetail.product', function ($q) use ($sort_search) {
                        $q->where('name', 'like', '%' . $sort_search . '%');
                    });
            });
        }

        $refund_requests = $refund_requests->paginate(15);
        $view = view(
            'refund_request.table',
            compact('refund_requests', 'sort_search')
        )->render();
        return response()->json(['html' => $view]);
    }

    public function filter_dispute_refund_request(Request $request)
    {
        $refund_requests = RefundRequest::with(['order', 'seller', 'user', 'orderDetail.product'])->where('dispute_refund_status', '!=', 0)->orderBy('dispute_refund_created_at', 'desc');
        $sort_search = null;

        if ($request->refund_request_status == "approved") {
            $refund_requests = $refund_requests->where('dispute_refund_status', 2);
        } else if ($request->refund_request_status == 'rejected') {
            $refund_requests = $refund_requests->where('dispute_refund_status', 3);
        } else if ($request->refund_request_status == 'wallet') {
            $refund_requests = $refund_requests->where('preferred_payment_channel', 'wallet')->orWhereNull('preferred_payment_channel');
        } else if ($request->refund_request_status == 'offline') {
            $refund_requests = $refund_requests->where('preferred_payment_channel', 'offline');
        } else if ($request->refund_request_status == 'pending') {
            $refund_requests = $refund_requests->where('dispute_refund_status', 1);
        } else if ($request->refund_request_status == 'admin_dispute_refunds') {
            $refund_requests = $refund_requests->whereHas('seller', function ($q) {
                $q->where('user_type', 'admin');
            });
        } else if ($request->refund_request_status == 'seller_dispute_refunds') {
            $refund_requests = $refund_requests->whereHas('seller', function ($q) {
                $q->where('user_type', 'seller');
            });
        }

        if ($request->search != null) {
            $sort_search = $request->search;

            $refund_requests->where(function ($query) use ($sort_search) {

                $query->where('refund_code', 'like', '%' . $sort_search . '%')

                    ->orWhereHas('order', function ($q) use ($sort_search) {
                        $q->where('code', 'like', '%' . $sort_search . '%');
                    })

                    ->orWhereHas('seller', function ($q) use ($sort_search) {
                        $q->where('name', 'like', '%' . $sort_search . '%');
                    })

                    ->orWhereHas('orderDetail.product', function ($q) use ($sort_search) {
                        $q->where('name', 'like', '%' . $sort_search . '%');
                    });
            });
        }

        $refund_requests = $refund_requests->paginate(15);
        $view = view(
            'refund_request.dispute_table',
            compact('refund_requests', 'sort_search')
        )->render();
        return response()->json(['html' => $view]);
    }

    public function request_approval_vendor(Request $request)
    {
        $authUser = auth()->user();
        $refund = RefundRequest::findOrFail($request->refund_id);
        $refund->seller_approval = 1;
        $refund->seller_refund_approval_datatime = now();

        if ($refund->save()) {
            $emailIdentifiers = array('refund_accepted_by_seller_email_to_admin', 'refund_accepted_by_seller_email_to_seller');
            EmailUtility::refundEmail($emailIdentifiers, $refund);

            return 1;
        } else {
            return 0;
        }
    }

    public function refund_pay(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->refund_id);

        $refund_amount = $refund->refund_amount;

        if (addon_is_activated('club_point')) {
            $club_point = ClubPoint::where('order_id', $refund->order_id)->first();

            if ($club_point) {
                $club_point_details = ClubPointDetail::where('club_point_id', $club_point->id)->where('product_id', $refund->orderDetail->product_id)->first();
                if ($club_point_details) {
                    if ($club_point->convert_status == 1) {
                        $refund_amount -= $club_point_details->converted_amount;
                    } else {
                        $club_point_details->refunded = 1;
                        $club_point_details->save();
                    }
                }
            }
        }

        $wallet = new Wallet;
        $wallet->user_id = $refund->user_id;
        $wallet->amount = $refund_amount;
        $wallet->payment_method = 'Refund';
        $wallet->payment_details = 'Product Money Refund';
        $wallet->save();

        $user = User::findOrFail($refund->user_id);
        $user->balance += $refund_amount;
        $user->save();
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $refund->admin_approval = 1;
            $refund->refund_status = 1;
            $refund->refund_approval_datatime = now();
        } else {
            $refund->seller_approval = 1;
            $refund->refund_status = 1;
            $refund->seller_refund_approval_datatime = now();
        }

        if ($refund->seller_approval == 1) {
            $seller = Shop::where('user_id', $refund->seller_id)->first();
            if ($seller != null) {
                $seller->admin_to_pay -= $refund->refund_amount;
            }
            $seller->save();
        }

        if ($refund->save()) {

            $admin = get_admin();
            $order_detail =  $refund->orderDetail;
            $emailIdentifiers = array('refund_accepted_by_admin_email_to_admin');
            if ($order_detail->order->user->email != null) {
                array_push($emailIdentifiers, 'refund_request_accepted_email_to_customer');
            }
            if ($order_detail->order->seller_id != $admin->id) {
                array_push($emailIdentifiers, 'refund_accepted_by_admin_email_to_seller');
            }

            EmailUtility::refundEmail($emailIdentifiers, $refund);

            return 1;
        } else {
            return response()->json(['success' => false, 'message' => translate('Something went wrong.')]);
        }
    }
    public function dispute_refund_pay(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->refund_id);

        $refund_amount = $refund->refund_amount;

        if (addon_is_activated('club_point')) {
            $club_point = ClubPoint::where('order_id', $refund->order_id)->first();
            if ($club_point != null) {
                $club_point_details = $club_point->club_point_details->where('product_id', $refund->orderDetail->product->id)->first();

                if ($club_point->convert_status == 1) {
                    $refund_amount -= $club_point_details->converted_amount;
                } else {
                    $club_point_details->refunded = 1;
                    $club_point_details->save();
                }
            }
        }

        $wallet = new Wallet;
        $wallet->user_id = $refund->user_id;
        $wallet->amount = $refund_amount;
        $wallet->payment_method = 'Refund';
        $wallet->payment_details = 'Product Money Refund';
        $wallet->save();

        $user = User::findOrFail($refund->user_id);
        $user->balance += $refund_amount;
        $user->save();

        $refund->dispute_admin_approval = 1;
        $refund->dispute_refund_status = 2;
        $refund->dispute_refund_approval_datatime = now();

        if ($refund->seller_approval == 1) {
            $seller = Shop::where('user_id', $refund->seller_id)->first();
            if ($seller != null) {
                $seller->admin_to_pay -= $refund->refund_amount;
            }
            $seller->save();
        }
        
        if ($refund->save()) {

            $admin = get_admin();
            $order_detail =  $refund->orderDetail;
            $emailIdentifiers = array('dispute_refund_accepted_by_admin_email_to_admin');
            if ($order_detail->order->user->email != null) {
                array_push($emailIdentifiers, 'dispute_refund_request_accepted_email_to_customer');
            }
            if ($order_detail->order->seller_id != $admin->id) {
                array_push($emailIdentifiers, 'dispute_refund_accepted_by_admin_email_to_seller');
            }

            EmailUtility::disputeRefundEmail($emailIdentifiers, $refund);

            return 1;
        } else {
            return response()->json(['success' => false, 'message' => translate('Something went wrong.')]);
        }
    }

    public function refund_offline_pay(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->refund_id);

        if ($refund->seller_approval == 1) {
            $seller = Shop::where('user_id', $refund->seller_id)->first();
            if ($seller != null) {
                $seller->admin_to_pay -= $refund->refund_amount;
                $seller->save();
            }
        }

        if (addon_is_activated('club_point')) {
            $club_point = ClubPoint::where('order_id', $refund->order_id)->first();
            if ($club_point != null) {
                $club_point_details = $club_point->club_point_details->where('product_id', $refund->orderDetail->product->id)->first();
                $club_point_details->refunded = 1;
                $club_point_details->save();
            }
        }

        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $refund->admin_approval = 1;
            $refund->refund_status = 1;
            $refund->refund_approval_datatime = now();
        } else {
            $refund->seller_approval = 1;
            $refund->refund_status = 1;
            $refund->seller_refund_approval_datatime = now();
        }

        $refund->transaction_id = $request->trx_id;
        $refund->photo = $request->photo;


        if ($refund->save()) {
            $admin = get_admin();
            $order_detail =  $refund->orderDetail;
            $emailIdentifiers = array('refund_accepted_by_admin_email_to_admin');
            if ($order_detail->order->user->email != null) {
                array_push($emailIdentifiers, 'refund_request_accepted_email_to_customer');
            }
            if ($order_detail->order->seller_id != $admin->id) {
                array_push($emailIdentifiers, 'refund_accepted_by_admin_email_to_seller');
            }

            EmailUtility::refundEmail($emailIdentifiers, $refund);

            flash(translate('Refund has been sent successfully.'))->success();
        } else {
            flash(translate('Something went wrong.'))->error();
        }
        return back();
    }

    public function dispute_refund_offline_pay(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->refund_id);

        if ($refund->seller_approval == 1) {
            $seller = Shop::where('user_id', $refund->seller_id)->first();
            if ($seller != null) {
                $seller->admin_to_pay -= $refund->refund_amount;
            }
            $seller->save();
        }

        if (addon_is_activated('club_point')) {
            $club_point = ClubPoint::where('order_id', $refund->order_id)->first();
            if ($club_point != null) {
                $club_point_details = $club_point->club_point_details->where('product_id', $refund->orderDetail->product->id)->first();
                $club_point_details->refunded = 1;
                $club_point_details->save();
            }
        }

        $refund->dispute_refund_status = 2;
        $refund->dispute_admin_approval = 1;
        $refund->transaction_id = $request->trx_id;
        $refund->dispute_photo = $request->photo;
        $refund->dispute_refund_approval_datatime = now();

        if ($refund->save()) {

            $admin = get_admin();
            $order_detail =  $refund->orderDetail;
            $emailIdentifiers = array('dispute_refund_accepted_by_admin_email_to_admin');
            if ($order_detail->order->user->email != null) {
                array_push($emailIdentifiers, 'dispute_refund_request_accepted_email_to_customer');
            }
            if ($order_detail->order->seller_id != $admin->id) {
                array_push($emailIdentifiers, 'dispute_refund_accepted_by_admin_email_to_seller');
            }

            EmailUtility::disputeRefundEmail($emailIdentifiers, $refund);

            flash(translate('Dispute Refund has been sent successfully.'))->success();
        } else {
            flash(translate('Something went wrong.'))->error();
        }
        return back();
    }

    public function reject_refund_request(Request $request)
    {
        $authUserType = auth()->user()->user_type;
        $refund = RefundRequest::findOrFail($request->refund_id);
        if ($authUserType == 'admin' ||  $authUserType == 'staff') {

            if ($refund->refund_status == 2 && $refund->dispute_refund_status == 1) {
                $refund->dispute_refund_status  = 3;
                $refund->admin_dispute_reject_reason  = $request->reject_reason;
                $refund->dispute_refund_approval_datatime = now();
            } else {
                $refund->admin_approval = 2;
                $refund->refund_status  = 2;
                $refund->admin_reject_reason  = $request->reject_reason;
                $refund->refund_approval_datatime = now();
            }
        } else {
            $refund->seller_approval = 2;
            if (get_setting('product_manage_by_admin') == 0 && get_setting('seller_product_refund_approval') == 'seller_can_refund_directly') {
                $refund->refund_status  = 2;
            }
            $refund->reject_reason  = $request->reject_reason;
            $refund->seller_refund_approval_datatime = now();
        }

        if ($refund->save()) {
            if($refund->dispute_refund_status  == 3){
                $admin = get_admin();
                $order_detail =  $refund->orderDetail;
                if ($authUserType == 'admin' ||  $authUserType == 'staff') {
                    $emailIdentifiers = array('dispute_refund_denied_by_admin_email_to_admin');
                    if ($order_detail->order->user->email != null) {
                        array_push($emailIdentifiers, 'dispute_refund_request_denied_email_to_customer');
                    }
                    if ($order_detail->order->seller_id != $admin->id) {
                        array_push($emailIdentifiers, 'dispute_refund_denied_by_admin_email_to_seller');
                    }
                } else {
                    $emailIdentifiers = array('dispute_refund_denied_by_seller_email_to_admin', 'dispute_refund_denied_by_seller_email_to_seller');
                }
                EmailUtility::disputeRefundEmail($emailIdentifiers, $refund);
            }else{
                $admin = get_admin();
                $order_detail =  $refund->orderDetail;
                if ($authUserType == 'admin' ||  $authUserType == 'staff') {
                    $emailIdentifiers = array('refund_denied_by_admin_email_to_admin');
                    if ($order_detail->order->user->email != null) {
                        array_push($emailIdentifiers, 'refund_request_denied_email_to_customer');
                    }
                    if ($order_detail->order->seller_id != $admin->id) {
                        array_push($emailIdentifiers, 'refund_denied_by_admin_email_to_seller');
                    }
                } else {
                    $emailIdentifiers = array('refund_denied_by_seller_email_to_admin', 'refund_denied_by_seller_email_to_seller');
                }
                EmailUtility::refundEmail($emailIdentifiers, $refund);
            }

            flash(translate('Refund request rejected successfully.'))->success();
            return back();
        } else {
            return back();
        }
    }

    public function refund_request_send_page($id)
    {
        $payment_information_id = 0;
        $user_id = Auth::user()->id;
        $payment_informations = PaymentInformation::where('user_id', $user_id)->get();
        $refund_reasons = RefundReason::where('type', 'customer_refund_reason')->where('status', 1)->get();
        if (count($payment_informations)) {
            $payment_information = $payment_informations->toQuery()->first();
            $payment_information_id = $payment_information->id;
            $default_payment_information = $payment_informations->toQuery()->where('set_default', 1)->first();
            if ($default_payment_information != null) {
                $payment_information_id = $default_payment_information->id;
            }
        }
        $order_detail = OrderDetail::findOrFail($id);
        if ($order_detail->product != null) {
            return view('refund_request.frontend.refund_request.create', compact('order_detail', 'payment_information_id', 'refund_reasons'));
        } else {
            return back();
        }
    }

    public function dispute_refund_request_send_page($id)
    {
        $user_id = Auth::user()->id;

        $refund = RefundRequest::with('orderDetail.product', 'orderDetail.order')
                    ->where('id', $id)
                    ->where('user_id', $user_id) 
                    ->firstOrFail();

        $order_detail = $refund->orderDetail;

        $refund_reasons = RefundReason::where('type', 'customer_refund_reason')->where('status', 1)->get();

        if ($order_detail && $order_detail->product != null) {
            return view('refund_request.frontend.refund_request.dispute_create', compact('order_detail', 'refund_reasons', 'refund'));
        }

        return back();
    }

    public function reason_view($id)
    {
        $user = auth()->user();
        $refund = RefundRequest::findOrFail($id);
        if ($user->user_type == 'admin' || $user->user_type == 'staff') {
            if ($refund->orderDetail != null) {
                $refund->admin_seen = 1;
                $refund->save();
                return view('refund_request.reason', compact('refund'));
            }
        } else {
            return view('refund_request.frontend.refund_request.reason', compact('refund'));
        }
    }

    public function reject_reason_view($id)
    {
        $authUserType = auth()->user()->user_type;
        $refund = RefundRequest::findOrFail($id);
        if ($authUserType == 'customer') {
            $html = '
                <div class="mb-2">
                    <p>' . ($refund->admin_reject_reason ?? 'N/A') . '</p>
                </div>
            ';
        } else {
            $html = '
                <div class="mb-2">
                    <strong>Reason By Admin:</strong>
                    <p>' . ($refund->admin_reject_reason ?? 'N/A') . '</p>
                </div>
                <div>
                    <strong>Reason By Seller:</strong>
                    <p>' . ($refund->reject_reason ?? 'N/A') . '</p>
                </div>
            ';
        }
        return $html;
    }

    public function categoriesWiseProductRefund(Request $request)
    {
        $sort_search = null;
        $category_tabs = ['All Categories', 'Main Categories'];

        $categories = Category::orderBy('order_level', 'desc');

        if ($request->filled('search')) {
            $sort_search = $request->search;
            $categories = $categories->where('name', 'like', '%' . $sort_search . '%');
        }

        $categories = $categories->paginate(15);
        return view('backend.product.category_wise_refund.set_refund', compact('categories', 'sort_search', 'category_tabs'));
    }

    public function filter_categories(Request $request)
    {
        $categories = Category::orderBy('order_level', 'desc');
        $sort_search = null;

        if ($request->category_status == "main_categories") {
            $categories = $categories->where('parent_id', 0);
        }

        if ($request->unassigned == 1) {
            $categories = $categories->where(function ($q) {
                $q->whereNull('refund_request_time')
                    ->orWhere('refund_request_time', 0);
            });
        }

        if ($request->filled('search')) {
            $sort_search = $request->search;
            $categories = $categories->where('name', 'like', '%' . $sort_search . '%');
        }

        $categories = $categories->paginate(15);
        $view = view(
            'backend.product.category_wise_refund.table',
            compact('categories', 'sort_search')
        )->render();
        return response()->json(['html' => $view]);
    }

    public function sellerCategoriesWiseProductRefund(Request $request)
    {
        $sort_search = null;
        $category_tabs = ['All Categories', 'Main Categories'];
        $categories = Category::orderBy('order_level', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $categories = $categories->where('name', 'like', '%' . $sort_search . '%');
        }
        $categories = $categories->paginate(15);
        return view('refund_request.frontend.recieved_refund_request.category_wise_refund', compact('categories', 'sort_search', 'category_tabs'));
    }

    public function seller_filter_categories(Request $request)
    {
        $categories = Category::orderBy('order_level', 'desc');
        $sort_search = null;

        if ($request->category_status == "main_categories") {
            $categories = $categories->where('parent_id', 0);
        }

        if ($request->unassigned == 1) {
            $categories = $categories->where(function ($q) {
                $q->whereNull('refund_request_time')
                    ->orWhere('refund_request_time', 0);
            });
        }

        if ($request->filled('search')) {
            $sort_search = $request->search;
            $categories = $categories->where('name', 'like', '%' . $sort_search . '%');
        }

        $categories = $categories->paginate(15);
        $view = view(
            'refund_request.frontend.recieved_refund_request.category_wise_refund_table',
            compact('categories', 'sort_search')
        )->render();
        return response()->json(['html' => $view]);
    }

    public function updateRefundSettings(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id' => 'required|exists:categories,id',
            'refund_request_time' => 'nullable|integer|min:0',
        ]);

        $categoryId = $request->id;
        $refundTime = $request->refund_request_time ?? 0;

        $category = Category::findOrFail($categoryId);

        $childCategoryIds = $this->getAllChildCategoryIds($category->id);

        $allCategoryIds = array_merge($childCategoryIds, [$category->id]);

        Category::whereIn('id', $allCategoryIds)->update([
            'refund_request_time' => $refundTime,
        ]);

        return response()->json([
            'message' => 'Refund settings updated successfully for category and all its children!',
            'success' => true,
        ]);
    }

    private function getAllChildCategoryIds($parentId)
    {
        $childIds = [];

        $children = Category::where('parent_id', $parentId)->pluck('id');

        foreach ($children as $childId) {
            $childIds[] = $childId;
            $childIds = array_merge($childIds, $this->getAllChildCategoryIds($childId));
        }

        return $childIds;
    }

    public function checkRefundableCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id'
        ]);

        $category = Category::findOrFail($request->category_id);

        $isRefundable = $category->refund_request_time > 0;

        return response()->json([
            'status' => 'success',
            'is_refundable' => $isRefundable,
            'message' => $isRefundable
                ? 'Category is refundable.'
                : 'Category is not refundable.'
        ]);
    }

    public function checkSellerRefundableCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id'
        ]);

        $category = Category::findOrFail($request->category_id);

        $isRefundable = $category->refund_request_time > 0;

        return response()->json([
            'status' => 'success',
            'is_refundable' => $isRefundable,
            'message' => $isRefundable
                ? 'Category is refundable.'
                : 'Category is not refundable.'
        ]);
    }

    public function order_details_update()
    {
        $refund_request_time = get_setting('refund_request_time');

        $refundable_product_ids = Product::where('refundable', 1)->pluck('id');

        if ($refundable_product_ids->isNotEmpty()) {
            OrderDetail::whereIn('product_id', $refundable_product_ids)->update([
                'refund_days' => $refund_request_time,
            ]);
        }
    }

    public function refund_offline_modal(Request $request)
    {
        return view('refund_request.modal', [
            'refund_id' => $request->refund_id
        ]);
    }

    public function payment_info_modal(Request $request)
    {
        $refund = RefundRequest::with('paymentInformation')->findOrFail($request->refund_id);

        $paymentInfo = $refund->paymentInformation;

        return view('refund_request.payment_info_modal', compact('refund', 'paymentInfo'));
    }

    public function reject_refund_modal(Request $request)
    {
        $refund_reasons = RefundReason::where('type', 'admin/seller_reject_refund_reason')->where('status', 1)->get();
        return view('refund_request.reject_refund_modal', [
            'refund_id' => $request->refund_id,
            'order_code' => $request->order_code,
            'refund_reasons' => $refund_reasons
        ]);
    }

    public function refund_request_view(Request $request)
    {
        $refund = RefundRequest::with(['order', 'seller', 'user', 'orderDetail.product'])
            ->where('id', $request->refund_id)
            ->first();

        return view('refund_request.view', [
            'refund' => $refund,
            'refund_id' => $request->refund_id,
            'order_code' => $request->order_code
        ]);
    }

    public function dispute_refund_time_update(Request $request)
    {
        $business_settings = BusinessSetting::where('type', $request->type)->first();

        if ($business_settings != null) {
            $business_settings->value = $request->value;
            $business_settings->save();
        } else {
            $business_settings = new BusinessSetting;
            $business_settings->type = $request->type;
            $business_settings->value = $request->value;
            $business_settings->save();
        }

        Artisan::call('cache:clear');
        flash(translate("Dispute refund request sending time has been updated successfully"))->success();
        return back();
    }

    public function updateBulkRefundDaysAssign(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id' => 'required|array',
            'id.*' => 'exists:categories,id',
            'bulk_refund_days' => 'nullable|integer|min:0',
        ]);

        $categoryIds = $request->id;
        $refundTime = $request->bulk_refund_days ?? 0;

        $allCategoryIds = [];

        foreach ($categoryIds as $categoryId) {

            $allCategoryIds[] = $categoryId;

            $childIds = $this->getAllChildCategoryIds($categoryId);

            $allCategoryIds = array_merge($allCategoryIds, $childIds);
        }

        $allCategoryIds = array_unique($allCategoryIds);

        Category::whereIn('id', $allCategoryIds)->update([
            'refund_request_time' => $refundTime,
        ]);

        return response()->json(1);
    }
}
