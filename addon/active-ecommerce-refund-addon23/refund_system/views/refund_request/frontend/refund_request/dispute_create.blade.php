@extends('frontend.layouts.app')

@section('content')

    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')
                <div class="aiz-user-panel">
                    <div class="card rounded-0 shadow-none border">
                        <div class="card-header border-bottom-0">
                            <h5 class="mb-0 fs-20 fw-700 text-dark">{{translate('Send Dispute Refund Request')}}</h5>
                        </div>
                        <div class="card-body">
                            <form id="aizSubmitForm" action="{{route('dispute_refund_request_send', $refund->id)}}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap gap-3">
                                      <div class="d-flex align-items-center">
                                          <!-- Product Image -->
                                        <img class="w-80px h-80px border border-gray-400 mr-2"
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

                                <div class="form-group mb-3">
                                    <label class="col-from-label">{{ translate('Image') }}</label> (<span class="fs-11">{{ translate('Max 6 files')}}</span>)
                                    <div class="bypass-img-upload-container">
                                        <div class="direct-uploader flex-shrink-0" data-direct-upload="true">
                                            <img src="{{ static_asset('assets/img/plus-lg.svg') }}" class="upload-icon">
                                            <input type="file" name="dispute_images[]" accept="image/*" multiple>
                                        </div>
                                        <div class="file-preview box sm direct-preview"></div>
                                    </div>
                                    <div class="upload-error text-danger mt-1"></div>
                                    @error('dispute_images')
                                        <span class="text-danger d-block">{{ $message }}</span>
                                    @enderror

                                    @error('dispute_images.*')
                                        <span class="text-danger d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="col-from-label">{{translate('Dispute Refund Reason')}} <span class="text-danger">*</span></label>
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
    </script>
@endsection    