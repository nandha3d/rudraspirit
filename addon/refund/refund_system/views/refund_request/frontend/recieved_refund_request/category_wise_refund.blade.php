@extends('seller.layouts.app')

@section('panel_content')

    @php
        CoreComponentRepository::instantiateShopRepository();
        CoreComponentRepository::initializeCache();
    @endphp
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{translate('Category Wise Product Refund')}}</h1>
            </div>
        </div>
    </div>
    <div class="card">
        <!--Nav Tab -->
        <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom  border-light px-25px table-nav-tabs pb-3 pb-xl-0">
            <div class="table-tabs-container flex-grow-1">
                @php
                    $active_tab = $active_tab ?? 'all-categories';
                @endphp
                <ul class="nav nav-tabs border-0 " id="myTab" role="tablist">
                    @foreach ($category_tabs as $category_tab)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-0 pb-15px fs-14 fw-500 {{ $active_tab == Str::slug($category_tab) ? 'active' : '' }}" data-toggle="tab"  role="tab" aria-selected="{{ $active_tab == Str::slug($category_tab) ? 'true' : 'false' }}"
                            id="{{ Str::slug($category_tab) }}-tab"  onclick="changeTab(this, '{{ Str::slug($category_tab) }}')" aria-controls="{{ Str::slug($category_tab) }}">
                            {{ translate($category_tab) }}
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="">
                <a onclick="unassigned_categories()" class="position-relative overflow-hidden add-new-btn">
                    <span
                        class="position-relative z-2 pr-15px fs-14 fw-500 text-danger label-text">{{ translate('Filter Unassigned') }}</span>
                    <span
                        class="position-absolute top-0 right-0 h-100 w-40px bg-danger d-flex align-items-center justify-content-end z-1 plus-icon-container m-0 p-0 rounded-pill">
                        <svg id="filter-icon" xmlns="http://www.w3.org/2000/svg" width="18.667" height="12"
                            viewBox="0 0 18.667 12">
                            <path id="Path_45233" data-name="Path 45233"
                                d="M151.667-684a.971.971,0,0,1-.713-.286.959.959,0,0,1-.287-.708.978.978,0,0,1,.287-.714.961.961,0,0,1,.713-.292H155a.971.971,0,0,1,.712.286.959.959,0,0,1,.288.708.979.979,0,0,1-.288.714A.96.96,0,0,1,155-684Zm-4-5a.971.971,0,0,1-.713-.286.959.959,0,0,1-.287-.708.978.978,0,0,1,.287-.714.961.961,0,0,1,.713-.292H159a.971.971,0,0,1,.712.286.959.959,0,0,1,.288.708.978.978,0,0,1-.288.714A.96.96,0,0,1,159-689ZM145-694a.971.971,0,0,1-.712-.286.959.959,0,0,1-.288-.708.979.979,0,0,1,.288-.714A.961.961,0,0,1,145-696h16.667a.971.971,0,0,1,.712.286.959.959,0,0,1,.288.708.979.979,0,0,1-.288.714.96.96,0,0,1-.712.292Z"
                                transform="translate(-144 696)" fill="#fff" />
                        </svg>
                    </span>
                </a>
            </div>
        </div>
        <!--Card Header (Search) Start-->
        <div class="tab-filter-bar">
            <form class="" id="sort_categories" action="" method="GET">
                <div class="card-header row gutters-10 border-0 pb-0 mt-2">
                    <div class="col-12">
                        <div class="input-group mb-0 border border-light px-3 bg-light rounded-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0 bg-transparent px-0" id="search">
                                    <svg id="Group_38844" data-name="Group 38844" xmlns="http://www.w3.org/2000/svg"
                                        width="16.001" height="16" viewBox="0 0 16.001 16">
                                        <path id="Path_3090" data-name="Path 3090"
                                            d="M8.248,14.642a6.394,6.394,0,1,1,6.394-6.394A6.4,6.4,0,0,1,8.248,14.642Zm0-11.509a5.115,5.115,0,1,0,5.115,5.115A5.121,5.121,0,0,0,8.248,3.133Z"
                                            transform="translate(-1.854 -1.854)" fill="#a5a5b8" />
                                        <path id="Path_3091" data-name="Path 3091"
                                            d="M23.011,23.651a.637.637,0,0,1-.452-.187l-4.92-4.92a.639.639,0,0,1,.9-.9l4.92,4.92a.639.639,0,0,1-.452,1.091Z"
                                            transform="translate(-7.651 -7.651)" fill="#a5a5b8" />
                                    </svg>
                                </span>
                            </div>
                            <input type="text" class="form-control form-control-sm border-0 px-2 bg-transparent"
                                id="search_input" name="search"placeholder="{{translate('Search Categories ...')}}">
                        </div>
                    </div>
                </div>
                <!-- Dynamic Tab Content -->
                <div class="tab-content filter-tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab-content">
                        <!-- AJAX content will load here -->
                    </div>
                </div>
            </form>
        </div>
        <!--Card Header (Search) End-->
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        let currentTab = '{{ $active_tab }}';
        var searchTimer;
        let unassigned = 0;

        $(document).on("change", ".check-all", function() 
        {
            if(this.checked) {
                // Iterate each checkbox                                                
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        function sort_categories(el)
        {
            $('#sort_categories').submit();
        }
        
        function getCategories(slug, page = 1, unassigned = 0) 
        {
            var status = $('#status').val();
            currentTab = slug;
            var slug = slug.replace(/-/g, '_');
            let keyword = $('#search_input').val();

            $('#tab-content').html('<div class="footable-loader mt-5"></div>');

            $.ajax({
                url: `{{ route('seller.refund_categories.filter') }}?page=${page}`,
                method: 'GET',
                data: {
                    status: status,
                    category_status: slug,
                    search: keyword,
                    unassigned: unassigned
                },
                success: function(response) {
                    $('#tab-content').html(response.html);
                    initFooTable();
                }
            });
        }

        function changeTab(button, statusSlug) 
        {
            document.querySelectorAll('#myTab .nav-link').forEach(el => el.classList.remove('active'));
            button.classList.add('active');
            getCategories(statusSlug);
        }

        document.addEventListener('DOMContentLoaded', function() 
        {
            getCategories(currentTab);
        });

        $('#search_input').on('keyup', function () 
        {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                getCategories(currentTab);
            }, 500);
        });

        $(document).on('click', '.pagination a', function(e) 
        {
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            getCategories(currentTab, page);
        });

        function unassigned_categories() {
            getCategories(currentTab, 1, 1);
        }
    </script>
@endsection