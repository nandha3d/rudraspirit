@extends('frontend.layouts.app')

@section('content')
<section style="background:var(--rs-cream);padding:54px 32px;text-align:center;">
    <div style="font-size:13px;letter-spacing:.24em;text-transform:uppercase;color:var(--rs-gold);">
        <a href="{{ route('home') }}" style="text-decoration:none;color:inherit;">{{ translate('Home') }}</a> / {{ translate('Shop') }}
    </div>
    <h1 class="rs-serif" style="font-weight:500;font-size:45px;letter-spacing:.06em;text-transform:uppercase;color:var(--rs-ink);margin:14px 0 0;">
        @if (isset($category) && $category)
            {{ $category->getTranslation('name') }}
        @elseif (isset($brand) && $brand)
            {{ $brand->getTranslation('name') }}
        @elseif (!empty($query))
            {{ translate('Results for') }} "{{ $query }}"
        @else
            {{ translate('Shop Collection') }}
        @endif
    </h1>
</section>

<section style="max-width:1280px;margin:0 auto;padding:46px 32px 90px;">
    <div class="rs-filter-layout">
        
        <!-- Sidebar Filters -->
        <div class="rs-filter-sidebar" id="rs-sidebar-filters">
            <!-- Mobile Close Header -->
            <div class="rs-filter-close-btn">
                <h3>{{ translate('Filters') }}</h3>
                <button type="button" onclick="toggleMobileSidebar()">&times;</button>
            </div>

            <form id="search-form" action="" method="GET">
                <input type="hidden" name="keyword" value="{{ $query }}">
                
                @if (isset($category_id))
                    <input type="hidden" name="category_id" value="{{ $category_id }}">
                @endif
                @if (isset($brand_id))
                    <input type="hidden" name="brand_id" value="{{ $brand_id }}">
                @endif

                <!-- Categories -->
                @if (isset($categories) && count($categories) > 0)
                <div class="rs-filter-section">
                    <div class="rs-filter-section-title" onclick="toggleSection(this)">
                        {{ translate('Categories') }}
                        <span></span>
                    </div>
                    <div class="rs-filter-section-content">
                        <ul class="list-unstyled mb-0" style="max-height: 250px; overflow-y: auto;">
                            @foreach ($categories as $cat)
                                @if ($cat->products_count > 0 || $cat->childrenCategories->count() > 0)
                                    <li class="mb-2">
                                        <label class="aiz-checkbox mb-0">
                                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                                @if (isset($category_id) && $category_id == $cat->id) checked @endif
                                                onchange="filter(event)">
                                            <span class="aiz-square-check"></span>
                                            <span>{{ $cat->getTranslation('name') }} ({{ $cat->products_count }})</span>
                                        </label>
                                        @if($cat->childrenCategories->count() > 0)
                                            <ul class="list-unstyled ml-4 mt-2">
                                                @foreach($cat->childrenCategories as $child)
                                                    <li class="mb-2">
                                                        <label class="aiz-checkbox mb-0">
                                                            <input type="checkbox" name="categories[]" value="{{ $child->id }}"
                                                                @if (isset($category_id) && $category_id == $child->id) checked @endif
                                                                onchange="filter(event)">
                                                            <span class="aiz-square-check"></span>
                                                            <span>{{ $child->getTranslation('name') }} ({{ $child->products_count }})</span>
                                                        </label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Price Range -->
                <div class="rs-filter-section">
                    <div class="rs-filter-section-title" onclick="toggleSection(this)">
                        {{ translate('Price Range') }}
                        <span></span>
                    </div>
                    <div class="rs-filter-section-content">
                        <div class="rs-price-inputs">
                            <input type="number" name="min_price" value="{{ $min_price }}" placeholder="{{ translate('Min') }}" onchange="filter(event)">
                            <span>—</span>
                            <input type="number" name="max_price" value="{{ $max_price }}" placeholder="{{ translate('Max') }}" onchange="filter(event)">
                        </div>
                    </div>
                </div>

                <!-- Attributes -->
                @foreach ($attributes as $attribute)
                    @if ($attribute->product_count > 0)
                        <div class="rs-filter-section">
                            <div class="rs-filter-section-title" onclick="toggleSection(this)">
                                {{ $attribute->getTranslation('name') }}
                                <span></span>
                            </div>
                            @php
                                $isChecked = false;
                                foreach ($attribute->attribute_values as $val) {
                                    if (in_array($val->value, $selected_attribute_values)) {
                                        $isChecked = true;
                                    }
                                }
                            @endphp
                            <div class="rs-filter-section-content" style="{{ $isChecked ? '' : 'display:none;' }}">
                                <div class="aiz-checkbox-list" style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($attribute->attribute_values as $val)
                                        @if ($val->product_count > 0)
                                            <div class="mb-2">
                                                <label class="aiz-checkbox mb-0">
                                                    <input type="checkbox" name="selected_attribute_values[]" value="{{ $val->value }}"
                                                        @if (in_array($val->value, $selected_attribute_values)) checked @endif
                                                        onchange="filter(event)">
                                                    <span class="aiz-square-check"></span>
                                                    <span>{{ $val->value }} ({{ $val->product_count }})</span>
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Color Filter -->
                @if (get_setting('color_filter_activation'))
                <div class="rs-filter-section">
                    <div class="rs-filter-section-title" onclick="toggleSection(this)">
                        {{ translate('Filter by Color') }}
                        <span></span>
                    </div>
                    <div class="rs-filter-section-content" style="display:none;">
                        <div class="aiz-checkbox-list" style="max-height: 200px; overflow-y: auto;">
                            @foreach ($colors as $color)
                                @if ($color->product_count > 0)
                                    <div class="mb-2">
                                        <label class="aiz-checkbox mb-0">
                                            <input type="checkbox" name="colors[]" value="{{ $color->code }}"
                                                @if (isset($selected_color) && $selected_color == $color->code) checked @endif
                                                onchange="filter(event)">
                                            <span class="aiz-square-check"></span>
                                            <span class="d-flex align-items-center">
                                                <span style="width: 14px; height: 14px; background-color: {{ $color->code }}; border-radius: 50%; display: inline-block; margin-right: 8px; border: 1px solid var(--rs-cream-deep);"></span>
                                                {{ $color->name }} ({{ $color->product_count }})
                                            </span>
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </form>
        </div>

        <!-- Products Area -->
        <div id="products-grid-container" style="transition: opacity 0.3s ease;">
            
            <!-- Topbar (Mobile Trigger, Product Count, Sorting) -->
            <div class="rs-listing-topbar">
                <button type="button" class="rs-mobile-filter-btn" onclick="toggleMobileSidebar()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    {{ translate('Filters') }}
                </button>

                <div class="fs-14 text-dark-50">
                    <span id="total_product_count" class="font-weight-bold" style="color:var(--rs-ink);">{{ $products->total() }}</span> 
                    {{ translate('Products Found') }}
                    <span id="searching_product" style="display:none; margin-left: 12px; font-weight: 500; color: var(--rs-gold);">
                        {{ translate('Searching') }}...
                    </span>
                </div>

                <div class="rs-sort-wrap">
                    <select name="sort_by" class="rs-sort-select" form="search-form" onchange="filter(event)">
                        <option value="">{{ translate('Sort by') }}</option>
                        <option value="newest" @if ($sort_by == 'newest') selected @endif>{{ translate('Newest') }}</option>
                        <option value="oldest" @if ($sort_by == 'oldest') selected @endif>{{ translate('Oldest') }}</option>
                        <option value="price-asc" @if ($sort_by == 'price-asc') selected @endif>{{ translate('Price: Low to High') }}</option>
                        <option value="price-desc" @if ($sort_by == 'price-desc') selected @endif>{{ translate('Price: High to Low') }}</option>
                    </select>
                </div>
            </div>

            <!-- Products Row -->
            <div class="row row-cols-xxl-3 row-cols-xl-3 row-cols-lg-3 row-cols-md-2 row-cols-1 g-4" id="products-row">
                @if ($products->count() > 0)
                    @foreach ($products as $product)
                        <div class="col">
                            @include('frontend.rudraspirit.partials.product_card', ['product' => $product])
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <p style="color:var(--rs-ink-muted);">{{ translate('No products found matching your criteria.') }}</p>
                    </div>
                @endif
            </div>

            <!-- Pagination Container -->
            <div id="pagination" class="mt-5 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</section>

