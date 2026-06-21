@extends('frontend.layouts.app')

@section('content')
@php
    $rsFaqs = [
        ['q' => 'What is a Rudraksha and where does it come from?', 'a' => 'Rudraksha are the dried seeds of the Elaeocarpus ganitrus tree, traditionally associated with Lord Shiva. Ours are sourced primarily from Nepal, prized for their large size and well-defined faces (mukhis).'],
        ['q' => 'How do I know my bead is authentic?', 'a' => 'Every bead ships with a lab certificate confirming its origin and mukhi count, verified by X-ray. We never sell artificially carved or fused beads.'],
        ['q' => 'What does "mukhi" mean?', 'a' => 'Mukhi refers to the number of natural clefts, or faces, running down the bead. Each mukhi count carries a distinct ruling deity and spiritual benefit.'],
        ['q' => 'How should I care for my Rudraksha?', 'a' => 'Keep it away from harsh chemicals, oil it occasionally with sandalwood or mustard oil, and recharge it by wearing it or placing it near your altar.'],
        ['q' => 'Do you ship internationally?', 'a' => 'Yes — we ship worldwide with secured payment gateways and accept all major cards. Prices are shown in your selected currency.'],
        ['q' => 'What is your return policy?', 'a' => 'We offer a 14-day return window on unworn items in their original certified packaging.'],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(function ($faq) {
        return [
            '@type' => 'Question',
            'name' => translate($faq['q']),
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => translate($faq['a'])],
        ];
    }, $rsFaqs),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<section style="background:var(--rs-cream);padding:54px 32px;text-align:center;">
    <div style="font-size:13px;letter-spacing:.24em;text-transform:uppercase;color:var(--rs-gold);">{{ translate('Home') }} / {{ translate('FAQ') }}</div>
    <h1 class="rs-serif" style="font-weight:500;font-size:45px;letter-spacing:.06em;text-transform:uppercase;color:var(--rs-ink);margin:14px 0 0;">{{ translate('Frequently') }} <em style="color:var(--rs-gold);font-style:italic;">{{ translate('Asked') }}</em></h1>
</section>

<section style="max-width:780px;margin:0 auto;padding:54px 32px 90px;">
    @foreach ($rsFaqs as $i => $faq)
        <div class="rs-faq-item" style="border-bottom:1px solid var(--rs-cream-deep);padding:24px 0;cursor:pointer;" onclick="this.classList.toggle('open')">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:20px;">
                <span class="rs-serif" style="font-size:22px;color:var(--rs-ink);">{{ translate($faq['q']) }}</span>
                <span style="color:var(--rs-gold);font-size:21px;flex:none;">+</span>
            </div>
            <p class="rs-faq-answer" style="font-size:17px;color:var(--rs-ink-soft);line-height:1.85;margin:14px 0 0;max-width:640px;display:none;">{{ translate($faq['a']) }}</p>
        </div>
    @endforeach
</section>

<style>
    .rs-faq-item.open .rs-faq-answer { display: block; }
</style>
@endsection
