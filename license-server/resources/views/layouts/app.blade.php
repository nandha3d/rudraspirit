<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'License Server')</title>
    <style>
        :root { --bg:#0f1216; --card:#181d24; --line:#28303a; --fg:#e7ecf2; --mut:#93a1b3;
                --accent:#c8a24a; --ok:#2fbf71; --warn:#e0a83e; --bad:#e05a5a; }
        * { box-sizing:border-box; }
        body { margin:0; font:15px/1.5 system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
               background:var(--bg); color:var(--fg); }
        a { color:var(--accent); text-decoration:none; }
        a:hover { text-decoration:underline; }
        header { display:flex; align-items:center; gap:20px; padding:14px 24px;
                 background:var(--card); border-bottom:1px solid var(--line); }
        header .brand { font-weight:700; letter-spacing:.5px; color:var(--fg); }
        header nav { display:flex; gap:18px; margin-left:12px; }
        header .spacer { flex:1; }
        main { max-width:1100px; margin:0 auto; padding:28px 24px; }
        .card { background:var(--card); border:1px solid var(--line); border-radius:10px;
                padding:20px; margin-bottom:20px; }
        h1 { font-size:22px; margin:0 0 18px; }
        h2 { font-size:17px; margin:0 0 14px; }
        table { width:100%; border-collapse:collapse; }
        th,td { text-align:left; padding:10px 12px; border-bottom:1px solid var(--line); vertical-align:middle; }
        th { color:var(--mut); font-size:12px; text-transform:uppercase; letter-spacing:.4px; }
        code { background:#0b0e12; padding:2px 6px; border-radius:5px; color:var(--accent); font-size:13px; }
        .btn { display:inline-block; background:var(--accent); color:#151515; border:0; padding:9px 16px;
               border-radius:7px; font-weight:600; cursor:pointer; font-size:14px; }
        .btn:hover { filter:brightness(1.08); text-decoration:none; }
        .btn.secondary { background:transparent; color:var(--fg); border:1px solid var(--line); }
        .btn.danger { background:var(--bad); color:#fff; }
        .btn.sm { padding:5px 10px; font-size:13px; }
        input,select,textarea { width:100%; background:#0b0e12; border:1px solid var(--line);
               color:var(--fg); padding:9px 11px; border-radius:7px; font:inherit; }
        label { display:block; font-size:13px; color:var(--mut); margin:0 0 5px; }
        .field { margin-bottom:15px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; }
        .stat { text-align:center; }
        .stat .n { font-size:28px; font-weight:700; }
        .stat .l { color:var(--mut); font-size:13px; }
        .pill { display:inline-block; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .pill.active { background:rgba(47,191,113,.15); color:var(--ok); }
        .pill.suspended { background:rgba(224,168,62,.15); color:var(--warn); }
        .pill.revoked,.pill.expired { background:rgba(224,90,90,.15); color:var(--bad); }
        .flash { background:rgba(47,191,113,.12); border:1px solid var(--ok); color:var(--ok);
                 padding:11px 15px; border-radius:8px; margin-bottom:18px; }
        .errors { background:rgba(224,90,90,.12); border:1px solid var(--bad); color:var(--bad);
                  padding:11px 15px; border-radius:8px; margin-bottom:18px; }
        .muted { color:var(--mut); }
        .row-actions { display:flex; gap:8px; }
        .inline { display:inline; }
    </style>
</head>
<body>
    @auth
    <header>
        <span class="brand">🔑 LICENSE SERVER</span>
        <nav>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('licenses.index') }}">Licenses</a>
        </nav>
        <span class="spacer"></span>
        <span class="muted">{{ auth()->user()->email }}</span>
        <form method="POST" action="{{ route('logout') }}" class="inline">@csrf
            <button class="btn secondary sm">Log out</button>
        </form>
    </header>
    @endauth

    <main>
        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
