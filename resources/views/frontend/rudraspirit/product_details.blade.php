@extends('frontend.layouts.app')

@php
    $rsMetaName = $detailedProduct->getTranslation('name');
    $rsMetaTitle = $detailedProduct->meta_title ?: $rsMetaName;
    $rsMetaDesc = $detailedProduct->meta_description ?: \Illuminate\Support\Str::limit(trim(strip_tags($detailedProduct->getTranslation('description'))), 160);
    $rsMetaImage = $detailedProduct->thumbnail ? get_image($detailedProduct->thumbnail) : static_asset('assets/img/pages/rudraspirit/Gemini_Generated_Image_2ht5mi2ht5mi2ht5.webp');
    $rsMetaQty = 0;
    foreach ($detailedProduct->stocks as $rsStock) { $rsMetaQty += $rsStock->qty; }
    $rsMetaCurrency = optional(get_system_currency())->code ?? 'INR';
@endphp

@section('meta_title'){{ $rsMetaTitle }}@stop
@section('meta_description'){{ $rsMetaDesc }}@stop
@section('meta_keywords'){{ $detailedProduct->tags }}@stop

@section('meta')
    <meta itemprop="name" content="{{ $rsMetaTitle }}">
    <meta itemprop="description" content="{{ $rsMetaDesc }}">
    <meta itemprop="image" content="{{ $rsMetaImage }}">
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $rsMetaTitle }}">
    <meta property="og:description" content="{{ $rsMetaDesc }}">
    <meta property="og:image" content="{{ $rsMetaImage }}">
    <meta property="og:url" content="{{ route('product', $detailedProduct->slug) }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $rsMetaTitle }}">
    <meta name="twitter:description" content="{{ $rsMetaDesc }}">
    <meta name="twitter:image" content="{{ $rsMetaImage }}">
    <script type="application/ld+json">
    {!! json_encode(array_filter([
        '@context' => 'https://schema.org/',
        '@type' => 'Product',
        'name' => $rsMetaName,
        'image' => $rsMetaImage,
        'description' => $rsMetaDesc,
        'sku' => (string) $detailedProduct->id,
        'brand' => ['@type' => 'Brand', 'name' => optional($detailedProduct->brand)->name ?: get_setting('website_name', 'Rudra Spirit')],
        'offers' => [
            '@type' => 'Offer',
            'priceCurrency' => $rsMetaCurrency,
            'price' => (string) $detailedProduct->unit_price,
            'availability' => $rsMetaQty > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => route('product', $detailedProduct->slug),
        ],
        'aggregateRating' => $reviews->count() > 0 ? [
            '@type' => 'AggregateRating',
            'ratingValue' => (string) round($detailedProduct->rating, 1),
            'reviewCount' => (string) $reviews->count(),
        ] : null,
    ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_values(array_filter([
            ['@type' => 'ListItem', 'position' => 1, 'name' => translate('Home'), 'item' => route('home')],
            optional($detailedProduct->category)->slug
                ? ['@type' => 'ListItem', 'position' => 2, 'name' => optional($detailedProduct->category)->getTranslation('name'), 'item' => route('products.category', $detailedProduct->category->slug)]
                : null,
            ['@type' => 'ListItem', 'position' => optional($detailedProduct->category)->slug ? 3 : 2, 'name' => $rsMetaName, 'item' => route('product', $detailedProduct->slug)],
        ])),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection

@section('content')
@php
    $rsRelated = filter_products(\App\Models\Product::where('published', 1)->where('id', '!=', $detailedProduct->id)->where('category_id', $detailedProduct->category_id)->latest())->take(4)->get();
    $rsQty = 0;
    foreach ($detailedProduct->stocks as $stock) {
        $rsQty += $stock->qty;
    }
@endphp
<main style="max-width:1180px;margin:0 auto;padding:54px 32px 90px;">
    <div style="font-size:13px;letter-spacing:.2em;text-transform:uppercase;color:var(--rs-ink-muted);margin-bottom:34px;">
        <a href="{{ route('home') }}" style="text-decoration:none;color:var(--rs-gold);">{{ translate('Home') }}</a> / {{ $detailedProduct->getTranslation('name') }}
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:start;">
        @php
            // product->photos is a comma-separated list of upload IDs -> uploaded_asset().
            // product->thumbnail is an Upload object -> get_image().
            $rsGalleryUrls = [];
            foreach (array_filter(explode(',', (string) $detailedProduct->photos)) as $rsPid) {
                $rsGalleryUrls[] = uploaded_asset($rsPid);
            }
            if (empty($rsGalleryUrls)) {
                $rsGalleryUrls[] = $detailedProduct->thumbnail
                    ? get_image($detailedProduct->thumbnail)
                    : static_asset('assets/img/pages/rudraspirit/Gemini_Generated_Image_2ht5mi2ht5mi2ht5.webp');
            }
            $rsMainImg = $rsGalleryUrls[0];
        @endphp
        <div>
            <div style="position:relative;aspect-ratio:1/1;border-radius:10px;background:linear-gradient(150deg,var(--rs-tan-light),var(--rs-tan));display:flex;align-items:center;justify-content:center;overflow:hidden;">
                <img id="rs-pdp-main-img" src="{{ $rsMainImg }}" alt="{{ $detailedProduct->getTranslation('name') }}" style="width:100%;height:100%;object-fit:cover;">
            </div>
            @if (count($rsGalleryUrls) > 1)
                <div style="display:flex;gap:10px;margin-top:14px;flex-wrap:wrap;">
                    @foreach ($rsGalleryUrls as $rsIdx => $rsImgUrl)
                        <button type="button" class="rs-pdp-thumb @if ($rsIdx == 0) active @endif" onclick="rsSwapMainImage(this, '{{ $rsImgUrl }}')"
                            style="width:68px;height:68px;border-radius:8px;overflow:hidden;border:2px solid transparent;background:none;padding:0;cursor:pointer;">
                            <img src="{{ $rsImgUrl }}" alt="{{ $detailedProduct->getTranslation('name') }} {{ $rsIdx + 1 }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
        <div>
            @php
                $rsDeity = optional(rudraspirit_mukhi_info($detailedProduct))->deity;
                if (!$rsDeity && $detailedProduct->tags) {
                    $rsDeity = explode(',', $detailedProduct->tags)[0];
                }
            @endphp
            @if ($rsDeity)
                <div style="font-size:13px;letter-spacing:.24em;text-transform:uppercase;color:var(--rs-gold);margin-bottom:14px;">{{ translate('Ruling Deity') }} &middot; {{ $rsDeity }}</div>
            @endif
            <h1 class="rs-serif" style="font-weight:500;font-size:40px;line-height:1.1;color:var(--rs-ink);margin:0 0 14px;">{{ $detailedProduct->getTranslation('name') }}</h1>

            <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
                <span class="rs-rating-stars" style="color:var(--rs-gold);font-size:15px;">
                    {{ renderStarRating($detailedProduct->rating) }}
                </span>
                <span style="font-size:14px;color:var(--rs-ink-muted);">
                    @php
                        $rsReviewCount = $reviews->count();
                    @endphp
                    <a href="#reviews-section" style="text-decoration:underline;color:inherit;">
                        {{ $rsReviewCount }} {{ $rsReviewCount == 1 ? translate('Review') : translate('Reviews') }}
                    </a>
                </span>
            </div>

            @if ($detailedProduct->num_of_sale > 0)
            <div style="display:flex;flex-direction:column;gap:4px;font-size:15px;color:var(--rs-gold-deep);margin-bottom:18px;">
                <span>&#128293; {{ $detailedProduct->num_of_sale }} {{ translate('sold') }}</span>
            </div>
            @endif

            <p style="font-size:17px;color:var(--rs-ink-soft);line-height:1.85;margin:0 0 26px;">{!! $detailedProduct->description !!}</p>

            @if ($rsQty > 0 || $detailedProduct->digital)
                <form id="option-choice-form">
                    @csrf
                    <input type="hidden" name="id" value="{{ $detailedProduct->id }}">

                    @if ($detailedProduct->variant_product && $detailedProduct->choice_options)
                        @foreach (json_decode($detailedProduct->choice_options) as $key => $choice)
                            <div style="margin-bottom:18px;">
                                <p style="margin:0 0 8px;font-size:15px;font-weight:500;color:var(--rs-ink);">{{ get_single_attribute_name($choice->attribute_id) }}</p>
                                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                    @foreach ($choice->values as $vi => $value)
                                        <label class="rs-variant-pill">
                                            <input type="radio" name="attribute_id_{{ $choice->attribute_id }}" value="{{ $value }}" @if ($vi == 0) checked @endif>
                                            <span>{{ $value }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <input type="hidden" name="quantity" value="1">
                    @endif

                    @php
                        $rsDefaultVariant = '';
                        if ($detailedProduct->variant_product && $detailedProduct->choice_options) {
                            $rsChoiceOptions = json_decode($detailedProduct->choice_options);
                            $rsVariantParts = [];
                            foreach ($rsChoiceOptions as $choice) {
                                if (isset($choice->values[0])) {
                                    $rsVariantParts[] = str_replace(' ', '', $choice->values[0]);
                                }
                            }
                            $rsDefaultVariant = implode('-', $rsVariantParts);
                        }
                        
                        $rsDefaultStock = null;
                        if ($rsDefaultVariant) {
                            $rsDefaultStock = $detailedProduct->stocks->where('variant', $rsDefaultVariant)->first();
                        }
                        
                        $rsInitialPrice = $rsDefaultStock ? $rsDefaultStock->price : $detailedProduct->unit_price;
                        
                        $rsDiscountApplicable = false;
                        if ($detailedProduct->discount_start_date == null) {
                            $rsDiscountApplicable = true;
                        } elseif (
                            strtotime(date('d-m-Y H:i:s')) >= $detailedProduct->discount_start_date &&
                            strtotime(date('d-m-Y H:i:s')) <= $detailedProduct->discount_end_date
                        ) {
                            $rsDiscountApplicable = true;
                        }
                        
                        if ($rsDiscountApplicable) {
                            if ($detailedProduct->discount_type == 'percent') {
                                $rsInitialPrice -= ($rsInitialPrice * $detailedProduct->discount) / 100;
                            } elseif ($detailedProduct->discount_type == 'amount') {
                                $rsInitialPrice -= $detailedProduct->discount;
                            }
                        }
                        
                        $rsTax = 0;
                        foreach ($detailedProduct->taxes as $product_tax) {
                            if ($product_tax->tax_type == 'percent') {
                                $rsTax += ($rsInitialPrice * $product_tax->tax) / 100;
                            } elseif ($product_tax->tax_type == 'amount') {
                                $rsTax += $product_tax->tax;
                            }
                        }
                        $rsInitialPrice += $rsTax;
                        if (addon_is_activated('gst_system')) {
                            $rsInitialPrice += ($rsInitialPrice * $detailedProduct->gst_rate) / 100;
                        }
                        
                        $rsInitialPrice = $rsInitialPrice * ($detailedProduct->min_qty ?? 1);
                        $rsInitialPriceFormatted = single_price($rsInitialPrice);
                    @endphp

                    <!-- Dynamic Price Display -->
                    <div id="chosen_price_div" style="display:flex;align-items:center;gap:12px;margin-bottom:20px;margin-top:14px;">
                        <span id="chosen_price" style="font-size:28px;color:var(--rs-gold-deep);font-family:'Playfair Display',serif;font-weight:500;">
                            {{ $rsInitialPriceFormatted }}
                        </span>
                        @if ($rsQty > 0)
                            <span class="rs-pdp-badge" style="background:var(--rs-success);">{{ translate('In Stock') }}</span>
                        @endif
                    </div>
                    <p style="font-size:15px;color:var(--rs-ink-muted);margin:0 0 16px;">
                        <span id="available-quantity">{{ $rsQty }}</span> {{ translate('Available') }} &middot;
                        {{ translate('Minimum order qty') }} <strong>{{ $detailedProduct->min_qty }}</strong>
                    </p>

                    @if (!$detailedProduct->digital)
                    <div style="display:flex;align-items:center;gap:18px;margin-bottom:22px;">
                        <div class="aiz-plus-minus" style="display:flex;align-items:center;border:1px solid var(--rs-tan);border-radius:999px;">
                            <button type="button" data-type="minus" data-field="quantity" style="border:none;background:none;padding:12px 18px;cursor:pointer;">&minus;</button>
                            <input type="number" name="quantity" value="{{ $detailedProduct->min_qty ?? 1 }}" min="{{ $detailedProduct->min_qty ?? 1 }}" class="input-number" style="width:46px;text-align:center;border:none;outline:none;font-size:18px;">
                            <button type="button" data-type="plus" data-field="quantity" style="border:none;background:none;padding:12px 18px;cursor:pointer;">+</button>
                        </div>
                    </div>
                    @endif

                    @if ($detailedProduct->variant_product)
                        <p style="font-size:15px;color:var(--rs-ink-muted);margin:0 0 14px;">{{ translate('Selected') }}: <span id="selected_variant"></span></p>
                    @endif

                    <div style="display:flex;gap:14px;">
                        <button type="button" id="added_to_cart_btn" @if (Auth::check() || get_setting('guest_checkout_activation') == 1) onclick="addToCart()" @else onclick="showLoginModal()" @endif class="rs-btn add-to-cart" style="flex:1;justify-content:center;">{{ translate('Add to Bag') }} <span id="add_to_cart_count">(01)</span></button>
                        <button type="button" @if (Auth::check() || get_setting('guest_checkout_activation') == 1) onclick="buyNow()" @else onclick="showLoginModal()" @endif class="rs-btn-outline buy-now" style="flex:1;">{{ translate('Buy It Now') }}</button>
                    </div>
                    <button type="button" class="out-of-stock d-none rs-btn-outline" style="width:100%;margin-top:14px;" disabled>{{ translate('Out of Stock') }}</button>
                </form>
            @else
                <span style="color:var(--rs-danger);font-size:15px;text-transform:uppercase;letter-spacing:.1em;">{{ translate('Out of stock') }}</span>
            @endif


        </div>
    </div>

    <div id="reviews-section" style="margin-top:70px;max-width:700px;">
        <h2 class="rs-serif" style="font-size:26px;font-weight:500;color:var(--rs-ink);margin:0 0 24px;">{{ translate('Customer Reviews') }}</h2>
        @if ($reviews->count() > 0)
            @foreach ($reviews as $review)
                <div style="border-bottom:1px solid var(--rs-cream-deep);padding:18px 0;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:17px;color:var(--rs-ink);font-weight:500;">{{ $review->user->name ?? ($review->custom_reviewer_name ?? translate('Anonymous')) }}</span>
                        <span class="rs-rating-stars" style="color:var(--rs-gold);font-size:13px;">{{ renderStarRating($review->rating) }}</span>
                    </div>
                    <p style="font-size:16px;color:var(--rs-ink-soft);margin:6px 0 0;">{{ $review->comment }}</p>
                    @if (!empty($review->photos))
                        <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap;">
                            @foreach (array_filter(explode(',', $review->photos)) as $rsReviewPhoto)
                                <img src="{{ uploaded_asset($rsReviewPhoto) }}" alt="{{ translate('Review photo') }}" style="width:64px;height:64px;object-fit:cover;border-radius:6px;">
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <p style="font-size:16px;color:var(--rs-ink-muted);">{{ translate('No reviews yet for this product.') }}</p>
        @endif

        {{-- Write a review — only for customers who received this product --}}
        @if (isset($review_status) && $review_status == 1)
            <div style="margin-top:34px;padding-top:28px;border-top:1px solid var(--rs-cream-deep);">
                <h3 class="rs-serif" style="font-size:21px;font-weight:500;color:var(--rs-ink);margin:0 0 16px;">{{ translate('Write a Review') }}</h3>
                <form method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detailedProduct->id }}">
                    <input type="hidden" name="order_id" value="{{ $order_id ?? '' }}">
                    <div class="rs-star-input" aria-label="{{ translate('Rating') }}">
                        @for ($rsStar = 5; $rsStar >= 1; $rsStar--)
                            <input type="radio" name="rating" id="rs-star-{{ $rsStar }}" value="{{ $rsStar }}" @if ($rsStar == 5) required @endif>
                            <label for="rs-star-{{ $rsStar }}" title="{{ $rsStar }}">&#9733;</label>
                        @endfor
                    </div>
                    <textarea name="comment" rows="3" required placeholder="{{ translate('Share your experience with this bead...') }}"
                        style="width:100%;margin-top:14px;padding:12px 14px;border:1px solid var(--rs-tan);border-radius:8px;font-family:inherit;font-size:15px;color:var(--rs-ink);outline:none;"></textarea>
                    <label style="display:block;margin-top:12px;font-size:13px;color:var(--rs-ink-muted);">
                        {{ translate('Add photos (optional)') }}
                        <input type="file" name="photos[]" multiple accept="image/*" style="display:block;margin-top:6px;">
                    </label>
                    <button type="submit" class="rs-btn" style="margin-top:16px;">{{ translate('Submit Review') }} <span>&rarr;</span></button>
                </form>
            </div>
        @elseif (!Auth::check())
            <p style="margin-top:24px;font-size:15px;color:var(--rs-ink-muted);">
                <a href="javascript:void(0)" onclick="showLoginModal()" style="color:var(--rs-gold);text-decoration:underline;">{{ translate('Log in') }}</a>
                {{ translate('and purchase this product to leave a review.') }}
            </p>
        @endif
    </div>

    @if ($rsRelated->count() > 0)
    <div style="margin-top:80px;">
        <h2 class="rs-section-title" style="text-align:center;font-size:31px;">{{ translate('You May Also') }} <em>{{ translate('Like') }}</em></h2>
        <div class="rs-product-grid" style="margin-top:36px;">
            @foreach ($rsRelated as $product)
                @include('frontend.rudraspirit.partials.product_card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif
</main>
@endsection

@section('script')
<script>
    function rsSwapMainImage(btn, src) {
        var main = document.getElementById('rs-pdp-main-img');
        if (main) main.src = src;
        document.querySelectorAll('.rs-pdp-thumb').forEach(function (el) { el.classList.remove('active'); });
        btn.classList.add('active');
    }

    $(document).ready(function() {
        // Trigger variant price calculation on page load
        getVariantPrice();

        // Listen for quantity change to update price
        $('#option-choice-form input[name="quantity"]').on('keyup change', function() {
            getVariantPrice();
        });

        // Trigger change event when clicking plus/minus buttons
        $(document).on('click', '[data-type="plus"], [data-type="minus"]', function() {
            setTimeout(function() {
                $('#option-choice-form input[name="quantity"]').trigger('change');
            }, 50);
        });
    });
</script>
@endsection
