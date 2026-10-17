{{-- Reusable themed storefront body. Expects $sf (palette + copy + image keywords),
     $products (niche products) and $catSlug. Each niche view sets $sf and includes this.
     Scoped to .$sf['class'] so multiple skins never collide. --}}
@php $s = $sf; $cls = $s['class']; @endphp
<style>
    @if(!empty($s['fontimport'])) @import url('{{ $s['fontimport'] }}'); @endif
    .{{ $cls }}{--ac:{{ $s['ac'] }};--bg:{{ $s['bg'] }};--surf:{{ $s['surf'] }};--ink:{{ $s['ink'] }};--muted:{{ $s['muted'] }};--line:{{ $s['line'] }};--on:{{ $s['on'] }};background:var(--bg);color:var(--ink);margin-top:-1px}
    .{{ $cls }} a{text-decoration:none}
    .{{ $cls }} .sf-hero{position:relative;overflow:hidden;padding:5rem 1rem 4rem;text-align:center;background:radial-gradient(90% 130% at 50% -10%,color-mix(in srgb,var(--ac) 20%,transparent),transparent 60%),var(--bg)}
    .{{ $cls }} .sf-hero .eb{position:relative;letter-spacing:.26em;text-transform:uppercase;font-size:.72rem;color:var(--ac);font-weight:600}
    .{{ $cls }} .sf-hero h1{position:relative;font-family:{{ $s['font'] }};font-size:clamp(2.5rem,6vw,4.7rem);line-height:1.02;margin:.5rem 0;font-weight:700;letter-spacing:-.01em}
    .{{ $cls }} .sf-hero p{position:relative;color:var(--muted);max-width:540px;margin:0 auto 1.4rem;font-size:1.03rem}
    .{{ $cls }} .sf-cta{position:relative;display:inline-flex;padding:.9rem 1.8rem;border-radius:999px;background:var(--ac);color:var(--on);font-weight:700;box-shadow:0 14px 30px -14px var(--ac)}
    .{{ $cls }} .sf-strip{display:flex;flex-wrap:wrap;justify-content:center;gap:1.6rem;padding:1.3rem;border-top:1px solid var(--line);border-bottom:1px solid var(--line);color:var(--muted);font-size:.82rem}
    .{{ $cls }} .sf-strip b{color:var(--ink)}
    .{{ $cls }} .sf-wrap{max-width:1180px;margin:0 auto;padding:3rem 1.1rem 4.5rem}
    .{{ $cls }} .sf-h2{font-family:{{ $s['font'] }};font-size:1.7rem;font-weight:700;margin:0 0 .3rem}
    .{{ $cls }} .sf-sub{color:var(--muted);margin:0 0 1.6rem}
    .{{ $cls }} .sf-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.3rem}
    .{{ $cls }} .sf-card{display:flex;flex-direction:column;background:var(--surf);border:1px solid var(--line);border-radius:18px;overflow:hidden;transition:transform .25s,box-shadow .25s,border-color .25s}
    .{{ $cls }} .sf-card:hover{transform:translateY(-5px);box-shadow:0 26px 50px -28px rgba(0,0,0,.5);border-color:color-mix(in srgb,var(--ac) 50%,var(--line))}
    .{{ $cls }} .sf-thumb{aspect-ratio:1/1;overflow:hidden;background:color-mix(in srgb,var(--ink) 6%,var(--surf))}
    .{{ $cls }} .sf-thumb img{width:100%;height:100%;object-fit:cover;transition:transform .5s}
    .{{ $cls }} .sf-card:hover .sf-thumb img{transform:scale(1.06)}
    .{{ $cls }} .sf-cb{padding:1rem 1.05rem 1.2rem;display:flex;flex-direction:column;gap:.35rem;flex:1}
    .{{ $cls }} .sf-nm{font-weight:700;font-size:1.02rem;color:var(--ink)}
    .{{ $cls }} .sf-ds{color:var(--muted);font-size:.8rem;flex:1}
    .{{ $cls }} .sf-cf{display:flex;align-items:center;justify-content:space-between;margin-top:.5rem}
    .{{ $cls }} .sf-pr{font-weight:800;font-size:1.1rem;color:var(--ink)}
    .{{ $cls }} .sf-view{padding:.5rem 1rem;border-radius:999px;border:1px solid var(--line);color:var(--ink);font-size:.8rem;font-weight:600}
    .{{ $cls }} .sf-card:hover .sf-view{background:var(--ac);color:var(--on);border-color:transparent}
    .{{ $cls }} .sf-foot{text-align:center;color:var(--muted);padding:0 1rem 3rem}
    .{{ $cls }} .sf-foot a{color:var(--ac)}
</style>
<div class="{{ $cls }}">
    <section class="sf-hero" style="background:linear-gradient(color-mix(in srgb,var(--bg) 74%,transparent),color-mix(in srgb,var(--bg) 92%,transparent)),url('/assets/store/img/hero-{{ session('sf_skin') }}.jpg') center/cover">
        <div class="eb">{{ $s['eb'] }}</div>
        <h1>{!! $s['h1'] !!}</h1>
        <p>{{ $s['p'] }}</p>
        <a href="{{ url('/' . session('sf_skin') . '/shop') }}" class="sf-cta">{{ $s['cta'] }}</a>
    </section>
    <div class="sf-strip">
        <span>✅ <b>Free shipping</b> over ₹999</span><span>🔁 <b>7-day</b> returns</span><span>🔒 <b>Secure</b> checkout</span><span>💬 <b>Real</b> support</span>
    </div>
    <div class="sf-wrap">
        <h2 class="sf-h2">{{ $s['h2'] }}</h2>
        <p class="sf-sub">{{ $s['sub'] }}</p>
        <div class="sf-grid">
            @forelse ($products as $product)
                @php $kw = $s['imgkw'][$product->slug] ?? urlencode($product->getTranslation('name')); @endphp
                <a class="sf-card" href="{{ route('product', $product->slug) }}">
                    <div class="sf-thumb"><img loading="lazy" src="/assets/store/img/{{ $product->slug }}.jpg" alt="{{ $product->getTranslation('name') }}" onerror="this.onerror=null;this.src='https://loremflickr.com/600/600/{{ $kw }}';"></div>
                    <div class="sf-cb">
                        <div class="sf-nm">{{ $product->getTranslation('name') }}</div>
                        <div class="sf-ds">{{ \Illuminate\Support\Str::limit(strip_tags($product->getTranslation('description')), 60) }}</div>
                        <div class="sf-cf"><span class="sf-pr">{!! home_base_price($product) !!}</span><span class="sf-view">View →</span></div>
                    </div>
                </a>
            @empty <p style="color:var(--muted)">No products yet.</p> @endforelse
        </div>
    </div>
    <div class="sf-foot">{{ $s['brand'] }} — a shivarudrakshainc storefront template · every feature on the live engine · <a href="{{ route('home') }}">shivarudrakshainc</a></div>
</div>
