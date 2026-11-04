@extends('frontend.layouts.app')
{{-- herre --}}
@section('meta_title'){{ $page->getTranslationMetaTitleAttribute() }}@stop

@section('meta_description'){{ $page->getTranslationMetaDescriptionAttribute() }}@stop

@section('meta_keywords'){{ $page->getTranslationMetaKeywordAttribute() }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $page->getTranslationMetaTitleAttribute() }}">
    <meta itemprop="description" content="{{ $page->getTranslationMetaDescriptionAttribute() }}">
    <meta itemprop="image" content="{{ my_asset($page->meta_img) }}">
    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $page->getTranslationMetaTitleAttribute() }}">
    <meta name="twitter:description" content="{{ $page->getTranslationMetaDescriptionAttribute() }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ my_asset($page->meta_img) }}">
    <meta name="twitter:data1" content="{{ single_price($page->unit_price) }}">
    <meta name="twitter:label1" content="Price">
    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $page->getTranslationMetaTitleAttribute() }}" />
    <meta property="og:type" content="customer_page" />

    <meta property="og:image" content="{{ my_asset($page->meta_img) }}" />
    <meta property="og:description" content="{{ $page->getTranslationMetaDescriptionAttribute() }}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
    <meta property="og:price:amount" content="{{ single_price($page->unit_price) }}" />
@endsection

@section('content')
    <section class="bg-white ysg-custom-page-section">
        <div class="container">
            <div class="ysg-custom-page">
                @foreach($blocks as $block)
                    @if (!$block->isExpired())
                        <div class="{{isset($block->options->margin_top)?'mt-4':''}} {{isset($block->options->margin_bottom)?'mb-4':''}}">
                            @includeFirst(
                                [
                                    'frontend.custom_page.'.$page->type.'.'.$block->type,
                                    'frontend.custom_page.page.'.$block->type
                                ])
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endsection
