@extends('backend.layouts.app')

@section('content')

    <div class="add-product-page-content">
        <div class="row">
            <div class="col-lg-7 mx-auto">
                <div class="card">
                    <div class="card-header d-flex flex-column align-items-start ">
                        <h3 class="h6">
                            {{ translate('Refund Management') }}
                        </h3>
                        <small class="text-muted fs-12 fw-400 mb-0">
                            {{ translate('When "Refund needs approval from Admin" is enabled, all refund requests for seller products will be managed by the admin.') }}
                        </small>
                    </div>
                    <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="types[]" value="seller_product_refund_approval">
                        <div class="card-body">
                            <div class="radio mar-btm">
                                <input id="admin_approval_required" class="magic-radio" type="radio"
                                    name="seller_product_refund_approval" value="admin_approval_required"
                                    {{ get_setting('seller_product_refund_approval') == 'admin_approval_required' ? 'checked' : '' }}>
                                <label for="admin_approval_required" class="fs-13">
                                    {{ translate('Refund needs approval from Admin') }}
                                </label>
                            </div>
                            <div class="radio mar-btm">
                                <input id="seller_can_refund_directly" class="magic-radio" type="radio"
                                    name="seller_product_refund_approval" value="seller_can_refund_directly"
                                    {{ get_setting('seller_product_refund_approval') == 'seller_can_refund_directly' ? 'checked' : '' }}>
                                <label for="seller_can_refund_directly" class="fs-13">
                                    {{ translate('Seller Can Refund Directly') }}
                                </label>
                            </div>
                            <div class="form-group mb-0 text-right mt-3">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    {{ translate('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card">
                    <div class="card-header d-flex flex-column align-items-start ">
                        <h3 class="h6">{{ translate('Refund Type') }}</h3>
                        <small class="text-muted fs-12 fw-400 mb-0">
                            {{ translate('When “Global Refund” is enabled, the refund period applies uniformly to all products. When “Category-Based Refund” is enabled, the refund period is applied to all products within the selected category.') }}
                        </small>
                    </div>
                    <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="types[]" value="refund_type">
                        <div class="card-body">
                            <div class="radio mar-btm">
                                <input id="global_refund" class="magic-radio" type="radio" name="refund_type"
                                    value="global_refund"
                                    {{ get_setting('refund_type') == 'global_refund' ? 'checked' : '' }}>
                                <label for="global_refund" class="fs-13">{{ translate('Global Refund') }}</label>
                            </div>
                            <div class="radio mar-btm">
                                <input id="category_based_refund" class="magic-radio" type="radio" name="refund_type"
                                    value="category_based_refund"
                                    {{ get_setting('refund_type') == 'category_based_refund' ? 'checked' : '' }}>
                                <label for="category_based_refund"
                                    class="fs-13">{{ translate('Category Based Refund') }}</label>
                            </div>
                            <small class="text-danger fs-12 fw-400 mb-0">
                                {{ translate('Switching the refund type is a core setting and will mark all products as non-refundable. After changing it, each product must be updated manually. It will also reset category-based refund times, which must be reconfigured again.') }}
                            </small>
                            <div class="form-group mb-0 text-right">
                                <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                @if (get_setting('refund_type') == 'global_refund')
                    <div class="card" id="global-refund-time">
                        <div class="card-header d-flex flex-column align-items-start ">
                            <h5 class="h6">{{ translate('Set Refund Time') }}</h5>
                            <small class="text-muted fs-12 fw-400 mb-0">
                                {{ translate('When the "Global Refund" is enabled, set the refund days here.') }}
                            </small>
                        </div>
                        <form class="form-horizontal" action="{{ route('refund_request_time_config') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <input type="hidden" name="type" value="refund_request_time">
                                    <label class="col-form-label">
                                        {{ translate('Set Time for sending Refund Request') }}
                                    </label>
                                    <div class="row gutters-5 align-items-center pr-2">
                                        <div class="col-12 pr-0 input-group d-block d-md-flex">
                                            <input type="number" min="0" step="1" value="{{ get_setting('refund_request_time') }}" name="value" class="form-control">
                                            <div class="input-group-prepend"><span class="input-group-text flex-grow-1">{{ translate('Days') }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-0 text-right">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        {{ translate('Save') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
                @if (get_setting('refund_type') == 'category_based_refund')
                    <div class="card" id="category-refund-time">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Set Category Based Refund Time') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-12 col-form-label">
                                    {{ translate('Configure the refund request submission time limit ') }}
                                    <a href="{{ route('categories_wise_product_refund') }}">
                                        {{ translate('Here') }}
                                    </a>
                                </label>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header d-flex flex-column align-items-start ">
                        <h3 class="h6">{{ translate('Dispute Refund Option') }}</h3>
                        <small class="text-muted fs-12 fw-400 mb-0">
                            {{ translate('If Dispute Refund is enabled, customers can resubmit rejected refund requests with reasons and images. Seller product disputes are sent directly to the admin.') }}
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="form-group row align-items-center">
                            <div class="col-lg-1">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" onchange="updateSettings(this, 'enable_dispute_refund')"
                                        @if (get_setting('enable_dispute_refund') == 1) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <label
                                class="col-lg-11 col-form-label pt-0">{{ translate('Enable Dispute') }}</label>
                        </div>
                        <small class="text-danger fs-12 fw-400 mb-0">
                            {{ translate('Orders placed when dispute is enabled will still follow the dispute time limit even if it is turned off later.') }}
                        </small>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex flex-column align-items-start ">
                        <h3 class="h6">{{ translate('Set Dispute Refund Time') }}</h3>
                        <small class="text-muted fs-12 fw-400 mb-0">
                            {{ translate('Dispute refund time is applied globally to all products. Set the time limit here, which will be added to the refund period.') }}
                        </small>
                    </div>
                    <form class="form-horizontal" action="{{ route('dispute_refund_request_time_config') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <input type="hidden" name="type" value="dispute_refund_request_time">
                                <label
                                    class="col-form-label">{{ translate('Set Time for Sending Dispute Refund Request') }}</label>
                                <div class="row gutters-5 align-items-center pr-2">
                                    <div class="col-12 pr-0 input-group d-block d-md-flex">
                                        <input type="number" min="0" step="1" value="{{ get_setting('dispute_refund_request_time') }}" name="value" @if (get_setting('enable_dispute_refund') == 0) disabled @endif class="form-control">
                                        <div class="input-group-prepend"><span class="input-group-text flex-grow-1">{{ translate('Days') }}</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0 text-right">
                                <button type="submit"
                                    class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card">
                    <div class="card-header d-flex flex-column align-items-start ">
                        <h3 class="h6">{{ translate('Customer Preset Refund Reason') }}</h3>
                        <small class="text-muted fs-12 fw-400 mb-0">
                            {{ translate('These refund reasons will be shown to customers when submitting a refund request.') }}
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="form-group overflow-hidden">
                            <div id="refund-reason-list">
                                @foreach (\App\Models\RefundReason::where('type', 'customer_refund_reason')->take(10)->get() as $refund_reason)
                                    <div class="mb-2">
                                        <div class="single-warranty-notes border border-2 border-gray-300 rounded-2 p-3 overflow-hidden has-transition">
                                            <p class="fs-14 fw-400 m-0 text-truncate-1">
                                                {{ $refund_reason->getTranslation('reason') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="new-reason-box" class="refund-reason-code pl-3 pr-2 border border-2 bg-light border-light rounded-2 has-transition d-none">
                                <div class="d-flex align-items-center justify-between">
                                    <div class="flex-grow-1">
                                        <input type="text"
                                            id="refund-reason-input"
                                            data-type="customer_refund_reason"
                                            class="form-control px-0 text-blue fs-12 fw-bold bg-transparent border-0 refund-reason-input">
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="border-0 bg-transparent"
                                            onclick="clearField(this, 'refund-reason')">
                                            <svg id="pen-icon" xmlns="http://www.w3.org/2000/svg" width="12"
                                                height="12" viewBox="0 0 12 12">
                                                <path
                                                    d="M12.2,6.567l-5.949,5.95a2.49,2.49,0,0,1-1.157.655l-2.282.571a.5.5,0,0,1-.6-.6l.571-2.282A2.49,2.49,0,0,1,3.437,9.7l5.949-5.95Zm1.409-4.226a1.992,1.992,0,0,1,0,2.818l-.705.7L10.09,3.045l.705-.7A1.992,1.992,0,0,1,13.613,2.341Z"
                                                    transform="translate(-2.196 -1.758)" fill="#a5a5b8" />
                                            </svg>
                                            <span class="fs-10 fw-400 text-blue">{{ translate('Clear') }}</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="refund-reason-message bg-dark mt-1 position-absolute py-1 px-2 rounded-1">
                                    <span class="fs-12 text-white fw-300">
                                        {{ translate('Type and press enter to save') }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="button"
                                    id="add-new-reason-btn"
                                    class="btn btn-block border border-gray-400 border-dashed hov-bg-soft-secondary mt-2 fs-14 rounded-2 d-flex align-items-center justify-content-center">
                                    <i class="las la-plus"></i>
                                    <span class="ml-2">{{ translate('Add New Reason') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex flex-column align-items-start ">
                        <h3 class="h6">{{ translate('Admin/Seller Preset Reject Refund Reason') }}</h3>
                        <small class="text-muted fs-12 fw-400 mb-0">
                            {{ translate('These reasons will be visible to the admin/seller while reviewing refund requests for rejection.') }}
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="form-group overflow-hidden">
                            <div id="reject-reason-list">
                                @foreach (\App\Models\RefundReason::where('type', 'admin/seller_reject_refund_reason')->take(10)->get() as $refund_reason)
                                    <div class="mb-2">
                                        <div class="single-warranty-notes border border-2 border-gray-300 rounded-2 p-3 overflow-hidden has-transition">
                                            <p class="fs-14 fw-400 m-0 text-truncate-2">
                                                {{ $refund_reason->getTranslation('reason') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="new-reject-reason-box" class="refund-reason-code pl-3 pr-2 border border-2 bg-light border-light rounded-2 has-transition d-none">
                                <div class="d-flex align-items-center justify-between">
                                    <div class="flex-grow-1">
                                        <input type="text"
                                            id="reject-reason-input"
                                            data-type="admin/seller_reject_refund_reason"
                                            class="form-control px-0 text-blue fs-12 fw-bold bg-transparent border-0 refund-reason-input">
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="border-0 bg-transparent"
                                            onclick="clearField(this, 'reject-reason')">
                                            <span class="fs-10 fw-400 text-blue">Clear</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="refund-reason-message bg-dark mt-1 position-absolute py-1 px-2 rounded-1">
                                    <span class="fs-12 text-white fw-300">
                                        Type and press enter to save
                                    </span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="button"
                                    id="add-new-reject-reason-btn"
                                    class="btn btn-block border border-gray-400 border-dashed hov-bg-soft-secondary mt-2 fs-14 rounded-2 d-flex align-items-center justify-content-center">
                                    <i class="las la-plus"></i>
                                    <span class="ml-2">Add New Reason</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex flex-column align-items-start ">
                        <h3 class="h6">{{ translate('Set Refund Sticker') }}</h3>
                        <small class="text-muted fs-12 fw-400 mb-0">
                            {{ translate('This sticker will be displayed on the product details page.') }}
                        </small>
                    </div>
                    <form class="form-horizontal" action="{{ route('refund_sticker_config') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <input type="hidden" name="type" value="refund_sticker">
                                <label class="col-form-label">{{ translate('Add Sticker') }}</label>

                                <div class="img-upload-container">
                                    <div class="input-group file-upload-input border border-dashed border-gray-400 rounded-1 w-120px h-120px d-flex align-items-center justify-content-center"
                                        data-toggle="aizuploader" data-type="image" data-multiple="false">
                                        <div
                                            class="form-control p-0 border-0 d-flex align-items-center justify-content-center">
                                            <img src="{{ static_asset('assets/img/plus-lg.svg') }}"
                                                class="w-40px h-40px w-md-64px h-md-64px" alt="generate Icon">
                                        </div>
                                        <input type="hidden" name="logo" class="selected-files" value="{{ get_setting('refund_sticker') }}">
                                    </div>
                                    <div class="file-preview box sm"></div>
                                </div>
                            </div>
                            <div class="form-group mb-0 text-right">
                                <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function updateSettings(el, type) {
            if ('{{ env('DEMO_MODE') }}' == 'On') {
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }
            var value = $(el).is(':checked') ? 1 : 0;

            $.post('{{ route('business_settings.update.activation') }}', {
                _token: '{{ csrf_token() }}',
                type: type,
                value: value
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Settings updated successfully') }}');
                    location.reload();
                } else {
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
        }

        $(document).ready(function() {

            // refund type toggle
            function toggleRefundCards() {
                let refundType = $('input[name="refund_type"]:checked').val();
                if (!refundType) {
                    $('#global-refund-time').hide();
                    $('#category-refund-time').hide();
                } else if (refundType === 'global_refund') {
                    $('#global-refund-time').show();
                    $('#category-refund-time').hide();
                } else {
                    $('#global-refund-time').hide();
                    $('#category-refund-time').show();
                }
            }

            toggleRefundCards();
            $('input[name="refund_type"]').on('change', toggleRefundCards);

            $(document).on('click', '#add-new-reason-btn, #add-new-reject-reason-btn', function () {

                let isCustomer = $(this).attr('id') === 'add-new-reason-btn';

                if (isCustomer) {
                    $('#new-reason-box').toggleClass('d-none');
                    $('#refund-reason-input').focus();
                } else {
                    $('#new-reject-reason-box').toggleClass('d-none');
                    $('#reject-reason-input').focus();
                }
            });

            let reasonErrorTimeout = null;

            $(document).on('input', '.refund-reason-input', function () {
                let input = $(this);

                input.removeClass('is-invalid');

                if (reasonErrorTimeout) {
                    clearTimeout(reasonErrorTimeout);
                }
            });

            $(document).on('keypress', '.refund-reason-input', function (e) {
                if (e.which === 13) {
                    e.preventDefault();

                    let input = $(this);
                    let reason = input.val().trim();
                    let type = input.data('type');

                    input.removeClass('is-invalid');

                    if (!reason) {
                        input.addClass('is-invalid');

                        AIZ.plugins.notify('danger', 'Reason cannot be empty!');

                        reasonErrorTimeout = setTimeout(() => {
                            input.removeClass('is-invalid');
                        }, 2000);

                        return;
                    }

                    if (reason.length > 120) {
                        input.addClass('is-invalid');

                        AIZ.plugins.notify('danger', 'Maximum 120 characters are allowed');

                        input.val(reason.substring(0, 120));

                        reasonErrorTimeout = setTimeout(() => {
                            input.removeClass('is-invalid');
                        }, 2000);

                        return;
                    }

                    $.ajax({
                        url: "{{ route('refund.reason.store.ajax') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            reason: reason,
                            type: type
                        },
                        success: function (res) {
                            if (res.success) {

                                let html = `
                                    <div class="mb-2">
                                        <div class="single-warranty-notes border border-2 border-gray-300 rounded-2 p-3 overflow-hidden has-transition">
                                            <p class="fs-14 fw-400 m-0 text-truncate-1">
                                                ${res.reason}
                                            </p>
                                        </div>
                                    </div>
                                `;

                                if (type === 'customer_refund_reason') {
                                    $('#refund-reason-list').append(html);
                                    $('#new-reason-box').addClass('d-none');
                                } else {
                                    $('#reject-reason-list').append(html);
                                    $('#new-reject-reason-box').addClass('d-none');
                                }

                                input.val('');
                                input.removeClass('is-invalid');

                                if (type === 'customer_refund_reason') {
                                    AIZ.plugins.notify('success', '{{ translate ('Customer Refund Reason Added successfully') }}');
                                }else{
                                    AIZ.plugins.notify('success', '{{ translate ('Admin/Seller Reject Refund Reason Added successfully')}}');
                                }
                            }
                        }
                    });
                }
            });

        });

        function clearField(button, type) {
            const container = button.closest('.d-flex');
            const input = container.querySelector('.refund-reason-input');
            input.value = '';
            input.focus();
        }
    </script>
@endsection