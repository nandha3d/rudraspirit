<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Animazon — Plans & Pricing')</title>
    <style>
        :root { --bg:#0f1216; --card:#181d24; --line:#28303a; --fg:#e7ecf2; --mut:#93a1b3;
                --accent:#c8a24a; --ok:#2fbf71; --bad:#e05a5a; }
        * { box-sizing:border-box; }
        body { margin:0; font:16px/1.6 system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
               background:var(--bg); color:var(--fg); }
        a { color:var(--accent); text-decoration:none; }
        header { display:flex; align-items:center; gap:16px; padding:16px 28px;
                 border-bottom:1px solid var(--line); }
        header .brand { font-weight:800; font-size:18px; letter-spacing:1px; }
        main { max-width:1080px; margin:0 auto; padding:40px 24px 80px; }
        h1 { font-size:32px; margin:0 0 8px; text-align:center; }
        .sub { text-align:center; color:var(--mut); margin:0 0 40px; }
        .plans { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:22px; align-items:stretch; }
        .plan { background:var(--card); border:1px solid var(--line); border-radius:14px;
                padding:28px 24px; display:flex; flex-direction:column; position:relative; }
        .plan.featured { border-color:var(--accent); box-shadow:0 0 0 1px var(--accent); }
        .plan .tag { position:absolute; top:-12px; left:50%; transform:translateX(-50%);
                     background:var(--accent); color:#151515; font-size:12px; font-weight:700;
                     padding:3px 14px; border-radius:20px; }
        .plan h3 { margin:0 0 6px; font-size:20px; }
        .plan .desc { color:var(--mut); font-size:14px; min-height:42px; }
        .plan .price { font-size:34px; font-weight:800; margin:14px 0 2px; }
        .plan .per { color:var(--mut); font-size:13px; margin-bottom:18px; }
        .plan ul { list-style:none; padding:0; margin:0 0 22px; flex:1; }
        .plan li { padding:6px 0 6px 26px; position:relative; font-size:14px; }
        .plan li::before { content:"✓"; position:absolute; left:2px; color:var(--ok); font-weight:700; }
        .plan .mods { font-size:12px; color:var(--mut); margin-bottom:16px; }
        .btn { display:block; text-align:center; background:var(--accent); color:#151515; border:0;
               padding:12px 18px; border-radius:9px; font-weight:700; cursor:pointer; font-size:15px; width:100%; }
        .btn.secondary { background:transparent; color:var(--fg); border:1px solid var(--line); }
        .card { background:var(--card); border:1px solid var(--line); border-radius:14px; padding:28px; }
        input { width:100%; background:#0b0e12; border:1px solid var(--line); color:var(--fg);
                padding:11px 13px; border-radius:8px; font:inherit; }
        label { display:block; font-size:13px; color:var(--mut); margin:0 0 6px; }
        .field { margin-bottom:16px; }
        .errors { background:rgba(224,90,90,.12); border:1px solid var(--bad); color:var(--bad);
                  padding:11px 15px; border-radius:8px; margin-bottom:18px; }
        code { background:#0b0e12; padding:2px 8px; border-radius:6px; color:var(--accent); }
        footer { text-align:center; color:var(--mut); font-size:13px; padding:30px; border-top:1px solid var(--line); }
    </style>
</head>
<body>
    <header>
        <span class="brand">ANIMAZON</span>
        <span style="flex:1;"></span>
        <a href="{{ route('public.pricing') }}">Pricing</a>
    </header>

    <main>
        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
            </div>
        @endif
        @yield('content')
    </main>

    <footer>© {{ date('Y') }} Animazon · <a href="{{ route('public.pricing') }}">Plans & Pricing</a></footer>
</body>
</html>
