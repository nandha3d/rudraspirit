@extends('frontend.layouts.app')

@section('content')
<section style="background:var(--rs-cream);padding:54px 32px;text-align:center;">
    <div style="font-size:13px;letter-spacing:.24em;text-transform:uppercase;color:var(--rs-gold);">{{ translate('Home') }} / {{ translate('Stories') }}</div>
    <h1 class="rs-serif" style="font-weight:500;font-size:45px;letter-spacing:.06em;text-transform:uppercase;color:var(--rs-ink);margin:14px 0 0;">{{ translate('The') }} <em style="color:var(--rs-gold);font-style:italic;">{{ translate('Journal') }}</em></h1>
</section>

<section style="max-width:1280px;margin:0 auto;padding:60px 32px 90px;">
    @if ($blogs->count() > 0)
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:34px;">
            @foreach ($blogs as $post)
                <a href="{{ route('blog.details', $post->slug) }}" style="text-decoration:none;color:inherit;">
                    <div style="position:relative;aspect-ratio:4/3;border-radius:8px;overflow:hidden;background:linear-gradient(150deg,var(--rs-tan-light),var(--rs-tan));">
                        @if ($post->banner)
                            <img src="{{ uploaded_asset($post->banner) }}" alt="{{ $post->title }}" style="width:100%;height:100%;object-fit:cover;">
                        @endif
                    </div>
                    <h3 class="rs-serif" style="font-size:24px;font-weight:500;color:var(--rs-ink);margin:18px 0 10px;line-height:1.3;">{{ $post->title }}</h3>
                    <p style="font-size:16px;color:var(--rs-ink-soft);line-height:1.75;margin:0 0 12px;">{{ \Illuminate\Support\Str::limit(strip_tags($post->short_description), 120) }}</p>
                    <span style="font-size:13px;letter-spacing:.18em;text-transform:uppercase;color:var(--rs-ink);">{{ translate('Read More') }} &rarr;</span>
                </a>
            @endforeach
        </div>
        <div class="mt-5 d-flex justify-content-center">{{ $blogs->links() }}</div>
    @else
        <p style="text-align:center;color:var(--rs-ink-muted);padding:60px 0;">{{ translate('No posts yet') }}</p>
    @endif
</section>
@endsection
