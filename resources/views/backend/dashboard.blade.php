@extends('backend.layouts.app')

@section('content')
    @if (auth()->user()->can('smtp_settings') &&
            env('MAIL_USERNAME') == null &&
            env('MAIL_PASSWORD') == null)
        <div class="">
            <div class="alert alert-info d-flex align-items-center">
                {{ translate('Please Configure SMTP Setting to work all email sending functionality') }},
                <a class="alert-link ml-2" href="{{ route('smtp_settings.index') }}">{{ translate('Configure Now') }}</a>
            </div>
        </div>
    @endif

    @can('admin_dashboard')
        @php
            // Category stock vs sales series (from cached graph data)
            $rs_cat_labels = [];
            foreach ($root_categories as $rc) { $rs_cat_labels[] = $rc->getTranslation('name'); }
            $rs_cat_sales = array_values(array_filter(explode(',', $cached_graph_data['num_of_sale_data'] ?? ''), fn($v) => $v !== ''));
            $rs_cat_qty   = array_values(array_filter(explode(',', $cached_graph_data['qty_data'] ?? ''), fn($v) => $v !== ''));

            $rs_admin_month = (float) ($admin_sale_this_month->total_sale ?? 0);
            $rs_seller_month = (float) ($seller_sale_this_month->total_sale ?? 0);
            $rs_month_total = max(1, $rs_admin_month + $rs_seller_month);
            $rs_admin_pct = round($rs_admin_month / $rs_month_total * 100);
            $rs_seller_pct = 100 - $rs_admin_pct;
        @endphp

        <style>
            .rs-dash { --rs-gap: 1rem; }
            .rs-dash-head { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:1rem; }
            .rs-dash-title { font-size:1.15rem; font-weight:700; margin:0; }
            .rs-mode-toggle { display:inline-flex; background:#eef1f6; border-radius:999px; padding:3px; }
            .rs-mode-toggle button { border:none; background:transparent; font-size:12px; font-weight:600; color:#64708a; padding:6px 16px; border-radius:999px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
            .rs-mode-toggle button.active { background:#fff; color:#2f6fed; box-shadow:0 1px 3px rgba(30,40,70,.14); }

            .rs-grid { display:grid; gap:var(--rs-gap); }
            .rs-kpis { grid-template-columns:repeat(4, 1fr); }
            .rs-charts { grid-template-columns:repeat(2, 1fr); }
            .rs-lists { grid-template-columns:repeat(2, 1fr); }
            @media (max-width:1200px){ .rs-kpis{ grid-template-columns:repeat(2,1fr);} }
            @media (max-width:992px){ .rs-charts,.rs-lists{ grid-template-columns:1fr; } }
            @media (max-width:576px){ .rs-kpis{ grid-template-columns:1fr; } }

            .rs-card { background:#fff; border:1px solid #eceef3; border-radius:14px; padding:1.15rem 1.25rem; }
            .rs-card-h { display:flex; align-items:center; justify-content:space-between; margin-bottom:.85rem; }
            .rs-card-h h3 { font-size:.95rem; font-weight:700; margin:0; }
            .rs-card-h small { color:#8a94a6; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; }

            .rs-kpi { display:flex; align-items:center; gap:14px; }
            .rs-kpi-ic { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; flex:0 0 auto; }
            .rs-kpi-val { font-size:1.6rem; font-weight:700; line-height:1.1; color:#1f2740; }
            .rs-kpi-lbl { font-size:.78rem; color:#7b8496; font-weight:600; }
            .rs-kpi-sub { font-size:.72rem; color:#9aa2b1; }
            .rs-ic-blue{background:#e8f0ff;color:#2f6fed} .rs-ic-green{background:#e6f7ee;color:#18a558}
            .rs-ic-amber{background:#fff4e0;color:#d9932a} .rs-ic-red{background:#fdecef;color:#e0435c}
            .rs-ic-purple{background:#f0ebfd;color:#7a52d9} .rs-ic-teal{background:#e3f6f6;color:#159a97}
            .rs-ic-pink{background:#fdeaf4;color:#d6478e} .rs-ic-slate{background:#eef1f6;color:#5a6b86}

            .rs-chart-box { position:relative; height:230px; }
            .rs-chart-sm { height:200px; }

            .rs-split-bar { height:10px; border-radius:999px; background:#eef1f6; overflow:hidden; display:flex; }
            .rs-split-bar span { display:block; height:100%; }

            .rs-pipe { display:grid; grid-template-columns:repeat(3,1fr); gap:.6rem; }
            .rs-pipe-item { border-radius:10px; padding:.7rem .8rem; }
            .rs-pipe-item .v { font-size:1.4rem; font-weight:700; line-height:1; }
            .rs-pipe-item .l { font-size:.72rem; font-weight:600; opacity:.85; }

            .rs-avatars { display:flex; flex-wrap:wrap; }
            .rs-avatars .a { width:38px; height:38px; border-radius:50%; overflow:hidden; margin-right:-8px; border:2px solid #fff; background:#eef1f6; }
            .rs-avatars .a img { width:100%; height:100%; object-fit:cover; }

            .rs-row { display:flex; align-items:center; justify-content:space-between; padding:.35rem 0; border-bottom:1px dashed #eef1f6; font-size:.85rem; }
            .rs-row:last-child { border-bottom:none; }
            .rs-dot { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:8px; }

            .rs-tablist { display:flex; gap:4px; }
            .rs-tablist a { font-size:.72rem; font-weight:600; color:#8a94a6; padding:3px 10px; border-radius:999px; cursor:pointer; }
            .rs-tablist a.active { background:#eef1f6; color:#2f6fed; }
            .rs-scroll { max-height:300px; overflow-y:auto; overflow-x:hidden; }

            /* ---------- COMPACT MODE ---------- */
            .rs-dash[data-mode="compact"] { --rs-gap:.6rem; }
            .rs-dash[data-mode="compact"] .rs-kpis { grid-template-columns:repeat(4,1fr); }
            @media (min-width:1200px){ .rs-dash[data-mode="compact"] .rs-kpis { grid-template-columns:repeat(8,1fr); } }
            .rs-dash[data-mode="compact"] .rs-card { padding:.7rem .8rem; border-radius:10px; }
            .rs-dash[data-mode="compact"] .rs-kpi { gap:9px; flex-direction:column; align-items:flex-start; }
            .rs-dash[data-mode="compact"] .rs-kpi-ic { width:34px; height:34px; font-size:15px; border-radius:9px; }
            .rs-dash[data-mode="compact"] .rs-kpi-val { font-size:1.2rem; }
            .rs-dash[data-mode="compact"] .rs-kpi-lbl { font-size:.7rem; }
            .rs-dash[data-mode="compact"] .rs-kpi-sub { display:none; }
            .rs-dash[data-mode="compact"] .rs-chart-box { height:170px; }
            .rs-dash[data-mode="compact"] .rs-chart-sm { height:150px; }
            .rs-dash[data-mode="compact"] .rs-card-h { margin-bottom:.5rem; }
            .rs-dash[data-mode="compact"] .rs-scroll { max-height:230px; }
        </style>

        <div class="rs-dash" id="rsDash" data-mode="comfortable">
            <div class="rs-dash-head">
                <h1 class="rs-dash-title">{{ translate('Dashboard') }}</h1>
                <div class="rs-mode-toggle" role="group" aria-label="{{ translate('Dashboard density') }}">
                    <button type="button" data-mode="comfortable" onclick="rsSetDashMode('comfortable')"><i class="las la-th-large"></i>{{ translate('Comfortable') }}</button>
                    <button type="button" data-mode="compact" onclick="rsSetDashMode('compact')"><i class="las la-th"></i>{{ translate('Compact') }}</button>
                </div>
            </div>

            <!-- KPI STRIP -->
            <div class="rs-grid rs-kpis mb-3">
                <div class="rs-card"><div class="rs-kpi">
                    <div class="rs-kpi-ic rs-ic-blue"><i class="las la-users"></i></div>
                    <div><div class="rs-kpi-val">{{ $total_customers }}</div><div class="rs-kpi-lbl">{{ translate('Customers') }}</div></div>
                </div></div>
                <div class="rs-card"><div class="rs-kpi">
                    <div class="rs-kpi-ic rs-ic-green"><i class="las la-box"></i></div>
                    <div><div class="rs-kpi-val">{{ $total_products }}</div><div class="rs-kpi-lbl">{{ translate('Products') }}</div><div class="rs-kpi-sub">{{ $total_inhouse_products }} {{ translate('in-house') }} / {{ $total_sellers_products }} {{ translate('seller') }}</div></div>
                </div></div>
                <div class="rs-card"><div class="rs-kpi">
                    <div class="rs-kpi-ic rs-ic-purple"><i class="las la-sitemap"></i></div>
                    <div><div class="rs-kpi-val">{{ $total_categories }}</div><div class="rs-kpi-lbl">{{ translate('Categories') }}</div></div>
                </div></div>
                <div class="rs-card"><div class="rs-kpi">
                    <div class="rs-kpi-ic rs-ic-teal"><i class="las la-tags"></i></div>
                    <div><div class="rs-kpi-val">{{ $total_brands }}</div><div class="rs-kpi-lbl">{{ translate('Brands') }}</div></div>
                </div></div>
                <div class="rs-card"><div class="rs-kpi">
                    <div class="rs-kpi-ic rs-ic-pink"><i class="las la-store"></i></div>
                    <div><div class="rs-kpi-val">{{ $total_sellers }}</div><div class="rs-kpi-lbl">{{ translate('Sellers') }}</div></div>
                </div></div>
                <div class="rs-card"><div class="rs-kpi">
                    <div class="rs-kpi-ic rs-ic-slate"><i class="las la-shopping-bag"></i></div>
                    <div><div class="rs-kpi-val">{{ $total_order }}</div><div class="rs-kpi-lbl">{{ translate('Total Orders') }}</div></div>
                </div></div>
                <div class="rs-card"><div class="rs-kpi">
                    <div class="rs-kpi-ic rs-ic-red"><i class="las la-clock"></i></div>
                    <div><div class="rs-kpi-val">{{ $total_pending_order }}</div><div class="rs-kpi-lbl">{{ translate('Pending Orders') }}</div></div>
                </div></div>
                <div class="rs-card"><div class="rs-kpi">
                    <div class="rs-kpi-ic rs-ic-amber"><i class="las la-rupee-sign"></i></div>
                    <div><div class="rs-kpi-val">{{ number_format_short($total_sale) }}</div><div class="rs-kpi-lbl">{{ translate('Total Sales') }}</div><div class="rs-kpi-sub">{{ single_price($sale_this_month) }} {{ translate('this month') }}</div></div>
                </div></div>
            </div>

            <!-- CHARTS -->
            <div class="rs-grid rs-charts mb-3">
                <div class="rs-card">
                    <div class="rs-card-h"><h3>{{ translate('Yearly Sales Trend') }}</h3><small>{{ translate('This year') }}</small></div>
                    <div class="rs-chart-box"><canvas id="rsChartSales"></canvas></div>
                </div>
                <div class="rs-card">
                    <div class="rs-card-h"><h3>{{ translate('Order Status Breakdown') }}</h3><small>{{ translate('All time') }}</small></div>
                    <div class="rs-chart-box"><canvas id="rsChartOrders"></canvas></div>
                </div>
                <div class="rs-card">
                    <div class="rs-card-h"><h3>{{ translate('Category Sales & Stock') }}</h3><small>{{ translate('By root category') }}</small></div>
                    <div class="rs-chart-box"><canvas id="rsChartCategory"></canvas></div>
                </div>
                <div class="rs-card">
                    <div class="rs-card-h"><h3>{{ translate('In-house Payment Mix') }}</h3><small>{{ translate('By amount') }}</small></div>
                    <div class="rs-chart-box"><canvas id="graph-2"></canvas></div>
                </div>
            </div>

            <!-- SALES SPLIT + ORDER PIPELINE -->
            <div class="rs-grid rs-lists mb-3">
                <div class="rs-card">
                    <div class="rs-card-h"><h3>{{ translate('Sales Split (this month)') }}</h3><small>{{ single_price($sale_this_month) }}</small></div>
                    <div class="rs-split-bar mb-3">
                        <span style="width:{{ $rs_admin_pct }}%;background:#2f6fed"></span>
                        <span style="width:{{ $rs_seller_pct }}%;background:#18a558"></span>
                    </div>
                    <div class="rs-row"><span><span class="rs-dot" style="background:#2f6fed"></span>{{ translate('In-house Sales') }}</span><b>{{ single_price($rs_admin_month) }} ({{ $rs_admin_pct }}%)</b></div>
                    <div class="rs-row"><span><span class="rs-dot" style="background:#18a558"></span>{{ translate('Sellers Sales') }}</span><b>{{ single_price($rs_seller_month) }} ({{ $rs_seller_pct }}%)</b></div>
                    <div class="rs-row"><span><span class="rs-dot" style="background:#d9932a"></span>{{ translate('In-house Total Sales') }}</span><b>{{ single_price($total_inhouse_sale) }}</b></div>
                    <div class="rs-row"><span><span class="rs-dot" style="background:#7a52d9"></span>{{ translate('In-house Avg. Rating') }}</span><b>{{ number_format($inhouse_product_rating ?? 0, 2) }} / 5</b></div>
                    <div class="rs-row"><span><span class="rs-dot" style="background:#5a6b86"></span>{{ translate('In-house Orders') }}</span><b>{{ $total_inhouse_order }}</b></div>
                </div>

                <div class="rs-card">
                    <div class="rs-card-h"><h3>{{ translate('Order Pipeline') }}</h3><a href="{{ route('all_orders.index') }}" class="btn btn-sm btn-soft-primary rounded-pill">{{ translate('All Orders') }}</a></div>
                    <div class="rs-pipe">
                        <div class="rs-pipe-item" style="background:#e8f0ff;color:#2f6fed"><div class="v">{{ $total_placed_order }}</div><div class="l">{{ translate('Placed') }}</div></div>
                        <div class="rs-pipe-item" style="background:#fdecef;color:#e0435c"><div class="v">{{ $total_pending_order }}</div><div class="l">{{ translate('Pending') }}</div></div>
                        <div class="rs-pipe-item" style="background:#e6f7ee;color:#18a558"><div class="v">{{ $total_confirmed_order }}</div><div class="l">{{ translate('Confirmed') }}</div></div>
                        <div class="rs-pipe-item" style="background:#fff4e0;color:#d9932a"><div class="v">{{ $total_picked_up_order }}</div><div class="l">{{ translate('Processed') }}</div></div>
                        <div class="rs-pipe-item" style="background:#f0ebfd;color:#7a52d9"><div class="v">{{ $total_shipped_order }}</div><div class="l">{{ translate('Shipped') }}</div></div>
                        <div class="rs-pipe-item" style="background:#eef1f6;color:#5a6b86"><div class="v">{{ $total_order }}</div><div class="l">{{ translate('Total') }}</div></div>
                    </div>
                    <hr style="border-top:1px dashed #eef1f6">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="rs-kpi-lbl mb-1">{{ translate('Top Customers') }}</div>
                            <div class="rs-avatars">
                                @foreach ($top_customers as $tc)
                                    <div class="a" title="{{ $tc->name }}"><img src="{{ uploaded_asset($tc->avatar_original) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"></div>
                                @endforeach
                                @if(count($top_customers) == 0)<span class="rs-kpi-sub">{{ translate('No data') }}</span>@endif
                            </div>
                        </div>
                        @if (get_setting('vendor_system_activation') == 1)
                        <div class="text-right">
                            <div class="rs-kpi-lbl mb-1">{{ translate('Top Sellers') }}</div>
                            <div class="rs-avatars justify-content-end">
                                @foreach ($top_sellers as $ts)
                                    <div class="a" title="{{ $ts->name }}"><img src="{{ uploaded_asset($ts->avatar_original) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"></div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TOP LISTS (AJAX loaded) -->
            <div class="rs-grid rs-lists mb-3">
                <div class="rs-card">
                    <div class="rs-card-h">
                        <div><h3 class="text-primary">{{ translate('In-house Top Category') }}</h3><small>{{ translate('By Sales') }}</small></div>
                        <div class="rs-tablist">
                            <a class="active inhouse_top_categories" data-target="all">{{ translate('All') }}</a>
                            <a class="inhouse_top_categories" data-target="DAY">{{ translate('Today') }}</a>
                            <a class="inhouse_top_categories" data-target="WEEK">{{ translate('Week') }}</a>
                            <a class="inhouse_top_categories" data-target="MONTH">{{ translate('Month') }}</a>
                        </div>
                    </div>
                    <div class="rs-scroll" id="inhouse-top-categories"></div>
                </div>

                <div class="rs-card">
                    <div class="rs-card-h">
                        <div><h3 class="text-danger">{{ translate('In-house Top Brands') }}</h3><small>{{ translate('By Sales') }}</small></div>
                        <div class="rs-tablist">
                            <a class="active inhouse_top_brands" data-target="all">{{ translate('All') }}</a>
                            <a class="inhouse_top_brands" data-target="DAY">{{ translate('Today') }}</a>
                            <a class="inhouse_top_brands" data-target="WEEK">{{ translate('Week') }}</a>
                            <a class="inhouse_top_brands" data-target="MONTH">{{ translate('Month') }}</a>
                        </div>
                    </div>
                    <div class="rs-scroll" id="inhouse-top-brands"></div>
                </div>
            </div>

            @if (get_setting('vendor_system_activation') == 1)
                <div class="rs-card mb-3">
                    <div class="rs-card-h">
                        <div><h3 class="text-warning">{{ translate('Top Seller & Products') }}</h3><small>{{ translate('By Sales') }}</small></div>
                        <div class="rs-tablist">
                            <a class="active top_sellers_products_tab" data-target="all">{{ translate('All') }}</a>
                            <a class="top_sellers_products_tab" data-target="DAY">{{ translate('Today') }}</a>
                            <a class="top_sellers_products_tab" data-target="WEEK">{{ translate('Week') }}</a>
                            <a class="top_sellers_products_tab" data-target="MONTH">{{ translate('Month') }}</a>
                        </div>
                    </div>
                    <div id="top-sellers-products-section"></div>
                </div>
            @else
                <div class="rs-card mb-3 text-center py-4">
                    <a href="{{ route('activation.index') }}" class="fs-14 fw-600 text-info hov-text-primary">{{ translate('Activate Vendor System') }}</a>
                </div>
            @endif
        </div>
    @endcan
@endsection

@section('script')
    @include('backend.dashboard.dashboard_js')

    <script type="text/javascript">
        // ---- view-mode toggle (persisted) ----
        function rsSetDashMode(mode) {
            var el = document.getElementById('rsDash');
            if (!el) return;
            el.setAttribute('data-mode', mode);
            document.querySelectorAll('.rs-mode-toggle button').forEach(function (b) {
                b.classList.toggle('active', b.getAttribute('data-mode') === mode);
            });
            try { localStorage.setItem('rs_dash_mode', mode); } catch (e) {}
            // charts need a resize after the container size changes
            setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 60);
        }
        (function () {
            var saved = 'comfortable';
            try { saved = localStorage.getItem('rs_dash_mode') || 'comfortable'; } catch (e) {}
            rsSetDashMode(saved);
        })();

        // ---- charts ----
        AIZ.plugins.chart('#rsChartSales', {
            type: 'line',
            data: {
                labels: [@foreach ($sales_stat as $month => $row)"{{ $month }}",@endforeach],
                datasets: [{
                    label: "{{ translate('Yearly Sales') }}",
                    data: [@foreach ($sales_stat as $row){{ $row[0]->total }},@endforeach],
                    fill: true,
                    borderColor: '#2f6fed',
                    backgroundColor: 'rgba(47,111,237,.10)',
                    tension: .35,
                    pointRadius: 3,
                    pointBackgroundColor: '#2f6fed'
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#f1f3f7' } } }
            }
        });

        AIZ.plugins.chart('#rsChartOrders', {
            type: 'doughnut',
            data: {
                labels: ['{{ translate('Placed') }}', '{{ translate('Pending') }}', '{{ translate('Confirmed') }}', '{{ translate('Processed') }}', '{{ translate('Shipped') }}'],
                datasets: [{
                    data: [{{ $total_placed_order }}, {{ $total_pending_order }}, {{ $total_confirmed_order }}, {{ $total_picked_up_order }}, {{ $total_shipped_order }}],
                    backgroundColor: ['#2f6fed', '#e0435c', '#18a558', '#d9932a', '#7a52d9'],
                    hoverOffset: 4
                }]
            },
            options: { maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } } } }
        });

        AIZ.plugins.chart('#rsChartCategory', {
            type: 'bar',
            data: {
                labels: [@foreach ($rs_cat_labels as $l)"{{ $l }}",@endforeach],
                datasets: [
                    { label: "{{ translate('Units Sold') }}", data: [{{ implode(',', $rs_cat_sales) ?: '0' }}], backgroundColor: '#2f6fed', borderRadius: 4 },
                    { label: "{{ translate('Stock Qty') }}", data: [{{ implode(',', $rs_cat_qty) ?: '0' }}], backgroundColor: '#cdd7ea', borderRadius: 4 }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } } },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#f1f3f7' } } }
            }
        });

        AIZ.plugins.chart('#graph-2', {
            type: 'doughnut',
            data: {
                labels: [@foreach ($payment_type_wise_inhouse_sale as $row)"{{ ucwords(str_replace('_', ' ', $row->payment_type)) }}",@endforeach],
                datasets: [{
                    label: '{{ translate('Total Sales') }}',
                    data: [@foreach ($payment_type_wise_inhouse_sale as $row){{ $row->total_amount }},@endforeach],
                    backgroundColor: ['#e0435c', '#2f6fed', '#d9932a', '#18a558', '#7a52d9'],
                    hoverOffset: 4
                }]
            },
            options: { maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } } } }
        });

        function top_category_products(category_id, e) {
            $(".top_category_products").removeClass("active"); e.classList.add("active");
            $(".top_category_product_table").removeClass("show"); $("#top_category_product_table_" + category_id).addClass("show");
        }
        function top_sellers_products(seller_id, e) {
            $(".top_sellers_products").removeClass("active"); e.classList.add("active");
            $(".top_sellers_product_table").removeClass("show"); $("#top_sellers_product_table_" + seller_id).addClass("show");
        }
        function top_brands_products(brand_id, e) {
            $(".top_brands_products").removeClass("active"); e.classList.add("active");
            $(".top_brands_product_table").removeClass("show"); $("#top_brands_product_table_" + brand_id).addClass("show");
        }

        // keep the pill tab active-state in sync on click
        $(document).on('click', '.rs-tablist a', function () {
            $(this).closest('.rs-tablist').find('a').removeClass('active');
            $(this).addClass('active');
        });
    </script>
@endsection
