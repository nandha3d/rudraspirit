{{-- Custom per-niche storefront footer, swapped in for frontend.inc.footer in a niche. --}}
@php
    $sfSlug = session('sf_skin');
    $cfg = config('storefronts.' . $sfSlug);
@endphp
@if ($cfg)
@php
    $dark = $cfg['dark']; $ac = $cfg['accent'];
    $bg   = $dark ? '#070910' : '#141414';
    $ink  = '#f4f6fb'; $muted = 'rgba(244,246,251,.6)';
    $line = 'rgba(255,255,255,.1)';
@endphp
<style>
    .sffoot{background:{{ $bg }};color:{{ $ink }};font-family:'{{ $cfg['font'] }}',serif}
    .sffoot .in{max-width:1180px;margin:0 auto;padding:3.2rem 1.4rem 1.4rem;display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:1.5rem}
    .sffoot .bd{font-family:'{{ $cfg['font'] }}',serif;font-weight:700;font-size:1.5rem}
    .sffoot .bd b{color:{{ $ac }}}
    .sffoot p{color:{{ $muted }};font-size:.86rem;max-width:280px;font-family:'Inter',sans-serif}
    .sffoot h4{font-family:'Inter',sans-serif;font-size:.72rem;letter-spacing:.12em;text-transform:uppercase;color:{{ $muted }};margin:0 0 .8rem}
    .sffoot ul{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem}
    .sffoot ul a{color:{{ $ink }};text-decoration:none;font-size:.88rem;font-family:'Inter',sans-serif;opacity:.85}
    .sffoot ul a:hover{opacity:1;color:{{ $ac }}}
    .sffoot .bar{border-top:1px solid {{ $line }};margin-top:1rem;padding:1rem 1.4rem;text-align:center;color:{{ $muted }};font-size:.78rem;font-family:'Inter',sans-serif}
    .sffoot .bar a{color:{{ $ac }};text-decoration:none}
    @media(max-width:800px){.sffoot .in{grid-template-columns:1fr 1fr}}
</style>
<footer class="sffoot">
    <div class="in">
        <div>
            <div class="bd">{{ $cfg['emoji'] }} <b>{{ $cfg['brand'] }}</b></div>
            <p>{{ $cfg['tag'] }}. Free shipping, cash on delivery and easy returns — powered by the shivarudrakshainc engine.</p>
        </div>
        <div><h4>Shop</h4><ul>
            <li><a href="{{ url('/' . $sfSlug) }}">Home</a></li>
            <li><a href="{{ url('/' . $sfSlug . '/shop') }}">All products</a></li>
            <li><a href="{{ route('cart') }}">Cart</a></li>
        </ul></div>
        <div><h4>Account</h4><ul>
            <li><a href="{{ auth()->user() ? route('dashboard') : route('user.login') }}">My account</a></li>
            <li><a href="{{ route('user.login') }}">Orders</a></li>
            <li><a href="{{ route('user.registration') }}">Register</a></li>
        </ul></div>
        <div><h4>More</h4><ul>
            <li><a href="https://animazon.in/marketplace/">All templates</a></li>
            <li><a href="{{ route('home') }}">shivarudrakshainc</a></li>
        </ul></div>
    </div>
    <div class="bar">© {{ date('Y') }} {{ $cfg['brand'] }} — a shivarudrakshainc storefront template · built on the live engine</div>
</footer>
@else
    @include('frontend.inc.footer')
@endif
