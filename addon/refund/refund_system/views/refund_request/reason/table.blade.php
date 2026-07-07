<div class="card-body">
    <table class="table mb-0" id="aiz-data-table">
        <thead>
            <tr>
                @if (auth()->user()->can('delete_refund_reason'))
                    <th>
                        <div class="form-group">
                            <div class="aiz-checkbox-inline">
                                <label class="aiz-checkbox pt-5px d-block">
                                    <input type="checkbox" class="check-all">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </div>
                    </th>
                @else
                    <th class="hide-lg">#</th>
                @endif
                <th class="text-uppercase fs-10 fs-md-12 fw-700 text-secondary">{{ translate('Type') }}
                </th>
                <th class=" hide-sm text-uppercase fs-10 fs-md-12 fw-700 text-secondary">{{ translate('Reason') }}
                </th>
                <th class="hide-sm text-uppercase fs-10 fs-md-12 fw-700 text-secondary">{{ translate('Status') }}
                </th>
                <th class="text-right text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Options') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($refund_reasons as $key => $refund_reason)
                <tr class="data-row">
                    <td class="align-middle w-40px">
                        <div>
                            <button type="button"
                                class="toggle-plus-minus-btn border-0 bg-blue fs-14 fw-500 text-white p-0 align-items-center justify-content-center">+</button>
                        </div>
                        @if (auth()->user()->can('delete_refund_reason'))
                        <div class="form-group d-inline-block">
                            <label class="aiz-checkbox">
                                <input type="checkbox" class="check-one" name="id[]" value="{{ $refund_reason->id }}">
                                <span class="aiz-square-check"></span>
                            </label>
                        </div>
                        @else
                        <div class="form-group d-inline-block">
                        {{ $key + 1 + ($refund_reasons->currentPage() - 1) * $refund_reasons->perPage() }}
                        </div>
                        @endif
                    </td>
                    <td class="align-middle" data-label="Type">
                        <div class="row gutters-5">
                            <div class="col">
                                <span
                                    class="text-dark fs-14 fw-400">{{ ucwords(str_replace('_', ' ', $refund_reason->getTranslation('type'))) }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle hide-sm" data-label="Reason">
                        <div class="row gutters-5 w-350px w-md-350px mw-350">
                            <div class="col">
                                <span class="text-dark fs-14 fw-400 text-truncate-2">
                                    {{$refund_reason->getTranslation('reason')}}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle hide-sm" data-label="Status">
                        <div class="row gutters-5">
                            <div class="col">
                                <label class="aiz-switch aiz-switch-primary mb-0">
                                    <input
                                        @can('can_update_refund_reason_status') onchange="trigger_alert(this)" @endcan
                                        value="{{ $refund_reason->id }}" id="trigger_alert_{{ $refund_reason->id }}" type="checkbox" @if($refund_reason->status == 1) checked @endif
                                        @cannot('can_update_refund_reason_status') disabled @endcan
                                    >
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </td>
                    <td class="text-right align-middle" data-label="Options">
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
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-xs">
                                <div class="table-options">
                                    <!--Edit-->
                                    @can('edit_refund_reason')
                                    <a href="{{route('refund_reason_edit', ['id'=>$refund_reason->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}"
                                        class="d-flex align-items-center px-20px py-10px hov-bg-light hov-text-blue">
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11.985"
                                                height="12" viewBox="0 0 11.985 12">
                                                <path
                                                    id="edit_square_24dp_9393A3_FILL0_wght400_GRAD0_opsz24"
                                                    d="M121.2-909a1.154,1.154,0,0,1-.846-.352A1.154,1.154,0,0,1,120-910.2v-8.39a1.154,1.154,0,0,1,.352-.846,1.154,1.154,0,0,1,.846-.352h3.91a.541.541,0,0,1,.449.187.645.645,0,0,1,.15.412.626.626,0,0,1-.157.412.563.563,0,0,1-.457.187h-3.9v8.39h8.39v-3.91a.541.541,0,0,1,.187-.449.645.645,0,0,1,.412-.15.645.645,0,0,1,.412.15.541.541,0,0,1,.187.449v3.91a1.154,1.154,0,0,1-.352.846,1.154,1.154,0,0,1-.846.352ZM125.393-914.393Zm-1.8,1.2v-1.453a1.183,1.183,0,0,1,.09-.457,1.165,1.165,0,0,1,.255-.382l5.154-5.154a1.2,1.2,0,0,1,.4-.27,1.2,1.2,0,0,1,.449-.09,1.183,1.183,0,0,1,.457.09,1.219,1.219,0,0,1,.4.27l.839.854a1.347,1.347,0,0,1,.255.4,1.147,1.147,0,0,1,.09.442,1.237,1.237,0,0,1-.082.442,1.122,1.122,0,0,1-.262.4l-5.154,5.154a1.27,1.27,0,0,1-.382.262,1.1,1.1,0,0,1-.457.1h-1.453a.58.58,0,0,1-.427-.172A.58.58,0,0,1,123.6-913.195Zm7.206-5.753-.839-.839Zm-6.007,5.154h.839l3.476-3.476-.419-.419-.434-.419-3.461,3.461Zm3.9-3.9-.434-.419.434.419.419.419Z"
                                                    transform="translate(-120 921)"
                                                    fill="#414141" />
                                            </svg>
                                        </span>
                                        <span  class="fs-14 text-secondary fw-500 pl-10px">{{translate('Edit')}}</span>
                                    </a>
                                    @endcan
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
    <div class="aiz-pagination" id="pagination">
        {{ $refund_reasons->links() }}
    </div>
</div>