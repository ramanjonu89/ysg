@extends('frontend.layouts.app')
@section('meta_title'){{ $shop->meta_title }}@stop
@section('meta_description'){{ $shop->meta_description }}@stop
@section('meta')
<!-- Schema.org markup for Google+ -->
<meta itemprop="name" content="{{ $shop->meta_title }}">
<meta itemprop="description" content="{{ $shop->meta_description }}">
<meta itemprop="image" content="{{ my_asset($shop->logo) }}">
<!-- Twitter Card data -->
<meta name="twitter:card" content="website">
<meta name="twitter:site" content="@publisher_handle">
<meta name="twitter:title" content="{{ $shop->meta_title }}">
<meta name="twitter:description" content="{{ $shop->meta_description }}">
<meta name="twitter:creator" content="@author_handle">
<meta name="twitter:image" content="{{ my_asset($shop->meta_img) }}">
<!-- Open Graph data -->
<meta property="og:title" content="{{ $shop->meta_title }}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ route('shop.visit', $shop->slug) }}" />
<meta property="og:image" content="{{ my_asset($shop->logo) }}" />
<meta property="og:description" content="{{ $shop->meta_description }}" />
<meta property="og:site_name" content="{{ $shop->name }}" />
@endsection

@section('content')
<!-- <section>
    <img loading="lazy"  src="https://via.placeholder.com/2000x300.jpg" alt="" class="img-fluid">
</section> -->

    @if (!isMicrosite())
        @include('frontend.seller_shop.inc.header')
    @endif

    <pre id="popupTitle" class="d-none"></pre>

    @if ($shop->user->isCommingSoon())
        @include('frontend.seller_shop.inc.comming_soon')
    @else
        @include('frontend.seller_shop.inc.menu')

        @if (!$type)
{{--            @include('frontend.seller_shop.inc.slider')--}}
            @include('frontend.microsite.blocks', ['blocks' => $blocks])
            @include('frontend.seller_shop.inc.featured_products', ['featuredProducts' => $featuredProducts])
            <style>
                .section-title-1-1:before{
                    background:white;
                }
                .section-title-1-1:after{
                    background:none;
                }
            </style>
        @endif
        @include('frontend.seller_shop.inc.products_list')
    @endif
@endsection

@section('script')
    <script src="{{ static_asset('/js/view_more.js') }}"></script>

    <script type="text/javascript">
        function filter() {
            $('#search-form').submit();
        }

        $(document).ready(function() {
            @if (session('is_confirm_age'))
                sessionStorage.setItem('isConfirmedRestrictedAge', {{ session('is_confirm_age') }});
            @endif
        });
    </script>
@endsection






