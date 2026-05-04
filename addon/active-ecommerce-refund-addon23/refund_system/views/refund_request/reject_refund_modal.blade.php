<!-- Offcanvas Header -->
<div class="border-sm-bottom pb-15px px-30px">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="fs-16 fw-700 text-dark mb-0">
            {{ translate('Reject Offline Refund Request !') }}
        </h6>
        <button onclick="closeOffcanvas()" class="border-0 bg-transparent">
            ✕
        </button>
    </div>
</div>

<!-- Offcanvas Body -->
<div class="right-offcanvas-body position-absolute h-100 px-30px pt-20px">

    <!-- Order Code -->
    <div class="form-group mb-3">
        <label class="fw-700">{{ translate('Order Code') }}</label>
        <input class="form-control mb-3 rounded-0"
            value="{{ $order_code }}" readonly>
    </div>

    <!-- Dropdown -->
    <div class="form-group mb-3">
        <label class="fw-700">{{translate('Refund Reject Reason')}} <span class="text-danger">*</span></label>
        <select id="reason_select" class="form-control aiz-selectpicker">
            <option value="">{{ ucfirst(translate('Select Reason')) }}</option>

            @foreach ($refund_reasons as $refund_reason)
                @php
                    $translatedReason = ucfirst(translate($refund_reason->reason));
                    $shortReason = \Illuminate\Support\Str::limit($translatedReason, 50, '...');
                @endphp
                
                <option value="{{ $refund_reason->reason }}">
                    {{ $shortReason }}
                </option>
            @endforeach

            <option value="others">Others</option>
        </select>
    </div>

    <!-- Others textarea -->
    <div class="form-group mb-3 d-none" id="other_reason_wrapper">
        <textarea class="form-control mb-3 rounded-0"
            rows="5"
            id="other_reason"
            placeholder="{{ translate('Write reason...') }}"></textarea>
    </div>

    <!-- FINAL VALUE -->
    <input type="hidden" id="final_reason">

</div>

<!-- Footer -->
<div class="w-100 px-30px position-absolute bottom-0 bg-white pt-20px pb-20px">
    <div class="d-flex justify-content-end">
        <button type="button"
            class="fs-14 fw-700 py-10px px-20px btn btn-primary"
            id="reject-refund-request"
            data-refund-id="{{ $refund_id }}">
            {{ translate('Confirm') }}
        </button>
    </div>
</div>