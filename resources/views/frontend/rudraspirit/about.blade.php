@extends('frontend.layouts.app')

@section('content')
<section style="background:var(--rs-dark);color:var(--rs-cream);text-align:center;padding:90px 32px;">
    <div style="font-size:13px;letter-spacing:.28em;text-transform:uppercase;color:var(--rs-gold-light);margin-bottom:18px;">{{ translate('Our Story') }}</div>
    <h1 class="rs-serif" style="font-weight:500;font-size:50px;letter-spacing:.04em;text-transform:uppercase;margin:0;">{{ translate('Rooted in') }} <em style="color:var(--rs-gold-light);font-style:italic;">{{ translate('Devotion') }}</em></h1>
    <p style="font-size:18px;color:var(--rs-on-dark-muted);max-width:600px;margin:24px auto 0;line-height:1.9;">{{ translate('From the foothills of the Himalayas to your altar — every bead carries a lineage of faith, craft, and care.') }}</p>
</section>

<section style="max-width:1080px;margin:0 auto;padding:80px 32px;display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center;">
    <div style="aspect-ratio:4/5;border-radius:10px;overflow:hidden;">
        <img src="{{ static_asset('assets/img/pages/rudraspirit/Gemini_Generated_Image_9keguo9keguo9keg.webp') }}" alt="{{ translate('Rudra Spirit') }}" style="width:100%;height:100%;object-fit:cover;">
    </div>
    <div>
        <h2 class="rs-serif" style="font-weight:500;font-size:35px;color:var(--rs-ink);margin:0 0 20px;">{{ translate('A bead is a') }} <em style="color:var(--rs-gold);font-style:italic;">{{ translate('promise') }}</em></h2>
        <p style="font-size:17px;color:var(--rs-ink-soft);line-height:1.9;margin:0 0 16px;">{{ translate('Rudra Spirit began with a simple belief: that authenticity is sacred. Too many seekers receive carved, fused, or mislabelled beads. We set out to change that — working directly with trusted growers in Nepal and certifying every single bead before it ships.') }}</p>
        <p style="font-size:17px;color:var(--rs-ink-soft);line-height:1.9;margin:0;">{{ translate('Each Rudraksha is cleaned, energised through traditional ritual, and paired with a lab certificate so you can wear it with complete trust.') }}</p>
    </div>
</section>

<section style="background:var(--rs-cream);padding:70px 32px;">
    <div style="max-width:1080px;margin:0 auto;display:grid;grid-template-columns:repeat(3,1fr);gap:34px;text-align:center;">
        <div><div class="rs-serif" style="font-size:45px;color:var(--rs-gold);">100%</div><div style="font-size:14px;letter-spacing:.16em;text-transform:uppercase;color:var(--rs-ink-soft);margin-top:8px;">{{ translate('Certified Authentic') }}</div></div>
        <div><div class="rs-serif" style="font-size:45px;color:var(--rs-gold);">14</div><div style="font-size:14px;letter-spacing:.16em;text-transform:uppercase;color:var(--rs-ink-soft);margin-top:8px;">{{ translate('Mukhi Types') }}</div></div>
        <div><div class="rs-serif" style="font-size:45px;color:var(--rs-gold);">40+</div><div style="font-size:14px;letter-spacing:.16em;text-transform:uppercase;color:var(--rs-ink-soft);margin-top:8px;">{{ translate('Countries Served') }}</div></div>
    </div>
</section>
@endsection
