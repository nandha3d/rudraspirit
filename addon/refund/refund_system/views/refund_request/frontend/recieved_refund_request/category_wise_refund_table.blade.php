<div class="card-body">
    <table class="table mb-0" id="aiz-data-table">
        <thead>
            <tr>
                <th>#</th>
                <th class="hide-xs text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Icon') }}
                </th>
                <th class="text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Name') }}
                </th>
                <th class="hide-md text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Parent') }}
                </th>
                <th class="hide-xs text-uppercase fs-10 fs-md-12 fw-700 text-secondary">
                    {{ translate('Refund Request Time(Days)') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $key => $category)
                @php
                    $isCategoryBasedRefund = get_setting('refund_type') == 'category_based_refund';
                @endphp
                <tr class="data-row">
                    <td class="align-middle w-100px">
                        <div class="form-group d-inline-block">
                            {{ $key + 1 + ($categories->currentPage() - 1) * $categories->perPage() }}
                        </div>
                    </td>
                    <td class="hide-xs align-middle" data-label="Icon">
                        <div class="w-200px w-md-200px">
                            @if($category->icon != null)
                                <span class="avatar avatar-square avatar-sm border border-gray-400">
                                    <img src="{{ uploaded_asset($category->icon) }}" alt="{{translate('icon')}}">
                                </span>
                            @else
                                —
                            @endif
                        </div>
                    </td>
                    <td class="align-middle" data-label="Name">
                        <div class="row gutters-5 w-200px w-md-200px pr-4">
                            <div class="col">
                                <span class="text-dark fs-14 fw-400">
                                    {{ $category->getTranslation('name') }}
                                    @if($category->digital == 1)
                                        <img src="{{ static_asset('assets/img/digital_tag.png') }}" alt="{{translate('Digital')}}"
                                            class="ml-2 h-25px" style="cursor: pointer;" title="Digital">
                                    @endif
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="hide-md align-middle" data-label="Parent">
                        <div class="w-200px w-md-200px">
                            <span
                                class="text-dark fs-14 fw-400">
                                @php
                                    $parent = \App\Models\Category::where('id', $category->parent_id)->first();
                                @endphp
                                @if ($parent != null)
                                    {{ $parent->getTranslation('name') }}
                                @else
                                    —
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="hide-xs align-middle" data-label="Refund Request Time(Days)">
                        {{ $category->refund_request_time ?? 0}}
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
        {{ $categories->appends(request()->input())->links() }}
    </div>
</div>