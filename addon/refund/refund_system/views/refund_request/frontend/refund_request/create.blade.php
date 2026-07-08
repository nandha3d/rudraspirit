@extends('frontend.layouts.app')

@section('content')

    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')
                <div class="aiz-user-panel">
                    <div class="card rounded-0 shadow-none border">
                        <div class="card-header border-bottom-0">
                            <h5 class="mb-0 fs-20 fw-700 text-dark">{{translate('Send Refund Request')}}</h5>
                        </div>
                        <div class="card-body">
                            <form id="aizSubmitForm" action="{{route('refund_request_send', $order_detail->id)}}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap gap-3">
                                      <div class="d-flex align-items-center">
                                          <!-- Product Image -->
                                        <img class="w-80px h-80px border border-gray-400 mr-3"
                                            src="{{ uploaded_asset($order_detail->product->thumbnail_img) }}" 
                                            alt="">
                                      <div>
                                          <!-- Product Name -->
                                        <p class="mb-0 fs-14 fw-700 text-truncate w-200px w-md-250px">{{ $order_detail->product->getTranslation('name') }}</p>
                                        @if(is_numeric($order_detail->gst_amount))
                                            <p class="mb-0 text-gray">{{ single_price(round($order_detail->price + get_gst_by_price_and_rate($order_detail->price, $order_detail->gst_rate), 2)) }}</p>
                                        @else
                                            <p class="mb-0 text-gray">{{translate('Amount')}}: {{ single_price($order_detail->price + $order_detail->tax) }}</p>
                                        @endif
                                      </div>
                                      </div>
                                        <p class="mb-0"><a href="{{route('purchase_history.details', encrypt($order_detail->order->id))}}" class="text-blue fs-14 fw-700">{{translate('Order-code')}}: {{ $order_detail->order->code }}</a></p>
                                    </div>
                                </div>
                                <hr>

                                <input type="hidden" name="name" value="{{ $order_detail->product->getTranslation('name') }}">

                                @if(is_numeric($order_detail->gst_amount))
                                    <input type="hidden" name="amount" value="{{ round($order_detail->price + get_gst_by_price_and_rate($order_detail->price, $order_detail->gst_rate), 2) }}">
                                @else
                                    <input type="hidden" name="amount" value="{{ $order_detail->price + $order_detail->tax }}">
                                @endif

                                <input type="hidden" name="code" value="{{ $order_detail->order->code }}">

                                <div class="form-group mb-3">
                                    <label class="col-from-label">{{translate('Refund Reason')}} <span class="text-danger">*</span></label>
                                    <select id="reason_select" class="form-control aiz-selectpicker">
                                        <option value="">{{ ucfirst(translate('Select Reason')) }}</option>
                                        @foreach ($refund_reasons as $refund_reason)
                                            @php
                                                $translatedReason = ucfirst(translate($refund_reason->reason));
                                                $shortReason = \Illuminate\Support\Str::limit($translatedReason, 50, '...');
                                            @endphp
                                            <option value="{{ $refund_reason->id }}" 
                                                    title="{{ $translatedReason }}" 
                                                    data-content='<span title="{{ $translatedReason }}">{{ $shortReason }}</span>'>
                                                {{ $shortReason }}
                                            </option>
                                        @endforeach
                                        <option value="others">Others</option>
                                    </select>
                                    @error('reason')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="other_reason_box" style="display: none;">
                                    <textarea id="other_reason" rows="5" class="form-control rounded-0"
                                        placeholder="Write your reason..."></textarea>
                                    <span class="text-gray fs-10">(Max 120 Character)</span>
                                    <small id="reason_error" class="text-danger d-none">
                                        Maximum 120 characters are allowed
                                    </small>
                                </div>
                                <input type="hidden" name="reason" id="final_reason">

                                <div class="form-group mb-3">
                                    <label class="col-from-label">{{ translate('Image') }}</label> (<span class="fs-11">{{ translate('Max 6 files')}}</span>)
                                    <div class="bypass-img-upload-container">
                                        <div class="direct-uploader flex-shrink-0" data-direct-upload="true">
                                            <img src="{{ static_asset('assets/img/plus-lg.svg') }}" class="upload-icon">
                                            <input type="file" name="images[]" accept="image/*" multiple>
                                        </div>
                                        <div class="file-preview box sm direct-preview"></div>
                                    </div>
                                    <div class="upload-error text-danger mt-1"></div>
                                    @error('images')
                                        <span class="text-danger d-block">{{ $message }}</span>
                                    @enderror

                                    @error('images.*')
                                        <span class="text-danger d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                @if (addon_is_activated('offline_payment') && addon_is_activated('refund_request'))
                                    <input type="hidden" name="payment_information_id" id="payment_information_id" value="{{ $payment_information_id }}">
                                    <div class="form-group">
                                        <label class="col-from-label">
                                            {{ translate('Preferred Channel') }} <span class="text-danger">*</span>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <label class="aiz-megabox d-block bg-white mb-0 mr-4" style="flex: 1; min-width: 120px;"> 
                                                <input type="radio" name="preferred_payment_channel" value="wallet" checked>
                                                <span class="d-flex align-items-center aiz-megabox-elem rounded-0"
                                                    style="padding: 0.75rem 1.2rem;">
                                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                    <span class="flex-grow-1 pl-3 fw-600">{{ translate('Wallet') }}</span>
                                                </span>
                                            </label>
                                            <label class="aiz-megabox d-block bg-white mb-0" style="flex: 1; min-width: 120px;">
                                                <input type="radio" name="preferred_payment_channel" value="offline">
                                                <span class="d-flex align-items-center aiz-megabox-elem rounded-0"
                                                    style="padding: 0.75rem 1.2rem;">
                                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                    <span class="flex-grow-1 pl-3 fw-600">{{ translate('Offline') }}</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3 mb-3" id="paymentInformationSection" style="display: none;">
                                        <label class="col-from-label">
                                            {{translate('Select Payment Information')}} <span class="text-danger">*</span>
                                        </label>
                                        <div class="card mb-0 rounded-0 border shadow-none">
                                            <div id="collapsePaymentInformation" class="collapse show">
                                                <div class="card-body">
                                                    <div id="refund-payment-context">
                                                        @include('frontend.partials.payment_information.payment_info', ['payment_information_id' => $payment_information_id])
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group mb-0 text-right ">
                                    <button type="submit" class="btn btn-primary rounded-0 w-150px">{{translate('Send Request')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
    <script>
        $('#aizSubmitForm').on('submit', function (e) {

            let selected = $('#reason_select').val();
            let reason = $('#other_reason').val().trim();

            if (selected === 'others') {

                if (reason === '') {
                    e.preventDefault();
                    alert('Please write your reason');
                    return;
                }

                if (reason.length > 120) {
                    e.preventDefault();
                    $('#reason_error').removeClass('d-none');
                    return;
                }
            }

            let $btn = $(this).find('button[type="submit"]');

            $btn.prop('disabled', true);
            $btn.html(`
                <span class="spinner-border spinner-border-sm mr-2"></span>
                Sending...
            `);
        });
    </script>

    <script>
        $(document).ready(function () {

            $('#reason_select').on('change', function () {
                let value = $(this).val();

                if (value === 'others') {
                    $('#other_reason_box').slideDown(200);
                    $('#final_reason').val('');
                } else {
                    $('#other_reason_box').slideUp(200);

                    $('#final_reason').val(value);
                }
            });

            $('#other_reason').on('keyup', function () {
                $('#final_reason').val($(this).val());
            });

        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('input[name="preferred_payment_channel"][value="wallet"]').prop('checked', true);
            $('#paymentInformationSection').hide();
            $('input[name="preferred_payment_channel"]').on('change', function() {
                if ($(this).val() === 'offline') {
                    $('#paymentInformationSection').slideDown();
                    if ($('input[name="single_payment_infomation_id"]:checked').length === 0) {
                        $('input[name="single_payment_infomation_id"]').first().prop('checked', true);
                    }
                } else if ($(this).val() === 'wallet') {
                    $('#payment_information_id').val('');
                    $('#paymentInformationSection').slideUp();
                }
            });

        });

        function openPaymentInfomrationAddOffcanvas() {
            const rightOffcanvas = document.getElementById('rightOffcanvas');
            const overlay = document.getElementById('rightOffcanvasOverlay');
            
            if (rightOffcanvas) rightOffcanvas.classList.add('active');
            if (overlay) overlay.classList.add('active');
            document.body.classList.add('body-no-scroll');
            
            if (rightOffcanvas) {
                rightOffcanvas.innerHTML = '<div class="footable-loader mt-5"><span class="fooicon fooicon-loader"></span></div>';
            }
            
            $.ajax({
                type: "POST",
                url: "{{ route('ajax_payment_informations.create') }}",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(html) {
                    if (rightOffcanvas) {
                        rightOffcanvas.innerHTML = html;
                    }
                },
                error: function() {
                    if (rightOffcanvas) {
                        rightOffcanvas.innerHTML = '<div class="p-4 text-center text-danger">{{ translate("Failed to load") }}</div>';
                    }
                    AIZ.plugins.notify('danger', '{{ translate("Something went wrong") }}');
                }
            });
        }

        $(document).on('change', 'select[name="payment_type"]', function () {
            let type = $(this).val();

            if (type === 'bank_transfer') {
                $('.bank-fields').removeClass('d-none');
                $('.basic-fields').addClass('d-none');
            } else if (type === 'others') {
                $('.basic-fields').removeClass('d-none');
                $('.bank-fields').addClass('d-none');
            } else {
                $('.basic-fields, .bank-fields').addClass('d-none');
            }
        });

        $(document).on('change', 'select[name="bank_name"]', function() {
            let value = $(this).val();

            if (value === 'other_bank') {
                $('#other-bank-name-field').removeClass('d-none');
            } else {
                $('#other-bank-name-field').addClass('d-none');
                $('#other-bank-name-field input').val(''); 
            }
        });

        $(document).on('change', 'select[name="payment_name"]', function() {
            let value = $(this).val();

            if (value === 'other_method') {
                $('#other-methods-field').removeClass('d-none');
            } else {
                $('#other-methods-field').addClass('d-none');
                $('#other-methods-field input').val(''); 
            }
        });

        $(document).on('click', '#create-payment-information', function () {
            const btn = $(this);
            if (!validatePaymentForm()) {
                return;
            }
            btn.prop('disabled', true);

            if (!btn.find('.spinner-border').length) {
                btn.append('<span class="spinner-border spinner-border-sm ms-2"></span>');
            }

            let formData = {
                _token: AIZ.data.csrf,
                payment_type: $('select[name="payment_type"]').val(),
                payment_name: $('select[name="payment_name"]').val(),
                other_payment_method: $('input[name="other_payment_method"]').val(),
                payment_instructions: $('textarea[name="payment_instructions"]').val(),
                bank_name: $('select[name="bank_name"]').val(),
                other_bank_name: $('input[name="other_bank_name"]').val(),
                account_name: $('input[name="account_name"]').val(),
                account_number: $('input[name="account_number"]').val(),
                routing_number: $('input[name="routing_number"]').val(),
            };

            $.ajax({
                url: "{{ route('ajax_payment_informations.store') }}",
                type: "POST",
                data: formData,

                success: function (res) {
                    AIZ.plugins.notify('success', 'Payment Information added successfully');
                    closeOffcanvas();
                    reloadPaymentInfoSection();
                },

                error: function (xhr) {
                    AIZ.plugins.notify('danger', 'Something went wrong');
                    btn.prop('disabled', false);
                    btn.find('.spinner-border').remove();

                    console.log(xhr.responseText);
                }
            });
        });

        window.openPaymentInfomrationEditOffcanvas = function(paymentInformationId) {
            const rightOffcanvas = document.getElementById('rightOffcanvas');
            const overlay = document.getElementById('rightOffcanvasOverlay');
            
            if (rightOffcanvas) rightOffcanvas.classList.add('active');
            if (overlay) overlay.classList.add('active');
            document.body.classList.add('body-no-scroll');
            
            if (rightOffcanvas) {
                rightOffcanvas.innerHTML = '<div class="footable-loader mt-5"><span class="fooicon fooicon-loader"></span></div>';
            }
            
            $.ajax({
                type: "POST",
                url: "{{ route('ajax_payment_informations.edit') }}",
                data: {
                    _token: '{{ csrf_token() }}',
                    payment_information_id: paymentInformationId 
                },
                success: function(html) {
                    if (rightOffcanvas) {
                        rightOffcanvas.innerHTML = html;
                        setTimeout(function () {
                            $('select[name="payment_type"]').trigger('change');
                            $('select[name="bank_name"]').trigger('change');
                            $('select[name="payment_name"]').trigger('change');
                        }, 100);
                    }
                },
                error: function() {
                    if (rightOffcanvas) {
                        rightOffcanvas.innerHTML = '<div class="p-4 text-center text-danger">{{ translate("Failed to load") }}</div>';
                    }
                    AIZ.plugins.notify('danger', '{{ translate("Something went wrong") }}');
                }
            });
        };

        $(document).on('click', '#edit-payment-information', function () {
            const btn = $(this);
            const paymentInformationId = btn.data('id'); 
            if (!validatePaymentForm()) {
                return;
            }
            btn.prop('disabled', true);

            if (!btn.find('.spinner-border').length) {
                btn.append('<span class="spinner-border spinner-border-sm ms-2"></span>');
            }

            let formData = {
                _token: AIZ.data.csrf,
                payment_information_id: paymentInformationId, 
                payment_type: $('select[name="payment_type"]').val(),
                payment_name: $('select[name="payment_name"]').val(),
                other_payment_method: $('input[name="other_payment_method"]').val(),
                payment_instructions: $('textarea[name="payment_instructions"]').val(),
                bank_name: $('select[name="bank_name"]').val(),
                other_bank_name: $('input[name="other_bank_name"]').val(),
                account_name: $('input[name="account_name"]').val(),
                account_number: $('input[name="account_number"]').val(),
                routing_number: $('input[name="routing_number"]').val(),
            };

            $.ajax({
                url: "{{ route('ajax_payment_informations.update') }}",
                type: "POST",
                data: formData,

                success: function (res) {
                    AIZ.plugins.notify('success', 'Payment Information added successfully');
                    closeOffcanvas();
                    reloadPaymentInfoSection();
                },

                error: function (xhr) {
                    AIZ.plugins.notify('danger', 'Something went wrong');
                    btn.prop('disabled', false);
                    btn.find('.spinner-border').remove();

                    console.log(xhr.responseText);
                }
            });
        });

        function reloadPaymentInfoSection() {
            $.ajax({
                url: "{{ route('ajax_payment_informations.list') }}",
                type: "GET",
                success: function (html) {
                    $('#refund-payment-context').html(html);
                },
                error: function () {
                    AIZ.plugins.notify('danger', 'Failed to reload payment info');
                }
            });
        }

        $(document).on('change', 'input[name="single_payment_infomation_id"]', function () {
            $('#payment_information_id').val($(this).val());
        });

        $(document).ready(function () {

            const maxLength = 120;

            $('#other_reason').on('keyup', function () {
                let value = $(this).val();

                $('#final_reason').val(value);

                if (value.length > maxLength) {
                    $('#reason_error').removeClass('d-none');
                } else {
                    $('#reason_error').addClass('d-none');
                }
            });

        });

        function validatePaymentForm() {
            const paymentType = $('select[name="payment_type"]').val();

            if (!paymentType) {
                AIZ.plugins.notify('danger', 'Please select a payment type.');
                return false;
            }

            if (paymentType === 'bank_transfer') {
                const bankName = $('select[name="bank_name"]').val();

                if (!bankName) {
                    AIZ.plugins.notify('danger', 'Please select a bank name.');
                    return false;
                }

                if (bankName === 'other_bank') {
                    const otherBankName = $('input[name="other_bank_name"]').val().trim();
                    if (!otherBankName) {
                        AIZ.plugins.notify('danger', 'Please enter the bank name.');
                        return false;
                    }
                }

                if (!$('input[name="account_name"]').val().trim()) {
                    AIZ.plugins.notify('danger', 'Please enter the account name.');
                    return false;
                }

                if (!$('input[name="account_number"]').val().trim()) {
                    AIZ.plugins.notify('danger', 'Please enter the account number.');
                    return false;
                }

                if (!$('input[name="routing_number"]').val().trim()) {
                    AIZ.plugins.notify('danger', 'Please enter the routing number.');
                    return false;
                }
            }

            if (paymentType === 'others') {
                const paymentName = $('select[name="payment_name"]').val();

                if (!paymentName) {
                    AIZ.plugins.notify('danger', 'Please select a payment method.');
                    return false;
                }

                if (paymentName === 'other_method') {
                    const otherMethod = $('input[name="other_payment_method"]').val().trim();
                    if (!otherMethod) {
                        AIZ.plugins.notify('danger', 'Please enter the payment method name.');
                        return false;
                    }
                }

                if (!$('textarea[name="payment_instructions"]').val().trim()) {
                    AIZ.plugins.notify('danger', 'Please enter the payment instructions.');
                    return false;
                }
            }

            return true;
        }
    </script>
@endsection