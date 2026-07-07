<!-- Offcanvas Header -->
<div class="border-sm-bottom pb-15px px-30px">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="fs-16 fw-700 text-dark mb-0">
            {{ translate('Refund Request View!') }}
        </h6>
        <button onclick="closeOffcanvas()" class="border-0 bg-transparent">
            ✕
        </button>
    </div>
</div>

<!-- Offcanvas Body -->
<div class="right-offcanvas-body position-absolute h-100 px-30px  inventory-offcanvas-body">
    <div class="d-flex align-items-center justify-content-between mt-3 border-bottom pb-3">
        @if ($refund->orderDetail != null && $refund->orderDetail->product != null)
            <div class="d-flex align-items-center">
                <div
                    class="w-50px h-50px w-lg-50px h-lg-50px d-flex align-items-center justify-content-center rounded-1 overflow-hidden border border-gray-400">
                    <img src="{{ uploaded_asset($refund->orderDetail->product->thumbnail_img) }}" alt="Image"
                        class="img-fit">
                </div>
                <div class="pl-3">
                    <p class="fs-14 fw-700 m-0 text-truncate w-200px w-lg-200px">
                        {{ $refund->orderDetail->product->getTranslation('name') }}
                    </p>
                    <span class="text-gray">#{{ $refund->refund_code }}</span>
                </div>
            </div>
        @endif
        @if ($refund->orderDetail != null)
            <div>
                <p class="fs-14 fw-400 mb-1">{{single_price($refund->refund_amount)}}</p>
                <span class="border border-1 rounded-pill px-1 py-1 text-gray fs-12">{{ $refund->preferred_payment_channel }}</span>
            </div>
        @endif
    </div>
    <div class=" border-bottom py-3">
        <div class="d-flex align-items-start" style="gap: 12px;">
            <div
                class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->user->avatar_original) }}" alt="Image">
            </div>
            <div class="ml-2 mt-2">
                <div class="pb-2 d-flex flex-column mb-2">
                    <span class="">
                        {{ $refund->user->name }}
                    </span>
                    <span class="fs-11 text-gray">
                        {{ $refund->created_at->format('m-d-y \a\t h:i A') }}
                    </span>
                </div>
                <div class="d-inline-block {{ is_numeric($refund->reason) ? 'border border-gray-400 rounded-2 py-2 px-2' : '' }}">
                    <span class="{{ is_numeric($refund->reason) ? 'p-2' : '' }}">
                        {{ $refund->reason_text }}
                    </span>
                </div>
                @if ($refund->images != null)
                    <div class="d-flex flex-wrap align-items-center mt-3" style="gap: 12px;">
                        @foreach (explode(',', $refund->images) as $photo)
                            <div
                                class="w-50px h-50px w-lg-80px h-lg-80px d-flex align-items-center justify-content-center rounded-1 overflow-hidden flex-shrink-0 border border-gray-400">
                                <a href="javascript:void(0)" onclick="openImageModal('{{ uploaded_asset($photo) }}')">
                                    <img src="{{ uploaded_asset($photo) }}" class="img-fit">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    @php
        $admin = \App\Models\User::where('user_type', 'admin')->first();
        $refund_reasons = \App\Models\RefundReason::where('type', 'admin/seller_reject_refund_reason')->where('status', 1)->get();
    @endphp
    @if ($refund->seller_id == $admin->id)
        @if ($refund->admin_approval == 1)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $refund->seller->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        @if ($refund->photo != null)
                            <div
                                class="w-50px h-50px w-lg-80px h-lg-80px mb-3 d-flex align-items-center justify-content-center rounded-1 overflow-hidden flex-shrink-0 border border-gray-400 mt-2">
                                <a href="javascript:void(0)" onclick="openImageModal('{{ uploaded_asset($refund->photo) }}')">
                                    <img src="{{ uploaded_asset($refund->photo) }}" class="img-fit">
                                </a>
                            </div>
                        @endif
                        <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px {{ $refund->photo == null ? 'mt-2' : '' }}">
                            {{translate('Approved')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->admin_approval == 2)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $refund->seller->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <div class="{{ is_numeric($refund->admin_reject_reason) ? 'border border-gray-400 rounded-2  px-2' : '' }} mt-2">
                            <span class="d-block {{ is_numeric($refund->admin_reject_reason) ? 'p-2' : '' }}">
                                {{ $refund->admin_reject_reason_display }}
                            </span>
                        </div>
                        <span class="mt-3 rounded-pill badge badge-inline badge-danger py-3 px-3 w-100px">
                            {{translate('Rejected')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->admin_approval == 0)
            @if (auth()->check() && auth()->user()->user_type === 'admin')
                @if ($refund->preferred_payment_channel == 'offline')
                    <div class=" border-bottom py-3">
                        <div class="d-flex align-items-start" style="gap: 12px;">
                            <div
                                class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                                <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                            </div>
                            <div class="ml-2 mt-2">
                                <div class="pb-2 d-flex flex-column">
                                    <span class="">
                                        {{ $refund->seller->name }}
                                    </span>
                                    <span class="fs-11 text-gray">
                                        {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                    </span>
                                </div>
                                <div class="d-flex mt-2">
                                    <button type="button" 
                                        class="btn btn-primary mr-2 refund-action-btn-{{ $refund->id }} offline-btn"
                                        onclick="toggleOfflineForm({{ $refund->id }})">
                                        {{ translate('Refund By Offline') }}
                                    </button>

                                    <button type="button" 
                                        class="btn btn-danger refund-action-btn-{{ $refund->id }} reject-btn"
                                        onclick="toggleRejectForm({{ $refund->id }})">
                                        {{ translate('Reject Refund') }}
                                    </button>
                                </div>
                                <div id="offline-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <form id="aizSubmitForm" action="{{ route('refund_request_offline_money_by_admin') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="refund_id" value="{{ $refund->id }}">
                                        <input type="text" class="form-control mb-3 rounded-0" name="trx_id" placeholder="{{ translate('Transaction ID') }}" required>
                                        <div class="form-group mb-3">
                                            <div class="add-product-page-content">
                                                <div class="img-upload-container">
                                                    <div class="input-group file-upload-input border border-dashed border-gray-400 rounded-1 w-120px h-120px d-flex align-items-center justify-content-center"
                                                        data-toggle="aizuploader" data-type="image" data-multiple="false">
                                                        <div
                                                            class="form-control p-0 border-0 d-flex align-items-center justify-content-center">
                                                            <img src="{{ static_asset('assets/img/plus-lg.svg') }}"
                                                                class="w-40px h-40px w-md-64px h-md-64px" alt="generate Icon">
                                                        </div>
                                                        <input type="hidden" name="photo" class="selected-files">
                                                    </div>
                                                    <div class="file-preview box sm"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit"
                                            id="confirm-refund-request"
                                            data-refund-id="{{ $refund->id }}"
                                            class="fs-14 fw-700 py-10px px-20px btn btn-primary">
                                            {{ translate('Confirm') }}
                                        </button>
                                    </form>
                                </div>
                                <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <div class="form-group mb-3">
                                        <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                        <select class="form-control reason_select" data-reason-input>
                                            <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                            @foreach ($refund_reasons as $refund_reason)
                                                <option value="{{ $refund_reason->id }}">
                                                    {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                                </option>
                                            @endforeach
                                            <option value="others">{{ translate('Others') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 d-none other_reason_wrapper">
                                        <textarea class="form-control rounded-0 other_reason"
                                            rows="5"
                                            placeholder="{{ translate('Write reason...') }}"></textarea>
                                    </div>
                                    <input type="hidden" name="reject_reason" class="final_reason" value="">
                                    <button type="button" 
                                        class="btn btn-danger reject-refund-btn" 
                                        data-refund-id="{{ $refund->id }}">
                                        {{ translate('Confirm') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class=" border-bottom py-3">
                        <div class="d-flex align-items-start" style="gap: 12px;">
                            <div
                                class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                                <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                            </div>
                            <div class="ml-2 mt-2">
                                <div class="pb-2 d-flex flex-column">
                                    <span class="">
                                        {{ $refund->seller->name }}
                                    </span>
                                    <span class="fs-11 text-gray">
                                        {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                    </span>
                                </div>
                                <div class="d-flex mt-2">
                                    <a href="javascript:void(0)" 
                                        onclick="refund_request_money('{{ $refund->id }}', this)" 
                                        class="btn btn-info mr-2 refund-btn refund-action-btn-{{ $refund->id }}">
                                        {{ translate('Refund Now') }}
                                    </a>

                                    <button type="button" 
                                        class="btn btn-danger reject-btn refund-action-btn-{{ $refund->id }}"
                                        onclick="toggleRejectForm({{ $refund->id }})">
                                        {{ translate('Reject Refund') }}
                                    </button>
                                </div>
                                <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <div class="form-group mb-3">
                                        <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                        <select class="form-control reason_select" data-reason-input>
                                            <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                            @foreach ($refund_reasons as $refund_reason)
                                                <option value="{{ $refund_reason->id }}">
                                                    {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                                </option>
                                            @endforeach
                                            <option value="others">{{ translate('Others') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 d-none other_reason_wrapper">
                                        <textarea class="form-control rounded-0 other_reason"
                                            rows="5"
                                            placeholder="{{ translate('Write reason...') }}"></textarea>
                                    </div>
                                    <input type="hidden" name="reject_reason" class="final_reason" value="">
                                    <button type="button" 
                                        class="btn btn-danger reject-refund-btn" 
                                        data-refund-id="{{ $refund->id }}">
                                        {{ translate('Confirm') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif( auth()->check() && ( auth()->user()->user_type === 'customer' || auth()->user()->user_type === 'seller' ) )    
                <div class=" border-bottom py-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <div
                            class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                            <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                        </div>
                        <div class="ml-2 mt-2">
                            <div class="pb-2 d-flex flex-column">
                                <span class="">
                                    {{ $refund->seller->name }}
                                </span>
                                <span class="text-gray fs-11">
                                    {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                </span>
                            </div>
                            <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                                {{translate('Pending')}}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        @endif

    @elseif ($refund->seller_id != $admin->id && get_setting('product_manage_by_admin') == 1 )
    
    
        @if ($refund->seller_approval == 1)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $refund->seller->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->seller_refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                            {{translate('Approved')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->seller_approval == 2)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $refund->seller->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->seller_refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <div class="{{ is_numeric($refund->reject_reason) ? 'border border-gray-400 rounded-2  px-2' : '' }} mt-2">
                            <span class="d-block {{ is_numeric($refund->reject_reason) ? 'p-2' : '' }}">
                                {{ $refund->seller_reject_reason_display }}
                            </span>
                        </div>
                        <span class="mt-3 rounded-pill badge badge-inline badge-danger py-3 px-3 w-100px">
                            {{translate('Rejected')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->seller_approval == 0)
            @if (auth()->check() && auth()->user()->user_type === 'seller')
                <div class=" border-bottom py-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <div
                            class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                            <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                        </div>
                        <div class="ml-2 mt-2">
                            <div class="pb-2 d-flex flex-column">
                                <span class="">
                                    {{ $refund->seller->name }}
                                </span>
                                <span class="fs-11 text-gray">
                                    {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                </span>
                            </div>
                            <div class="d-flex mt-2">
                                <a href="javascript:void(0)" 
                                    onclick="seller_approval('{{ $refund->id }}')" 
                                    class="btn btn-info mr-2 approve-btn refund-action-btn-{{ $refund->id }}">
                                    {{ translate('Give Approval') }}
                                </a>
                                <button type="button" 
                                    class="btn btn-danger reject-btn refund-action-btn-{{ $refund->id }}"
                                    onclick="toggleRejectForm({{ $refund->id }})">
                                    {{ translate('Reject Refund') }}
                                </button>
                            </div>
                            <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                <div class="form-group mb-3">
                                    <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                    <select class="form-control reason_select">
                                        <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                        @foreach ($refund_reasons as $refund_reason)
                                            <option value="{{ $refund_reason->id }}">
                                                {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                            </option>
                                        @endforeach
                                        <option value="others">Others</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3 d-none other_reason_wrapper">
                                    <textarea class="form-control rounded-0 other_reason"
                                        rows="5"
                                        placeholder="{{ translate('Write reason...') }}"></textarea>
                                </div>
                                <input type="hidden" name="reject_reason" class="final_reason" value="">
                                <button type="button" class="btn btn-danger reject-refund-btn" data-refund-id="{{ $refund->id }}">
                                    {{ translate('Confirm') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif( auth()->check() && ( auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'customer' ) )    
                <div class=" border-bottom py-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <div
                            class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                            <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                        </div>
                        <div class="ml-2 mt-2">
                            <div class="pb-2 d-flex flex-column">
                                <span class="">
                                    {{ $refund->seller->name }}
                                </span>
                                <span class="fs-11 text-gray">
                                    {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                </span>
                            </div>
                            <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                                {{translate('Pending')}}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        @if ($refund->admin_approval == 1)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $admin->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        @if ($refund->photo != null)
                            <div
                                class="w-50px h-50px w-lg-80px h-lg-80px mb-3 d-flex align-items-center justify-content-center rounded-1 overflow-hidden flex-shrink-0 border border-gray-400 mt-2">
                                <a href="javascript:void(0)" onclick="openImageModal('{{ uploaded_asset($refund->photo) }}')">
                                    <img src="{{ uploaded_asset($refund->photo) }}" class="img-fit">
                                </a>
                            </div>
                        @endif
                        <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px {{ $refund->photo == null ? 'mt-2' : '' }}">
                            {{translate('Approved')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->admin_approval == 2)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $admin->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <div class="{{ is_numeric($refund->admin_reject_reason) ? 'border border-gray-400 rounded-2  px-2' : '' }} mt-2">
                            <span class="d-block {{ is_numeric($refund->admin_reject_reason) ? 'p-2' : '' }}">
                                {{ $refund->admin_reject_reason_display }}
                            </span>
                        </div>
                        <span class="mt-3 rounded-pill badge badge-inline badge-danger py-3 px-3 w-100px">
                            {{translate('Rejected')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->admin_approval == 0 && $refund->refund_status == 0)
            @if (auth()->check() && auth()->user()->user_type === 'admin')
                @if ($refund->preferred_payment_channel == 'offline')
                    <div class=" border-bottom py-3">
                        <div class="d-flex align-items-start" style="gap: 12px;">
                            <div
                                class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                                <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                            </div>
                            <div class="ml-2 mt-2">
                                <div class="pb-2 d-flex flex-column">
                                    <span class="">
                                        {{ $admin->name }}
                                    </span>
                                    <span class="fs-11 text-gray">
                                        {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                    </span>
                                </div>
                                <div class="d-flex mt-2">
                                    <button type="button" 
                                        class="btn btn-primary mr-2 refund-action-btn-{{ $refund->id }} offline-btn"
                                        onclick="toggleOfflineForm({{ $refund->id }})">
                                        {{ translate('Refund By Offline') }}
                                    </button>

                                    <button type="button" 
                                        class="btn btn-danger refund-action-btn-{{ $refund->id }} reject-btn"
                                        onclick="toggleRejectForm({{ $refund->id }})">
                                        {{ translate('Reject Refund') }}
                                    </button>
                                </div>
                                <div id="offline-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <form id="aizSubmitForm" action="{{ route('refund_request_offline_money_by_admin') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="refund_id" value="{{ $refund->id }}">
                                        <input type="text" class="form-control mb-3 rounded-0" name="trx_id" placeholder="{{ translate('Transaction ID') }}" required>
                                        <div class="form-group mb-3">
                                            <div class="add-product-page-content">
                                                <div class="img-upload-container">
                                                    <div class="input-group file-upload-input border border-dashed border-gray-400 rounded-1 w-120px h-120px d-flex align-items-center justify-content-center"
                                                        data-toggle="aizuploader" data-type="image" data-multiple="false">
                                                        <div
                                                            class="form-control p-0 border-0 d-flex align-items-center justify-content-center">
                                                            <img src="{{ static_asset('assets/img/plus-lg.svg') }}"
                                                                class="w-40px h-40px w-md-64px h-md-64px" alt="generate Icon">
                                                        </div>
                                                        <input type="hidden" name="photo" class="selected-files">
                                                    </div>
                                                    <div class="file-preview box sm"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit"
                                            id="confirm-refund-request"
                                            data-refund-id="{{ $refund->id }}"
                                            class="fs-14 fw-700 py-10px px-20px btn btn-primary">
                                            {{ translate('Confirm') }}
                                        </button>
                                    </form>
                                </div>
                                <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <div class="form-group mb-3">
                                        <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                        <select class="form-control reason_select" data-reason-input>
                                            <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                            @foreach ($refund_reasons as $refund_reason)
                                                <option value="{{ $refund_reason->id }}">
                                                    {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                                </option>
                                            @endforeach
                                            <option value="others">{{ translate('Others') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 d-none other_reason_wrapper">
                                        <textarea class="form-control rounded-0 other_reason"
                                            rows="5"
                                            placeholder="{{ translate('Write reason...') }}"></textarea>
                                    </div>
                                    <input type="hidden" name="reject_reason" class="final_reason" value="">
                                    <button type="button" 
                                        class="btn btn-danger reject-refund-btn" 
                                        data-refund-id="{{ $refund->id }}">
                                        {{ translate('Confirm') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class=" border-bottom py-3">
                        <div class="d-flex align-items-start" style="gap: 12px;">
                            <div
                                class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                                <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                            </div>
                            <div class="ml-2 mt-2">
                                <div class="pb-2 d-flex flex-column">
                                    <span class="">
                                        {{ $admin->name }}
                                    </span>
                                    <span class="fs-11 text-gray">
                                        {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                    </span>
                                </div>
                                <div class="d-flex mt-2">
                                    <a href="javascript:void(0)" 
                                        onclick="refund_request_money('{{ $refund->id }}', this)" 
                                        class="btn btn-info mr-2 refund-btn refund-action-btn-{{ $refund->id }}">
                                        {{ translate('Refund Now') }}
                                    </a>

                                    <button type="button" 
                                        class="btn btn-danger reject-btn refund-action-btn-{{ $refund->id }}"
                                        onclick="toggleRejectForm({{ $refund->id }})">
                                        {{ translate('Reject Refund') }}
                                    </button>
                                </div>
                                <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <div class="form-group mb-3">
                                        <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                        <select class="form-control reason_select" data-reason-input>
                                            <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                            @foreach ($refund_reasons as $refund_reason)
                                                <option value="{{ $refund_reason->id }}">
                                                    {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                                </option>
                                            @endforeach
                                            <option value="others">{{ translate('Others') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 d-none other_reason_wrapper">
                                        <textarea class="form-control rounded-0 other_reason"
                                            rows="5"
                                            placeholder="{{ translate('Write reason...') }}"></textarea>
                                    </div>
                                    <input type="hidden" name="reject_reason" class="final_reason" value="">
                                    <button type="button" 
                                        class="btn btn-danger reject-refund-btn" 
                                        data-refund-id="{{ $refund->id }}">
                                        {{ translate('Confirm') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif( auth()->check() && ( auth()->user()->user_type === 'customer' || auth()->user()->user_type === 'seller' ) )    
                <div class=" border-bottom py-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <div
                            class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                            <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                        </div>
                        <div class="ml-2 mt-2">
                            <div class="pb-2 d-flex flex-column">
                                <span class="">
                                    {{ $admin->name }}
                                </span>
                                <span class="text-gray fs-11">
                                    {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                </span>
                            </div>
                            <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                                {{translate('Pending')}}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        @else
        <div class=" border-bottom py-3">
            <div class="d-flex align-items-start" style="gap: 12px;">
                <div
                    class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                    <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                </div>
                <div class="ml-2 mt-2">
                    <div class="pb-2 d-flex flex-column">
                        <span class="">
                            {{ $admin->name }}
                        </span>
                        <span class="text-gray fs-11">
                            {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                        </span>
                    </div>
                    <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                        {{translate('N/A')}}
                    </span>
                </div>
            </div>
        </div>    
        @endif

    @elseif ($refund->seller_id != $admin->id && get_setting('product_manage_by_admin') == 0 && get_setting('seller_product_refund_approval') == 'admin_approval_required')
    
        @if ($refund->seller_approval == 1)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $refund->seller->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->seller_refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                            {{translate('Approved')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->seller_approval == 2)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $refund->seller->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->seller_refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <div class="{{ is_numeric($refund->reject_reason) ? 'border border-gray-400 rounded-2  px-2' : '' }} mt-2">
                            <span class="d-block {{ is_numeric($refund->reject_reason) ? 'p-2' : '' }}">
                                {{ $refund->seller_reject_reason_display }}
                            </span>
                        </div>
                        <span class="mt-3 rounded-pill badge badge-inline badge-danger py-3 px-3 w-100px">
                            {{translate('Rejected')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->seller_approval == 0)
            @if (auth()->check() && auth()->user()->user_type === 'seller')
                <div class=" border-bottom py-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <div
                            class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                            <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                        </div>
                        <div class="ml-2 mt-2">
                            <div class="pb-2 d-flex flex-column">
                                <span class="">
                                    {{ $refund->seller->name }}
                                </span>
                                <span class="fs-11 text-gray">
                                    {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                </span>
                            </div>
                            <div class="d-flex mt-2">
                                <a href="javascript:void(0)" 
                                    onclick="seller_approval('{{ $refund->id }}')" 
                                    class="btn btn-info mr-2 approve-btn refund-action-btn-{{ $refund->id }}">
                                    {{ translate('Give Approval') }}
                                </a>
                                <button type="button" 
                                    class="btn btn-danger reject-btn refund-action-btn-{{ $refund->id }}"
                                    onclick="toggleRejectForm({{ $refund->id }})">
                                    {{ translate('Reject Refund') }}
                                </button>
                            </div>
                            <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                <div class="form-group mb-3">
                                    <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                    <select class="form-control reason_select">
                                        <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                        @foreach ($refund_reasons as $refund_reason)
                                            <option value="{{ $refund_reason->id }}">
                                                {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                            </option>
                                        @endforeach
                                        <option value="others">Others</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3 d-none other_reason_wrapper">
                                    <textarea class="form-control rounded-0 other_reason"
                                        rows="5"
                                        placeholder="{{ translate('Write reason...') }}"></textarea>
                                </div>
                                <input type="hidden" name="reject_reason" class="final_reason" value="">
                                <button type="button" class="btn btn-danger reject-refund-btn" data-refund-id="{{ $refund->id }}">
                                    {{ translate('Confirm') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif( auth()->check() && ( auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'customer' ) )    
                <div class=" border-bottom py-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <div
                            class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                            <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                        </div>
                        <div class="ml-2 mt-2">
                            <div class="pb-2 d-flex flex-column">
                                <span class="">
                                    {{ $refund->seller->name }}
                                </span>
                                <span class="fs-11 text-gray">
                                    {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                </span>
                            </div>
                            <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                                {{translate('Pending')}}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        @if ($refund->admin_approval == 1)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $admin->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        @if ($refund->photo != null)
                            <div
                                class="w-50px h-50px w-lg-80px h-lg-80px mb-3 d-flex align-items-center justify-content-center rounded-1 overflow-hidden flex-shrink-0 border border-gray-400 mt-2">
                                <a href="javascript:void(0)" onclick="openImageModal('{{ uploaded_asset($refund->photo) }}')">
                                    <img src="{{ uploaded_asset($refund->photo) }}" class="img-fit">
                                </a>
                            </div>
                        @endif
                        <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px {{ $refund->photo == null ? 'mt-2' : '' }}">
                            {{translate('Approved')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->admin_approval == 2)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $admin->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <div class="{{ is_numeric($refund->admin_reject_reason) ? 'border border-gray-400 rounded-2  px-2' : '' }} mt-2">
                            <span class="d-block {{ is_numeric($refund->admin_reject_reason) ? 'p-2' : '' }}">
                                {{ $refund->admin_reject_reason_display }}
                            </span>
                        </div>
                        <span class="mt-3 rounded-pill badge badge-inline badge-danger py-3 px-3 w-100px">
                            {{translate('Rejected')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->admin_approval == 0 && $refund->refund_status == 0)
            @if (auth()->check() && auth()->user()->user_type === 'admin')
                @if ($refund->preferred_payment_channel == 'offline')
                    <div class=" border-bottom py-3">
                        <div class="d-flex align-items-start" style="gap: 12px;">
                            <div
                                class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                                <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                            </div>
                            <div class="ml-2 mt-2">
                                <div class="pb-2 d-flex flex-column">
                                    <span class="">
                                        {{ $admin->name }}
                                    </span>
                                    <span class="fs-11 text-gray">
                                        {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                    </span>
                                </div>
                                <div class="d-flex mt-2">
                                    <button type="button" 
                                        class="btn btn-primary mr-2 refund-action-btn-{{ $refund->id }} offline-btn"
                                        onclick="toggleOfflineForm({{ $refund->id }})">
                                        {{ translate('Refund By Offline') }}
                                    </button>

                                    <button type="button" 
                                        class="btn btn-danger refund-action-btn-{{ $refund->id }} reject-btn"
                                        onclick="toggleRejectForm({{ $refund->id }})">
                                        {{ translate('Reject Refund') }}
                                    </button>
                                </div>
                                <div id="offline-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <form id="aizSubmitForm" action="{{ route('refund_request_offline_money_by_admin') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="refund_id" value="{{ $refund->id }}">
                                        <input type="text" class="form-control mb-3 rounded-0" name="trx_id" placeholder="{{ translate('Transaction ID') }}" required>
                                        <div class="form-group mb-3">
                                            <div class="add-product-page-content">
                                                <div class="img-upload-container">
                                                    <div class="input-group file-upload-input border border-dashed border-gray-400 rounded-1 w-120px h-120px d-flex align-items-center justify-content-center"
                                                        data-toggle="aizuploader" data-type="image" data-multiple="false">
                                                        <div
                                                            class="form-control p-0 border-0 d-flex align-items-center justify-content-center">
                                                            <img src="{{ static_asset('assets/img/plus-lg.svg') }}"
                                                                class="w-40px h-40px w-md-64px h-md-64px" alt="generate Icon">
                                                        </div>
                                                        <input type="hidden" name="photo" class="selected-files">
                                                    </div>
                                                    <div class="file-preview box sm"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit"
                                            id="confirm-refund-request"
                                            data-refund-id="{{ $refund->id }}"
                                            class="fs-14 fw-700 py-10px px-20px btn btn-primary">
                                            {{ translate('Confirm') }}
                                        </button>
                                    </form>
                                </div>
                                <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <div class="form-group mb-3">
                                        <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                        <select class="form-control reason_select" data-reason-input>
                                            <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                            @foreach ($refund_reasons as $refund_reason)
                                                <option value="{{ $refund_reason->id }}">
                                                    {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                                </option>
                                            @endforeach
                                            <option value="others">{{ translate('Others') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 d-none other_reason_wrapper">
                                        <textarea class="form-control rounded-0 other_reason"
                                            rows="5"
                                            placeholder="{{ translate('Write reason...') }}"></textarea>
                                    </div>
                                    <input type="hidden" name="reject_reason" class="final_reason" value="">
                                    <button type="button" 
                                        class="btn btn-danger reject-refund-btn" 
                                        data-refund-id="{{ $refund->id }}">
                                        {{ translate('Confirm') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class=" border-bottom py-3">
                        <div class="d-flex align-items-start" style="gap: 12px;">
                            <div
                                class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                                <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                            </div>
                            <div class="ml-2 mt-2">
                                <div class="pb-2 d-flex flex-column">
                                    <span class="">
                                        {{ $admin->name }}
                                    </span>
                                    <span class="fs-11 text-gray">
                                        {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                    </span>
                                </div>
                                <div class="d-flex mt-2">
                                    <a href="javascript:void(0)" 
                                        onclick="refund_request_money('{{ $refund->id }}', this)" 
                                        class="btn btn-info mr-2 refund-btn refund-action-btn-{{ $refund->id }}">
                                        {{ translate('Refund Now') }}
                                    </a>

                                    <button type="button" 
                                        class="btn btn-danger reject-btn refund-action-btn-{{ $refund->id }}"
                                        onclick="toggleRejectForm({{ $refund->id }})">
                                        {{ translate('Reject Refund') }}
                                    </button>
                                </div>
                                <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <div class="form-group mb-3">
                                        <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                        <select class="form-control reason_select" data-reason-input>
                                            <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                            @foreach ($refund_reasons as $refund_reason)
                                                <option value="{{ $refund_reason->id }}">
                                                    {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                                </option>
                                            @endforeach
                                            <option value="others">{{ translate('Others') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 d-none other_reason_wrapper">
                                        <textarea class="form-control rounded-0 other_reason"
                                            rows="5"
                                            placeholder="{{ translate('Write reason...') }}"></textarea>
                                    </div>
                                    <input type="hidden" name="reject_reason" class="final_reason" value="">
                                    <button type="button" 
                                        class="btn btn-danger reject-refund-btn" 
                                        data-refund-id="{{ $refund->id }}">
                                        {{ translate('Confirm') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif( auth()->check() && ( auth()->user()->user_type === 'customer' || auth()->user()->user_type === 'seller' ) )    
                <div class=" border-bottom py-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <div
                            class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                            <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                        </div>
                        <div class="ml-2 mt-2">
                            <div class="pb-2 d-flex flex-column">
                                <span class="">
                                    {{ $admin->name }}
                                </span>
                                <span class="text-gray fs-11">
                                    {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                </span>
                            </div>
                            <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                                {{translate('Pending')}}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $admin->name }}
                            </span>
                            <span class="text-gray fs-11">
                                {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                            {{translate('N/A')}}
                        </span>
                    </div>
                </div>
            </div>    
        @endif


    @elseif ($refund->seller_id != $admin->id && get_setting('product_manage_by_admin') == 0 && get_setting('seller_product_refund_approval') == 'seller_can_refund_directly' )
        @if ($refund->seller_approval == 1)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $refund->seller->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->seller_refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        @if ($refund->photo != null)
                            <div
                                class="w-50px h-50px w-lg-80px h-lg-80px mb-3 d-flex align-items-center justify-content-center rounded-1 overflow-hidden flex-shrink-0 border border-gray-400 mt-2">
                                <a href="javascript:void(0)" onclick="openImageModal('{{ uploaded_asset($refund->photo) }}')">
                                    <img src="{{ uploaded_asset($refund->photo) }}" class="img-fit">
                                </a>
                            </div>
                        @endif
                        <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px {{ $refund->photo == null ? 'mt-2' : '' }}">
                            {{translate('Approved')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->seller_approval == 2)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $refund->seller->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->seller_refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <div class="{{ is_numeric($refund->reject_reason) ? 'border border-gray-400 rounded-2  px-2' : '' }} mt-2">
                            <span class="d-block {{ is_numeric($refund->reject_reason) ? 'p-2' : '' }}">
                                {{ $refund->seller_reject_reason_display }}
                            </span>
                        </div>
                        <span class="mt-3 rounded-pill badge badge-inline badge-danger py-3 px-3 w-100px">
                            {{translate('Rejected')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->seller_approval == 0 && $refund->refund_status == 0)
            @if (auth()->check() && auth()->user()->user_type === 'seller')
                @if ($refund->preferred_payment_channel == 'offline')
                    <div class=" border-bottom py-3">
                        <div class="d-flex align-items-start" style="gap: 12px;">
                            <div
                                class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                                <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                            </div>
                            <div class="ml-2 mt-2">
                                <div class="pb-2 d-flex flex-column">
                                    <span class="">
                                        {{ $refund->seller->name }}
                                    </span>
                                    <span class="fs-11 text-gray">
                                        {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                    </span>
                                </div>
                                <div class="d-flex mt-2">
                                    <button type="button" 
                                        class="btn btn-primary mr-2 refund-action-btn-{{ $refund->id }} offline-btn"
                                        onclick="toggleOfflineForm({{ $refund->id }})">
                                        {{ translate('Refund By Offline') }}
                                    </button>

                                    <button type="button" 
                                        class="btn btn-danger refund-action-btn-{{ $refund->id }} reject-btn"
                                        onclick="toggleRejectForm({{ $refund->id }})">
                                        {{ translate('Reject Refund') }}
                                    </button>
                                </div>
                                <div id="offline-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <form id="aizSubmitForm" action="{{ route('refund_request_offline_money_by_seller') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="refund_id" value="{{ $refund->id }}">
                                        <input type="text" class="form-control mb-3 rounded-0" name="trx_id" placeholder="{{ translate('Transaction ID') }}" required>
                                        <div class="form-group mb-3">
                                            <div class="add-product-page-content">
                                                <div class="img-upload-container">
                                                    <div class="input-group file-upload-input border border-dashed border-gray-400 rounded-1 w-120px h-120px d-flex align-items-center justify-content-center"
                                                        data-toggle="aizuploader" data-type="image" data-multiple="false">
                                                        <div
                                                            class="form-control p-0 border-0 d-flex align-items-center justify-content-center">
                                                            <img src="{{ static_asset('assets/img/plus-lg.svg') }}"
                                                                class="w-40px h-40px w-md-64px h-md-64px" alt="generate Icon">
                                                        </div>
                                                        <input type="hidden" name="photo" class="selected-files">
                                                    </div>
                                                    <div class="file-preview box sm"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit"
                                            id="confirm-refund-request"
                                            data-refund-id="{{ $refund->id }}"
                                            class="fs-14 fw-700 py-10px px-20px btn btn-primary">
                                            {{ translate('Confirm') }}
                                        </button>
                                    </form>
                                </div>
                                <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <div class="form-group mb-3">
                                        <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                        <select class="form-control reason_select" data-reason-input>
                                            <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                            @foreach ($refund_reasons as $refund_reason)
                                                <option value="{{ $refund_reason->id }}">
                                                    {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                                </option>
                                            @endforeach
                                            <option value="others">{{ translate('Others') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 d-none other_reason_wrapper">
                                        <textarea class="form-control rounded-0 other_reason"
                                            rows="5"
                                            placeholder="{{ translate('Write reason...') }}"></textarea>
                                    </div>
                                    <input type="hidden" name="reject_reason" class="final_reason" value="">
                                    <button type="button" 
                                        class="btn btn-danger reject-refund-btn" 
                                        data-refund-id="{{ $refund->id }}">
                                        {{ translate('Confirm') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class=" border-bottom py-3">
                        <div class="d-flex align-items-start" style="gap: 12px;">
                            <div
                                class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                                <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                            </div>
                            <div class="ml-2 mt-2">
                                <div class="pb-2 d-flex flex-column">
                                    <span class="">
                                        {{ $refund->seller->name }}
                                    </span>
                                    <span class="fs-11 text-gray">
                                        {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                    </span>
                                </div>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="javascript:void(0)" 
                                        onclick="refund_request_money('{{ $refund->id }}', this)" 
                                        class="btn btn-info mr-2 refund-btn refund-action-btn-{{ $refund->id }}">
                                        {{ translate('Refund Now') }}
                                    </a>

                                    <button type="button" 
                                        class="btn btn-danger reject-btn refund-action-btn-{{ $refund->id }}"
                                        onclick="toggleRejectForm({{ $refund->id }})">
                                        {{ translate('Reject Refund') }}
                                    </button>
                                </div>
                                <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                    <div class="form-group mb-3">
                                        <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                        <select class="form-control reason_select" data-reason-input>
                                            <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                            @foreach ($refund_reasons as $refund_reason)
                                                <option value="{{ $refund_reason->id }}">
                                                    {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                                </option>
                                            @endforeach
                                            <option value="others">{{ translate('Others') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 d-none other_reason_wrapper">
                                        <textarea class="form-control rounded-0 other_reason"
                                            rows="5"
                                            placeholder="{{ translate('Write reason...') }}"></textarea>
                                    </div>
                                    <input type="hidden" name="reject_reason" class="final_reason" value="">
                                    <button type="button" 
                                        class="btn btn-danger reject-refund-btn" 
                                        data-refund-id="{{ $refund->id }}">
                                        {{ translate('Confirm') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif( auth()->check() && ( auth()->user()->user_type === 'customer' || auth()->user()->user_type === 'admin' ) )    
                <div class=" border-bottom py-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <div
                            class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                            <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                        </div>
                        <div class="ml-2 mt-2">
                            <div class="pb-2 d-flex flex-column">
                                <span class="">
                                    {{ $refund->seller->name }}
                                </span>
                                <span class="fs-11 text-gray">
                                    {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                                </span>
                            </div>
                            <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                                {{translate('Pending')}}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->seller->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $refund->seller->name }}
                            </span>
                            <span class="text-gray fs-11">
                                {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                            {{translate('N/A')}}
                        </span>
                    </div>
                </div>
            </div>    
        @endif

        @if ($refund->admin_approval == 1)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $admin->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        @if ($refund->photo != null)
                            <div
                                class="w-50px h-50px w-lg-80px h-lg-80px mb-3 d-flex align-items-center justify-content-center rounded-1 overflow-hidden flex-shrink-0 border border-gray-400 mt-2">
                                <a href="javascript:void(0)" onclick="openImageModal('{{ uploaded_asset($refund->photo) }}')">
                                    <img src="{{ uploaded_asset($refund->photo) }}" class="img-fit">
                                </a>
                            </div>
                        @endif
                        <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px {{ $refund->photo == null ? 'mt-2' : '' }}">
                            {{translate('Approved')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->admin_approval == 2)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $admin->name }}
                            </span>
                            <span class="fs-11 text-gray">
                                {{ $refund->refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <div class="{{ is_numeric($refund->admin_reject_reason) ? 'border border-gray-400 rounded-2  px-2' : '' }} mt-2">
                            <span class="d-block {{ is_numeric($refund->admin_reject_reason) ? 'p-2' : '' }}">
                                {{ $refund->admin_reject_reason_display }}
                            </span>
                        </div>
                        <span class="mt-3 rounded-pill badge badge-inline badge-danger py-3 px-3 w-100px">
                            {{translate('Rejected')}}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($refund->admin_approval == 0)
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{ $admin->name }}
                            </span>
                            <span class="text-gray fs-11">
                                {{ $refund->created_at->format('m-d-y \a\t h:i A') }} 
                            </span>
                        </div>
                        <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                            {{translate('N/A')}}
                        </span>
                    </div>
                </div>
            </div>
        @endif

    @endif

    {{-- dispute  --}}
    @if ($refund->dispute_refund_status != 0)
        <div class=" border-bottom py-3">
            <div class="d-flex align-items-start" style="gap: 12px;">
                <div
                    class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                    <img class="fs-30 text-gray" src="{{ uploaded_asset($refund->user->avatar_original) }}" alt="Image">
                </div>
                <div class="ml-2 mt-2">
                    <div class="pb-2 d-flex flex-column mb-2">
                        <span class="">
                            {{ $refund->user->name }}
                        </span>
                        <span class="text-gray fs-11">
                            {{ $refund->dispute_refund_created_at->format('m-d-y \a\t h:i A') }}
                        </span>
                    </div>
                    <div class="d-inline-block {{ is_numeric($refund->dispute_reason) ? 'border border-gray-400 rounded-2 py-2  px-2' : '' }}">
                        <span class="{{ is_numeric($refund->dispute_reason) ? 'p-2' : '' }}">
                            {{ $refund->dispute_reason_text }}
                        </span>
                    </div>
                    @if ($refund->dispute_images != null)
                        <div class="mt-3 d-flex flex-wrap align-items-center" style="gap: 12px;">
                            @foreach (explode(',', $refund->dispute_images) as $photo)
                                <div
                                    class="w-50px h-50px w-lg-80px h-lg-80px d-flex align-items-center justify-content-center rounded-1 overflow-hidden flex-shrink-0 border border-gray-400">
                                    <a href="javascript:void(0)" onclick="openImageModal('{{ uploaded_asset($photo) }}')">
                                        <img src="{{ uploaded_asset($photo) }}" class="img-fit">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if ($refund->dispute_refund_status == 1)
        @if ( auth()->check() && ( auth()->user()->user_type === 'seller' || auth()->user()->user_type === 'customer' ) )
            <div class=" border-bottom py-3">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div
                        class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                        <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                    </div>
                    <div class="ml-2 mt-2">
                        <div class="pb-2 d-flex flex-column">
                            <span class="">
                                {{$admin->name}}
                            </span>
                            <span class="text-gray fs-11">
                                {{ $refund->dispute_refund_created_at->format('m-d-y \a\t h:i A') }}
                            </span>
                        </div>
                        <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px mt-2">
                            {{translate('Pending')}}
                        </span>
                    </div>
                </div>
            </div>
        @else
            @if ($refund->preferred_payment_channel == 'offline')
                <div class=" border-bottom py-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <div
                            class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                            <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                        </div>
                        <div class="ml-2 mt-2">
                            <div class="pb-2 d-flex flex-column">
                                <span class="">
                                    {{$admin->name}}
                                </span>
                                <span class="text-gray fs-11">
                                    {{ $refund->dispute_refund_created_at->format('m-d-y \a\t h:i A') }} 
                                </span>
                            </div>
                            <div class="d-flex mt-2">
                                <button type="button" 
                                    class="btn btn-primary mr-2 dispute-action-btn-{{ $refund->id }}"
                                    onclick="toggleOfflineForm({{ $refund->id }})">
                                    {{ translate('Dispute By Offline') }}
                                </button>
                                <button type="button" 
                                    class="btn btn-danger dispute-action-btn-{{ $refund->id }}"
                                    onclick="toggleRejectForm({{ $refund->id }})">
                                    {{ translate('Reject Refund') }}
                                </button>
                            </div>
                            <div id="offline-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                <form id="aizSubmitForm" action="{{ route('dispute_refund_request_offline_money_by_admin') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="refund_id" value="{{ $refund->id }}">
                                    <input type="text" class="form-control mb-3 rounded-0" name="trx_id" placeholder="{{ translate('Transaction ID') }}" required>
                                    <div class="form-group mb-3">
                                        <div class="add-product-page-content">
                                            <div class="img-upload-container">
                                                <div class="input-group file-upload-input border border-dashed border-gray-400 rounded-1 w-120px h-120px d-flex align-items-center justify-content-center"
                                                    data-toggle="aizuploader" data-type="image" data-multiple="false">
                                                    <div
                                                        class="form-control p-0 border-0 d-flex align-items-center justify-content-center">
                                                        <img src="{{ static_asset('assets/img/plus-lg.svg') }}"
                                                            class="w-40px h-40px w-md-64px h-md-64px" alt="generate Icon">
                                                    </div>
                                                    <input type="hidden" name="photo" class="selected-files">
                                                </div>
                                                <div class="file-preview box sm"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit"
                                        class="fs-14 fw-700 py-10px px-20px btn btn-primary">
                                        {{ translate('Confirm') }}
                                    </button>
                                </form>
                            </div>
                            <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                <div class="form-group mb-3">
                                    <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                    <select class="form-control reason_select">
                                        <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                        @foreach ($refund_reasons as $refund_reason)
                                            <option value="{{ $refund_reason->id }}">
                                                {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                            </option>
                                        @endforeach
                                        <option value="others">Others</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3 d-none other_reason_wrapper">
                                    <textarea class="form-control rounded-0 other_reason"
                                        rows="5"
                                        placeholder="{{ translate('Write reason...') }}"></textarea>
                                </div>
                                <input type="hidden" name="reject_reason" class="final_reason" value="">
                                <button type="button" class="btn btn-danger reject-dispute-refund-btn" data-refund-id="{{ $refund->id }}">
                                    {{ translate('Confirm') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class=" border-bottom py-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <div
                            class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                            <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                        </div>
                        <div class="ml-2 mt-2">
                            <div class="pb-2 d-flex flex-column">
                                <span class="">
                                    {{$admin->name}}
                                </span>
                                <span class="text-gray fs-11">
                                    {{ $refund->dispute_refund_created_at->format('m-d-y \a\t h:i A') }} 
                                </span>
                            </div>
                            <div class="d-flex mt-2">
                                <a href="javascript:void(0)" 
                                    onclick="dispute_refund_request_money('{{ $refund->id }}', this)" 
                                    class="btn btn-info mr-2 dispute-action-btn-{{ $refund->id }}">
                                    {{ translate('Dispute Now') }}
                                </a>

                                <button type="button" 
                                    class="btn btn-danger dispute-action-btn-{{ $refund->id }}"
                                    onclick="toggleRejectForm({{ $refund->id }})">
                                    {{ translate('Reject Refund') }}
                                </button>
                            </div>
                            <div id="reject-form-{{ $refund->id }}" style="display:none;" class="mt-3">
                                <div class="form-group mb-3">
                                    <label class="fw-700">{{translate('Refund Reject Reason')}} *</label>
                                    <select class="form-control reason_select">
                                        <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                        @foreach ($refund_reasons as $refund_reason)
                                            <option value="{{ $refund_reason->id }}">
                                                {{ \Illuminate\Support\Str::limit(ucfirst(translate($refund_reason->reason)), 50, '...') }}
                                            </option>
                                        @endforeach
                                        <option value="others">Others</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3 d-none other_reason_wrapper">
                                    <textarea class="form-control rounded-0 other_reason"
                                        rows="5"
                                        placeholder="{{ translate('Write reason...') }}"></textarea>
                                </div>
                                <input type="hidden" name="reject_reason" class="final_reason" value="">
                                <button type="button" class="btn btn-danger reject-dispute-refund-btn" data-refund-id="{{ $refund->id }}">
                                    {{ translate('Confirm') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @elseif($refund->dispute_refund_status == 2)
        <div class=" border-bottom py-3">
            <div class="d-flex align-items-start" style="gap: 12px;">
                <div
                    class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                    <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                </div>
                <div class="ml-2 mt-2">
                    <div class="pb-2 d-flex flex-column">
                        <span class="">
                            {{$admin->name}}
                        </span>
                        <span class="text-gray fs-11">
                            {{ $refund->dispute_refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                        </span>
                    </div>
                    @if ($refund->dispute_photo != null)
                        <div
                            class="w-50px h-50px w-lg-80px h-lg-80px mb-3 d-flex align-items-center justify-content-center rounded-1 overflow-hidden flex-shrink-0 border border-gray-400 mt-2">
                            <a href="javascript:void(0)" onclick="openImageModal('{{ uploaded_asset($refund->dispute_photo) }}')">
                                <img src="{{ uploaded_asset($refund->dispute_photo) }}" class="img-fit">
                            </a>
                        </div>
                    @endif
                    <span class="rounded-pill badge badge-inline badge-info py-3 px-3 w-100px {{ $refund->dispute_photo == null ? 'mt-2' : '' }}">
                        {{translate('Dispute Approved')}}
                    </span>
                </div>
            </div>
        </div>
    @elseif($refund->dispute_refund_status == 3)
        <div class=" border-bottom py-3">
            <div class="d-flex align-items-start" style="gap: 12px;">
                <div
                    class="w-50px h-50px d-flex  justify-content-center rounded-pill overflow-hidden border border-gray-400 flex-shrink-0">
                    <img class="fs-30 text-gray" src="{{ uploaded_asset($admin->avatar_original) }}" alt="Image">
                </div>
                <div class="ml-2 mt-2">
                    <div class="pb-2 d-flex flex-column">
                        <span class="">
                            {{$admin->name}}
                        </span>
                        <span class="text-gray fs-11">
                            {{ $refund->dispute_refund_approval_datatime->format('m-d-y \a\t h:i A') }} 
                        </span>
                    </div>
                    <div class="{{ is_numeric($refund->admin_dispute_reject_reason) ? 'border border-gray-400 rounded-2  px-2' : '' }} mt-2">
                        <span class="d-block {{ is_numeric($refund->admin_dispute_reject_reason) ? 'p-2' : '' }}">
                            {{ $refund->admin_dispute_reject_reason_display }}
                        </span>
                    </div>
                    <span class="mt-3 rounded-pill badge badge-inline badge-danger py-3 px-3 w-100px">
                        {{translate('Dispute Rejected')}}
                    </span>
                </div>
            </div>
        </div>
    @endif

</div>

@if ($refund->orderDetail->dispute_refund_days > 0 && $refund->dispute_refund_status == 0 && $refund->refund_status == 2 && $refund->dispute_admin_approval == 0)
    @if (auth()->check() && auth()->user()->user_type === 'customer')
        <div class="w-100 px-30px position-absolute bottom-0 bg-white right-offcavas-footer pt-20px pb-20px">
            <div class="mb-3">
                <label class="d-flex align-items-start cursor-pointer">
                    <input type="checkbox" class="agreeCheckbox mt-1 mr-2">
                    <span class="fs-13">
                        {{ translate('I agree to the Refund Policy and acknowledge that this is my final refund request for this product.') }}
                    </span>
                </label>
            </div>
            <div class="footer-btn">
                <a href="{{ route('dispute_refund_request_send_page', $refund->id) }}"
                class="disputeNowBtn btn btn-outline-info disabled d-block opacity-60"
                style="pointer-events: none;">
                    {{ translate('Dispute Now') }}
                </a>
            </div>
        </div>
    @endif
@endif