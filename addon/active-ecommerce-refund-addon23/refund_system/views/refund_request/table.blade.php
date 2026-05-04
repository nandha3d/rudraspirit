<div class="card-body">
    <table class="table mb-0" id="aiz-data-table">
        <thead>
            <tr>
                <th class="">#</th>
                <th class="text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Refund Code') }}
                </th>
                <th class="hide-md text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Product') }}
                </th>
                <th class="hide-xl text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Refund Amount') }}
                </th>
                <th class="hide-6xl text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Approval Status') }}
                </th>
                <th class="hide-xs text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Payment Status') }}
                </th>
                <th class="hide-s text-right text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Options') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($refund_requests as $key => $refund_request)
                <tr class="data-row">
                    <td class="align-middle h-40">
                        <div>
                            <button type="button"
                                class="toggle-plus-minus-btn border-0 bg-blue fs-12 fw-500 text-white p-0 align-items-center justify-content-center">+</button>
                        </div>
                        <div class="form-group d-inline-block w-40px">
                            {{ $key + 1 + ($refund_requests->currentPage() - 1) * $refund_requests->perPage() }}
                        </div>
                    </td>
                    <td class="align-middle" data-label="Order Code">
                        <div class="w-200px w-md-200px">
                            <span
                                class="text-dark fs-12 fw-700">
                                <a href="#" id="refund_request_view" data-user-id="{{ $refund_request->id }}">{{ $refund_request->refund_code }}</a>
                            </span><br>
                            <span
                                class="text-dark fs-12 fw-400">
                                @if ($refund_request->seller != null)
                                    {{ $refund_request->seller->name }}
                                @endif 
                            </span>
                        </div>
                    </td>
                    <td class="hide-md align-middle" data-label="Product">
                        <div class="row gutters-5 w-200px w-md-300px pr-4">
                            <div class="col">
                                <span class="text-dark fs-12 fw-400">
                                    @if ($refund_request->orderDetail != null && $refund_request->orderDetail->product != null)
                                        <a href="{{ route('product', $refund_request->orderDetail->product->slug) }}" target="_blank" class="media-block">
                                            <div class="row align-items-center">
                                                <div class="border rounded-0 w-30px h-30px w-sm-50px h-sm-50px w-md-48px h-md-48px overflow-hidden">
                                                    <img src="{{ uploaded_asset($refund_request->orderDetail->product->thumbnail_img) }}" alt="Image" class="img-fit">
                                                </div>
                                                <div class="col">
                                                    <div class="media-body text-truncate-1">{{ $refund_request->orderDetail->product->getTranslation('name') }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="hide-xl align-middle" data-label="Refund Amount">
                        <div class="w-200px w-md-200px">
                            <span
                                class="text-dark fs-12 fw-400">
                                @if ($refund_request->orderDetail != null)
                                    {{single_price($refund_request->refund_amount)}}
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="hide-6xl align-middle" data-label="Approval Status">
                        <div class="w-150px w-md-150px">
                            <div class="pb-1">
                                <span
                                    class="text-dark fs-12 fw-400">
                                    @if ($refund_request->orderDetail != null && $refund_request->orderDetail->product != null && $refund_request->orderDetail->product->added_by == 'admin')
                                        <span class="mr-2">{{translate('Seller')}}</span><span class="fs-12">{{translate('N/A')}}</span>
                                    @else
                                        @if ($refund_request->seller_approval == 1)
                                            <span class="mr-2">{{translate('Seller')}}</span><span class="badge badge-inline badge-success fs-11">{{translate('Approved')}}</span>
                                        @elseif ($refund_request->seller_approval == 2)
                                            <span class="mr-2">{{translate('Seller')}}</span><span class="badge badge-inline badge-danger fs-11">{{translate('Rejected')}}</span>
                                        @else
                                            <span class="mr-2">{{translate('Seller')}}</span><span class="badge badge-inline badge-info fs-11">{{translate('Pending')}}</span>
                                        @endif
                                    @endif
                                </span>
                            </div>    
                            <div> 
                                <span class="text-dark fs-12 fw-400"> 
                                    @if($refund_request->admin_approval == 0 && get_setting('seller_product_refund_approval') == 'seller_can_refund_directly' && $refund_request->orderDetail->product->added_by == 'seller')
                                        <span class="mr-2">{{translate('Admin')}}</span><span class="fs-12">{{translate('N/A')}}</span>
                                    @else
                                        @if ($refund_request->admin_approval == 1)
                                            <span class="mr-1">{{translate('Admin')}}</span><span class="badge badge-inline badge-success fs-11">{{translate('Approved')}}</span>
                                        @elseif($refund_request->admin_approval == 2)
                                            <span class="mr-1">{{translate('Admin')}}</span><span class="badge badge-inline badge-danger fs-11">{{translate('Rejected')}}</span>
                                        @else
                                            <span class="mr-1">{{translate('Admin')}}</span><span class="badge badge-inline badge-info fs-11">{{translate('Pending')}}</span>
                                        @endif    
                                    @endif
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="hide-xs align-middle" data-label="Payment Status">
                        <div class="w-150px w-md-150px">
                            <span class="text-dark fs-12 fw-400">
                                @if ($refund_request->preferred_payment_channel == 'offline')
                                    {{translate('offline')}}
                                @else
                                    {{translate('wallet')}}
                                @endif
                                <br>
                                @if ($refund_request->refund_status == 1)
                                    <span class="badge badge-inline badge-success fs-11">{{translate('Paid')}}</span>
                                @else
                                    <span class="badge badge-inline badge-warning fs-11">{{translate('Non-Paid')}}</span>
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="text-right hide-s align-middle" data-label="Options">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="dropdown float-right">
                                <button
                                    class="btn btn-light w-35px h-35px  action-toggle d-flex align-items-center justify-content-center p-0"
                                    type="button" data-toggle="dropdown" aria-haspopup="false"
                                    aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="3"
                                        height="16" viewBox="0 0 3 16">
                                        <g id="Group_38888" data-name="Group 38888"
                                            transform="translate(-1653 -342)">
                                            <circle id="Ellipse_1018" data-name="Ellipse 1018"
                                                cx="1.5" cy="1.5" r="1.5"
                                                transform="translate(1653 348.5)" />
                                            <circle id="Ellipse_1019" data-name="Ellipse 1019"
                                                cx="1.5" cy="1.5" r="1.5"
                                                transform="translate(1653 342)" />
                                            <circle id="Ellipse_1020" data-name="Ellipse 1020"
                                                cx="1.5" cy="1.5" r="1.5"
                                                transform="translate(1653 355)" />
                                        </g>
                                    </svg>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-sm">
                                    <div class="table-options">
                                        <a href="#" id="refund_request_view" data-user-id="{{ $refund_request->id }}"  title="{{ translate('Refund Details') }}"
                                            class="d-flex align-items-center px-20px py-10px hov-bg-light hov-text-blue text-dark">
                                            <span
                                                class="fs-12 fw-500 pl-10px">{{ translate('Refund Details') }}</span>
                                        </a>
                                        <a href="{{ route('all_orders.show', encrypt($refund_request->order->id)) }}" title="{{ translate('View Order') }}"
                                            class="d-flex align-items-center px-20px py-10px hov-bg-light hov-text-blue text-dark">
                                            <span
                                                class="fs-12 fw-500 pl-10px">{{ translate('View Order') }}</span>
                                        </a>
                                        @if ($refund_request->preferred_payment_channel == 'offline')
                                            <a href="#" id="view_payment_info" data-user-id="{{ $refund_request->id }}" title="{{ translate('View Payment Info') }}"
                                                class="d-flex align-items-center px-20px py-10px hov-bg-light hov-text-blue text-dark">
                                                <span
                                                    class="fs-14 fw-500 pl-10px">{{ translate('View Payment Info') }}</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center py-5">
                        <div class="w-100">
                            <h5 class="fs-16 fw-bold text-gray">{{ translate('No Data found!') }}</h5>
                            <i class="las la-frown fs-48 text-soft-white"></i>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="aiz-pagination">
        {{ $refund_requests->appends(request()->input())->links() }}
    </div>
</div>