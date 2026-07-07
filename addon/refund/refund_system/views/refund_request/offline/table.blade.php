<div class="card-body">
    <table class="table mb-0" id="aiz-data-table">
        <thead>
            <tr>
                @if (auth()->user()->can('delete_payout_method_for_customer'))
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
                    <th class="">#</th>
                @endif
                <th class="hide-sm text-uppercase fs-10 fs-md-12 fw-700 text-secondary">{{ translate('Logo') }}</th>
                <th class="hide-sm text-uppercase fs-10 fs-md-12 fw-700 text-secondary">{{ translate('Type') }}</th>
                <th class="text-uppercase fs-10 fs-md-12 fw-700 text-secondary">{{ translate('Name') }}</th>
                <th class="text-right text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Options') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payout_methods as $key => $payout_method)
                <tr class="data-row">
                    <td class="align-middle w-40px">
                        <div>
                            <button type="button"
                                class="toggle-plus-minus-btn border-0 bg-blue fs-14 fw-500 text-white p-0 align-items-center justify-content-center">+</button>
                        </div>
                        @if (auth()->user()->can('delete_payout_method'))
                            <div class="form-group d-inline-block">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-one" name="id[]" value="{{ $payout_method->id }}">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        @else
                            <div class="form-group d-inline-block">
                                {{ $key + 1 + ($payout_methods->currentPage() - 1) * $payout_methods->perPage() }}
                            </div>
                        @endif
                    </td>
                    <td class="align-middle hide-sm" data-label="Logo">
                        <div class="row gutters-5 w-100px w-md-100px mw-100">
                            <div class="col">
                                <span class="text-dark fs-14 fw-700">
                                    <div class="border rounded-0 w-30px h-30px w-sm-50px h-sm-50px w-md-48px h-md-48px overflow-hidden">
                                        <img src="{{ uploaded_asset($payout_method->image) }}" alt="Image" class="img-fit">
                                    </div>
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle hide-sm" data-label="Type">
                        <div class="row gutters-5 w-200px w-md-200px mw-200">
                            <div class="col">
                                <span
                                    class="text-dark text-truncate-2 fs-14 fw-300">{{ ucwords(str_replace('_', ' ', $payout_method->type)) }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle" data-label="Name">
                        <div class="row gutters-5 w-100px w-md-100px mw-100">
                            <div class="col">
                                <span
                                    class="text-dark text-truncate-2 fs-14 fw-300">{{ ucwords(str_replace('_', ' ', $payout_method->name)) }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="text-right align-middle" data-label="Options">
                        <div class="dropdown float-right">
                            <button
                                class="btn btn-light w-35px h-35px  action-toggle d-flex align-items-center justify-content-center p-0"
                                type="button" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="3" height="16" viewBox="0 0 3 16">
                                    <g id="Group_38888" data-name="Group 38888" transform="translate(-1653 -342)">
                                        <circle id="Ellipse_1018" data-name="Ellipse 1018" cx="1.5" cy="1.5" r="1.5"
                                            transform="translate(1653 348.5)" />
                                        <circle id="Ellipse_1019" data-name="Ellipse 1019" cx="1.5" cy="1.5" r="1.5"
                                            transform="translate(1653 342)" />
                                        <circle id="Ellipse_1020" data-name="Ellipse 1020" cx="1.5" cy="1.5" r="1.5"
                                            transform="translate(1653 355)" />
                                    </g>
                                </svg>

                            </button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-xs">
                                <div class="table-options">
                                    <!--Edit-->
                                    @can('edit_payout_method_for_customer')
                                        <a href="{{route('payout_method_for_customer_edit', ['id' => $payout_method->id, 'lang' => env('DEFAULT_LANGUAGE')])}}"
                                            class="d-flex align-items-center px-20px py-10px hov-bg-light hov-text-blue">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="11.985" height="12"
                                                    viewBox="0 0 11.985 12">
                                                    <path id="edit_square_24dp_9393A3_FILL0_wght400_GRAD0_opsz24"
                                                        d="M121.2-909a1.154,1.154,0,0,1-.846-.352A1.154,1.154,0,0,1,120-910.2v-8.39a1.154,1.154,0,0,1,.352-.846,1.154,1.154,0,0,1,.846-.352h3.91a.541.541,0,0,1,.449.187.645.645,0,0,1,.15.412.626.626,0,0,1-.157.412.563.563,0,0,1-.457.187h-3.9v8.39h8.39v-3.91a.541.541,0,0,1,.187-.449.645.645,0,0,1,.412-.15.645.645,0,0,1,.412.15.541.541,0,0,1,.187.449v3.91a1.154,1.154,0,0,1-.352.846,1.154,1.154,0,0,1-.846.352ZM125.393-914.393Zm-1.8,1.2v-1.453a1.183,1.183,0,0,1,.09-.457,1.165,1.165,0,0,1,.255-.382l5.154-5.154a1.2,1.2,0,0,1,.4-.27,1.2,1.2,0,0,1,.449-.09,1.183,1.183,0,0,1,.457.09,1.219,1.219,0,0,1,.4.27l.839.854a1.347,1.347,0,0,1,.255.4,1.147,1.147,0,0,1,.09.442,1.237,1.237,0,0,1-.082.442,1.122,1.122,0,0,1-.262.4l-5.154,5.154a1.27,1.27,0,0,1-.382.262,1.1,1.1,0,0,1-.457.1h-1.453a.58.58,0,0,1-.427-.172A.58.58,0,0,1,123.6-913.195Zm7.206-5.753-.839-.839Zm-6.007,5.154h.839l3.476-3.476-.419-.419-.434-.419-3.461,3.461Zm3.9-3.9-.434-.419.434.419.419.419Z"
                                                        transform="translate(-120 921)" fill="#414141" />
                                                </svg>
                                            </span>
                                            <span class="fs-14 text-secondary fw-500 pl-10px">{{translate('Edit')}}</span>
                                        </a>
                                    @endcan
                                    <!--Delete-->
                                    @can('delete_payout_method_for_customer')
                                        <a href="javascript:void(0)"
                                            class="d-flex align-items-center px-20px py-10px hov-bg-light hov-text-blue"
                                            onclick="singleDelete({{$payout_method->id}})" title="{{ translate('Delete') }}">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10.667" height="12"
                                                    viewBox="0 0 10.667 12">
                                                    <path id="Path_45219" data-name="Path 45219"
                                                        d="M162-828a1.284,1.284,0,0,1-.942-.392,1.284,1.284,0,0,1-.392-.942V-838H160v-1.333h3.333V-840h4v.667h3.333V-838H170v8.667a1.284,1.284,0,0,1-.392.942,1.284,1.284,0,0,1-.942.392Zm6.667-10H162v8.667h6.667Zm-5.333,7.333h1.333v-6h-1.333Zm2.667,0h1.333v-6H166ZM162-838v0Z"
                                                        transform="translate(-160 840)" fill="#dc3545" />
                                                </svg>
                                            </span>
                                            <span class="fs-14 text-danger fw-500 pl-10px">{{translate('Delete')}}</span>
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
        {{ $payout_methods->links() }}
    </div>
</div>