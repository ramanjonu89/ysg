@extends('frontend.layouts.app')

@section('meta_title'){{ $shop->meta_title }}@stop
@section('meta_description'){{ $shop->meta_description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $shop->meta_title }}">
    <meta itemprop="description" content="{{ $shop->meta_description }}">
    <meta itemprop="image" content="{{ my_asset($shop->logo) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $shop->meta_title }}">
    <meta name="twitter:description" content="{{ $shop->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ my_asset($shop->meta_img) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $shop->meta_title }}" />
    <meta property="og:type" content="Shop" />
    <meta property="og:url" content="{{ route('shop.visit', $shop->slug) }}" />
    <meta property="og:image" content="{{ my_asset($shop->logo) }}" />
    <meta property="og:description" content="{{ $shop->meta_description }}" />
    <meta property="og:site_name" content="{{ $shop->name }}" />
@endsection

@section('content')

    <pre id="popupTitle" class="d-none"></pre>
    <section class="bg-white">
        <div class="container">
            <div class="row sticky-top">
                <div class="col">
                    <div class="seller-shop-menu">
                        <ul class="inline-links">
                            <li class="active"><a href="{{ route('microsite.index', ['slug' => $shop->slug]) }}">Store Home</a></li>

                            <li><a href="{{ route('shop.visit.type', ['slug' => $shop->slug, 'type' => 'all_products']) }}">All Products</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            @include('frontend.microsite.blocks')
        </div>
    </section>

    {{-- @include('frontend/microsite/feature_list')
    @include('frontend/microsite/search_bar')
    @include('frontend/microsite/product_list') --}}

    
@endsection
