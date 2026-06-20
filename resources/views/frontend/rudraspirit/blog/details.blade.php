@extends('frontend.layouts.app')

@section('content')
<section style="background:var(--rs-cream);padding:54px 32px;text-align:center;">
    <div style="font-size:13px;letter-spacing:.24em;text-transform:uppercase;color:var(--rs-gold);">
        <a href="{{ route('blog') }}" style="color:inherit;text-decoration:none;">{{ translate('Journal') }}</a> / {{ $blog->title }}
    </div>
    <h1 class="rs-serif" style="font-weight:500;font-size:40px;color:var(--rs-ink);margin:14px auto 0;max-width:760px;">{{ $blog->title }}</h1>
</section>

<section style="max-width:760px;margin:0 auto;padding:54px 32px 90px;">
    @if ($blog->banner)
        <div style="aspect-ratio:16/9;border-radius:10px;overflow:hidden;margin-bottom:34px;">
            <img src="{{ uploaded_asset($blog->banner) }}" alt="{{ $blog->title }}" style="width:100%;height:100%;object-fit:cover;">
        </div>
    @endif
    <div style="font-size:18px;color:var(--rs-brown);line-height:1.9;">{!! $blog->description !!}</div>

    @if ($recent_blogs->count() > 1)
    <div style="margin-top:70px;">
        <h2 class="rs-serif" style="font-size:26px;font-weight:500;color:var(--rs-ink);margin:0 0 24px;">{{ translate('More Stories') }}</h2>
        <div style="display:flex;flex-direction:column;gap:14px;">
            @foreach ($recent_blogs->where('id', '!=', $blog->id)->take(5) as $post)
                <a href="{{ route('blog.details', $post->slug) }}" style="text-decoration:none;color:var(--rs-ink);font-size:17px;border-bottom:1px solid var(--rs-cream-deep);padding-bottom:12px;">{{ $post->title }}</a>
            @endforeach
        </div>
    </div>
    @endif
</section>
@endsection