<!-- Mobile Overlay -->
<div class="rs-filter-overlay" id="rs-filter-overlay" onclick="toggleMobileSidebar()"></div>

@endsection

@section('script')
<script type="text/javascript">
    let initialCategoryLoaded = true;

    // Toggle Collapsible Sections
    function toggleSection(element) {
        let parent = element.parentElement;
        parent.classList.toggle('collapsed');
        let content = element.nextElementSibling;
        if (parent.classList.contains('collapsed')) {
            $(content).slideUp(250);
        } else {
            $(content).slideDown(250);
        }
    }

    // Toggle Mobile Sidebar Drawer
    function toggleMobileSidebar() {
        $('#rs-sidebar-filters').toggleClass('show');
        $('#rs-filter-overlay').toggleClass('show');
    }

    // Trigger Filter Data Update
    function filter(e) {
        if (e) e.preventDefault();
        filter_data();
    }

    // AJAX Fetch Filtered Products
    function filter_data(page = 1) {
        $("#searching_product").show();
        $("#products-grid-container").css("opacity", "0.55");

        let formData = $('#search-form').serialize();
        formData += '&page=' + page;

        // On first interaction or subsequent filter updates, append Category ID if no checkbox checked
        let categoryId = "{{ $category_id ?? 'null' }}";
        if (categoryId !== "null" && !formData.includes('categories%5B%5D=')) {
            // Include category_id parameter so SearchController handles it properly
            formData += '&category_id=' + categoryId;
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('suggestion.search2') }}",
            type: 'get',
            data: formData,
            success: function(response) {
                $("#searching_product").hide();
                $("#products-grid-container").css("opacity", "1");

                // Populate Grid and Pagination
                $('#products-row').html(response.product_html);
                
                // Replace the pagination markup with AJAX-compatible links
                $('#pagination').html(response.pagination_html);
                $('#total_product_count').text(response.total_product_count);

                // Smooth scroll to top of listing
                window.scrollTo({
                    top: $('#products-grid-container').offset().top - 120,
                    behavior: 'smooth'
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching filtered products:', error);
                $("#searching_product").hide();
                $("#products-grid-container").css("opacity", "1");
            }
        });
    }

    // Listen to AJAX pagination clicks
    $(document).on('click', '#pagination .page-btn, #pagination a', function(e) {
        e.preventDefault();
        // Extract page number from data attributes or URL href
        let page = $(this).data('page');
        if (!page) {
            let url = $(this).attr('href');
            if (url) {
                let matches = url.match(/page=(\d+)/);
                if (matches) page = matches[1];
            }
        }
        if (page) {
            filter_data(page);
        }
    });
</script>
@endsection
