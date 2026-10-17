@extends('frontend.layouts.app')
@section('meta_title', 'CRAVE — Food & Beverage | ' . get_setting('website_name'))
@php
    $sf_imgkw = ['crave-coffee' => 'coffee,beans', 'crave-hotsauce' => 'hot,sauce', 'crave-granola' => 'granola,cereal'];
    $sf_img = function ($p) { return '/assets/store/img/' . $p->slug . '.jpg'; };
@endphp
@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap');
    .sf-crave{--ac:#e8a04b;--bg:#140f0b;--surf:#1e1712;--ink:#f3ebe0;--muted:#b6a693;--line:rgba(232,160,75,.16);background:var(--bg);color:var(--ink);font-family:inherit;margin-top:-1px}
    .sf-crave a{text-decoration:none}
    .sf-hero{display:grid;grid-template-columns:1.1fr .9fr;gap:2rem;align-items:center;max-width:1180px;margin:0 auto;padding:4.5rem 1.2rem 3rem}
    .sf-hero .eb{letter-spacing:.24em;text-transform:uppercase;font-size:.72rem;color:var(--ac);font-weight:600}
    .sf-hero h1{font-family:'Fraunces',serif;font-size:clamp(2.6rem,5vw,4.4rem);line-height:1.02;margin:.5rem 0;font-weight:600}
    .sf-hero p{color:var(--muted);max-width:440px;font-size:1.05rem}
    .sf-cta{display:inline-flex;margin-top:1rem;padding:.9rem 1.7rem;border-radius:999px;background:var(--ac);color:#241305;font-weight:700}
    .sf-hero .art{aspect-ratio:1/1;border-radius:24px;overflow:hidden;border:1px solid var(--line)}
    .sf-hero .art img{width:100%;height:100%;object-fit:cover}
    .sf-strip{display:flex;flex-wrap:wrap;justify-content:center;gap:1.6rem;padding:1.3rem;border-top:1px solid var(--line);border-bottom:1px solid var(--line);color:var(--muted);font-size:.82rem}
    .sf-strip b{color:var(--ink)}
    .sf-wrap{max-width:1180px;margin:0 auto;padding:3rem 1.1rem 4.5rem}
    .sf-h2{font-family:'Fraunces',serif;font-size:1.8rem;font-weight:600;margin:0 0 .3rem}
    .sf-sub{color:var(--muted);margin:0 0 1.6rem}
    .sf-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.3rem}
    .sf-card{display:flex;flex-direction:column;background:var(--surf);border:1px solid var(--line);border-radius:18px;overflow:hidden;transition:transform .25s,box-shadow .25s}
    .sf-card:hover{transform:translateY(-5px);box-shadow:0 26px 50px -28px #000}
    .sf-thumb{aspect-ratio:1/1;overflow:hidden;background:#0e0a06}
    .sf-thumb img{width:100%;height:100%;object-fit:cover;transition:transform .5s}
    .sf-card:hover .sf-thumb img{transform:scale(1.06)}
    .sf-cb{padding:1rem 1.05rem 1.2rem;display:flex;flex-direction:column;gap:.35rem;flex:1}
    .sf-nm{font-weight:700;font-size:1.02rem}.sf-ds{color:var(--muted);font-size:.8rem;flex:1}
    .sf-cf{display:flex;align-items:center;justify-content:space-between;margin-top:.5rem}
    .sf-pr{font-weight:800;font-size:1.1rem}
    .sf-view{padding:.5rem 1rem;border-radius:999px;border:1px solid var(--line);color:var(--ink);font-size:.8rem;font-weight:600}
    .sf-card:hover .sf-view{background:var(--ac);color:#241305;border-color:transparent}
    .sf-foot{text-align:center;color:var(--muted);padding:0 1rem 3rem}.sf-foot a{color:var(--ac)}
    @media(max-width:800px){.sf-hero{grid-template-columns:1fr}}
</style>
<div class="sf-crave">
    <section class="sf-hero">
        <div><div class="eb">Small-batch provisions</div><h1>Taste, bottled boldly.</h1>
            <p>Coffee, sauces and snacks made in tiny batches. Free shipping, cash on delivery.</p>
            <a href="{{ url('/crave/shop') }}" class="sf-cta">Shop the pantry →</a></div>
        <div class="art"><img loading="lazy" src="/assets/store/img/hero-crave.jpg" alt="CRAVE" onerror="this.onerror=null;this.src='https://loremflickr.com/800/800/coffee,roastery';"></div>
    </section>
    <div class="sf-strip"><span>☕ <b>Fresh</b> roasted</span><span>🚚 <b>Free shipping</b> over ₹999</span><span>🔒 <b>Secure</b> checkout</span></div>
    <div class="sf-wrap"><h2 class="sf-h2">Fresh drops</h2><p class="sf-sub">Made in small batches, shipped fresh.</p>
        <div class="sf-grid">
            @forelse ($products as $product)
                <a class="sf-card" href="{{ route('product', $product->slug) }}">
                    <div class="sf-thumb"><img loading="lazy" src="{{ $sf_img($product) }}" alt="{{ $product->getTranslation('name') }}" onerror="this.onerror=null;this.src='https://loremflickr.com/600/600/{{ $sf_imgkw[$product->slug] ?? 'food' }}';"></div>
                    <div class="sf-cb"><div class="sf-nm">{{ $product->getTranslation('name') }}</div>
                        <div class="sf-ds">{{ \Illuminate\Support\Str::limit(strip_tags($product->getTranslation('description')), 60) }}</div>
                        <div class="sf-cf"><span class="sf-pr">{!! home_base_price($product) !!}</span><span class="sf-view">View →</span></div></div>
                </a>
            @empty <p style="color:var(--muted)">No products yet.</p> @endforelse
        </div></div>
    <div class="sf-foot">CRAVE — a shivarudrakshainc storefront template · every feature on the live engine · <a href="{{ route('home') }}">shivarudrakshainc</a></div>
</div>
@endsection
