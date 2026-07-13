<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('Partner Dashboard') }} — {{ get_setting('website_name') }}</title>
    <style>
        *{box-sizing:border-box} body{font-family:system-ui,'Segoe UI',Roboto,sans-serif;background:#f6f3ee;color:#241b12;margin:0}
        .top{background:#1c1811;color:#e7c98a;padding:16px 28px;display:flex;align-items:center;justify-content:space-between}
        .top h1{font-size:16px;margin:0} .top .who{font-size:13px;color:#c9bfa8}
        .top form{margin:0} .logout{background:transparent;border:1px solid #4a3f28;color:#c9bfa8;padding:7px 14px;border-radius:8px;cursor:pointer;font-size:13px}
        .wrap{max-width:920px;margin:0 auto;padding:24px}
        .kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
        @media(max-width:700px){.kpis{grid-template-columns:repeat(2,1fr)}}
        .kpi{background:#fff;border:1px solid #eadfce;border-radius:12px;padding:16px}
        .kpi .l{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#9a927f}
        .kpi .v{font-size:22px;font-weight:700;margin-top:4px}
        .card{background:#fff;border:1px solid #eadfce;border-radius:12px;overflow:hidden}
        .card h2{font-size:14px;margin:0;padding:14px 18px;border-bottom:1px solid #f0e8db}
        table{width:100%;border-collapse:collapse;font-size:14px}
        th,td{text-align:left;padding:11px 18px;border-bottom:1px solid #f2ece0}
        th{font-size:11px;text-transform:uppercase;color:#9a927f}
        .r{text-align:right} .badge{font-size:11px;padding:2px 9px;border-radius:999px}
        .paid{background:#e6f7ee;color:#1f8a4c} .pending{background:#fdf4e3;color:#b07d1a}
        .muted{color:#9a927f}
    </style>
</head>
<body>
    <div class="top">
        <div>
            <h1>{{ translate('Partner Dashboard') }}</h1>
            <div class="who">{{ $partner->name }} · {{ translate('Share') }} {{ $partner->share_percent }}%</div>
        </div>
        <form method="POST" action="{{ route('partner.logout') }}">
            @csrf
            <button class="logout" type="submit">{{ translate('Log out') }}</button>
        </form>
    </div>

    <div class="wrap">
        <div class="kpis">
            <div class="kpi"><div class="l">{{ translate('Your Share') }}</div><div class="v">{{ $partner->share_percent }}%</div></div>
            <div class="kpi"><div class="l">{{ translate('Total Earned') }}</div><div class="v">{{ single_price($totalEarned) }}</div></div>
            <div class="kpi"><div class="l">{{ translate('Paid') }}</div><div class="v" style="color:#1f8a4c">{{ single_price($totalPaid) }}</div></div>
            <div class="kpi"><div class="l">{{ translate('Pending') }}</div><div class="v" style="color:#b07d1a">{{ single_price($totalPending) }}</div></div>
        </div>

        <div class="card">
            <h2>{{ translate('Your distributions') }}</h2>
            <table>
                <thead><tr>
                    <th>{{ translate('Period') }}</th>
                    <th class="r">{{ translate('Share %') }}</th>
                    <th class="r">{{ translate('Amount') }}</th>
                    <th>{{ translate('Status') }}</th>
                </tr></thead>
                <tbody>
                    @forelse ($shares as $s)
                        <tr>
                            <td>
                                @if ($s->distribution)
                                    {{ \Carbon\Carbon::parse($s->distribution->period_from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($s->distribution->period_to)->format('d M Y') }}
                                @else — @endif
                            </td>
                            <td class="r">{{ $s->share_percent }}%</td>
                            <td class="r" style="font-weight:700">{{ single_price($s->amount) }}</td>
                            <td>
                                @if ($s->paid)
                                    <span class="badge paid">{{ translate('Paid') }}</span>
                                    <span class="muted" style="font-size:12px">{{ optional($s->paid_at)->format('d M Y') }}</span>
                                @else
                                    <span class="badge pending">{{ translate('Pending') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted" style="text-align:center;padding:26px">{{ translate('No distributions yet') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
