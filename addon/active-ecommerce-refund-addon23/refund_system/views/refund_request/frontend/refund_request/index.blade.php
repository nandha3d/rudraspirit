@extends('frontend.layouts.app')

@section('content')
@php
    $offcanvas_class = 'right-offcanvas-lg';
@endphp
<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-start">
            @include('frontend.inc.user_side_nav')
            <div class="aiz-user-panel">
                <div class="card shadow-none rounded-0 border p-4">
                    <h5 class="mb-2 fs-20 fw-700 text-dark">{{ translate('Applied Refund Requests') }}</h5>
                    <hr>
                    <div class="card-body py-0 pt-3 px-0">
                        <div class="mb-4">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-12">
                                    @foreach($refunds as $key => $refund)
                                    <div class="row">
                                        <div class="col-md-3 col-xl-4 d-flex align-items-center mb-1 mb-md-0">
                                            <div class="border rounded-0 mr-3 ">
                                                <img src="{{ uploaded_asset($refund->orderDetail->product->thumbnail_img) }}"
                                                    class="img-fit product-history-img w-30px h-30px w-sm-50px h-sm-50px w-md-48px h-md-48px overflow-hidden">
                                            </div>
                                            <div class="w-100 text-wrap">
                                                <div class="font-weight-semibold fs-14 product-name-color text-truncate-2"
                                                    title="{{ $refund->orderDetail->product->getTranslation('name') }}">
                                                    {{ $refund->orderDetail->product->getTranslation('name') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xl-3">
                                            <div>
                                                <span class="text-muted">{{ translate('Applied') }}:</span> 
                                                <span class="fw-bold">{{ date('d-m-Y', strtotime($refund->created_at)) }}</span>
                                            </div>
                                            <div class="text-muted">{{ translate('Order Code') }}</div>
                                            <div class="font-weight-bold"> <a class="text-blue" href="{{route('purchase_history.details', encrypt($refund->order->id))}}">{{$refund->order->code}}</a></div>
                                        </div>
                                        <div class="col-md-2 col-xl-2">
                                            <div class="text-muted">{{ translate('Amount') }}</div>
                                            <div class="font-weight-bold">{{single_price($refund->refund_amount)}}</div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 col-xl-1">
                                            <div class="text-muted">{{ translate('Channel') }}</div>
                                            <div class="font-weight-bold">{{ ucfirst($refund->preferred_payment_channel) }}</div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 mt-1 mt-lg-0">
                                            <div class="row">
                                                @if ($refund->dispute_refund_status == 1 || $refund->dispute_refund_status == 2 || $refund->dispute_refund_status == 3)
                                                    @if ($refund->dispute_refund_status == 2)
                                                        <div class="col-md-12 text-right">
                                                            <button type="button"
                                                                class="btn btn-success btn-sm text-white px-2 py-1 rounded-1 w-100px"
                                                                onclick="openRefundViewOffcanvas('{{ $refund->id }}')">
                                                                {{ translate('Approved') }}
                                                                <i class="las la-arrow-right ml-1"></i>
                                                            </button>
                                                        </div>
                                                    @elseif ($refund->dispute_refund_status == 3)    
                                                        <div class="col-md-12 text-right">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm text-white px-2 py-1 rounded-1 w-100px"
                                                                onclick="openRefundViewOffcanvas('{{ $refund->id }}')">
                                                                {{ translate('Rejected') }}
                                                                <i class="las la-arrow-right ml-1"></i>
                                                            </button>
                                                        </div>
                                                    @elseif($refund->dispute_refund_status == 1)
                                                        <div class="col-md-12 text-right">
                                                            <button type="button"
                                                                class="btn btn-info btn-sm text-white px-2 py-1 rounded-1 w-100px"
                                                                onclick="openRefundViewOffcanvas('{{ $refund->id }}')">
                                                                {{ translate('Pending') }}
                                                                <i class="las la-arrow-right ml-1"></i>
                                                            </button>
                                                        </div>
                                                    @endif 
                                                @else
                                                    @if ($refund->refund_status == 1)
                                                        <div class="col-md-12 text-right">
                                                            <button type="button"
                                                                class="btn btn-success btn-sm text-white px-2 py-1 rounded-1 w-100px"
                                                                onclick="openRefundViewOffcanvas('{{ $refund->id }}')">
                                                                {{ translate('Approved') }}
                                                                <i class="las la-arrow-right ml-1"></i>
                                                            </button>
                                                        </div>
                                                    @elseif ($refund->refund_status == 2)    
                                                        <div class="col-md-12 text-right">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm text-white px-2 py-1 rounded-1 w-100px"
                                                                onclick="openRefundViewOffcanvas('{{ $refund->id }}')">
                                                                {{ translate('Rejected') }}
                                                                <i class="las la-arrow-right ml-1"></i>
                                                            </button>
                                                            @if ($refund->refund_status == 2 && $refund->dispute_admin_approval == 0 && $refund->orderDetail->dispute_refund_days != 0 && $refund->dispute_refund_status == 0)
                                                                <span class="text-blue fs-10 pr-2">{{ translate('Dispute Available') }}</span>
                                                            @endif  
                                                        </div>
                                                    @else
                                                        <div class="col-md-12 text-right">
                                                            <button type="button"
                                                                class="btn btn-info btn-sm text-white px-2 py-1 rounded-1 w-100px"
                                                                onclick="openRefundViewOffcanvas('{{ $refund->id }}')">
                                                                {{ translate('Pending') }}
                                                                <i class="las la-arrow-right ml-1"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ $refunds->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('modal')
<div class="modal fade uploadModal" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

            <!-- Header -->
            <div class="modal-header bg-dark text-white py-2 rounded-0">
                <h6 class="modal-title mb-0">Image Preview</h6>
                <button type="button" class="close text-white fs-3 border-0 bg-transparent" data-dismiss="modal">
                    &times;
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body text-center bg-light p-3">
                <img id="modalImage"
                     src=""
                     class="img-fluid rounded shadow-sm"
                     style="max-height: 80vh;">
            </div>

        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">

    function openRefundViewOffcanvas(refundId) {
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
            url: "{{ route('customer_refund_request_view') }}",
            data: {
                _token: '{{ csrf_token() }}',
                refund_id: refundId
            },
            success: function(html) {
                if (rightOffcanvas) {
                    rightOffcanvas.innerHTML = html;

                    if (typeof AIZ !== 'undefined' && AIZ.extra && AIZ.extra.inputRating) {
                        AIZ.extra.inputRating();
                    }

                    if (typeof AIZ !== 'undefined' && AIZ.plugins && AIZ.plugins.aizUploader) {
                        AIZ.plugins.aizUploader();
                    }
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

    $(document).on('change', '.agreeCheckbox', function () {

        let parent = $(this).closest('.right-offcavas-footer');
        let button = parent.find('.disputeNowBtn');

        if ($(this).is(':checked')) {
            button.removeClass('disabled').css({
                'pointer-events': 'auto',
                'opacity': '1'
            });
        } else {
            button.addClass('disabled').css({
                'pointer-events': 'none',
                'opacity': '0.6'
            });
        }
    });

    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
        myModal.show();
    }
</script>
@endsection
