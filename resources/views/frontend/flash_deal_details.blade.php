@extends('frontend.layouts.app')
@section('content')
    @if ($flash_deal->status == 1 && strtotime(date('d-m-Y')) <= $flash_deal->end_date)
        <div style="background-color:{{ $flash_deal->background_color }}">
            <section class="text-center">
                <img src="{{ my_asset($flash_deal->banner) }}" alt="{{ $flash_deal->title }}" class="img-fit w-100">
            </section>

            <section class="pb-4">
                <div class="container">
                    <div class="text-center my-4 text-{{ $flash_deal->text_color }}">
                        <h1 class="h3">{{ $flash_deal->title }}</h1>
                        @php
                            $date = strtotime('+1 day', $flash_deal->end_date);
                        @endphp
                        <div class="countdown countdown-sm countdown--style-1"
                            data-countdown-date="{{ date('m/d/Y', $date) }}" data-countdown-label="show"></div>
                    </div>
                    <div class="gutters-5 row">
                        @foreach ($flash_deal->flashDealProducts as $key => $flash_deal_product)
                            @php
                                $product = $flash_deal_product->product;
                            @endphp
                            @if ($product->published != 0)
                                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                    <div class="product-card-2 card card-product shop-cards shop-tech mb-2">
                                        <div class="card-body p-0">
                                            <div class="card-image">
                                                <a href="{{ route('product', $product->slug) }}"
                                                    class="d-block text-center">
                                                    @if ($product->discount_badge != '-0%')
                                                        <div class="notify-badge">{{ $product->discount_badge }}</div>
                                                    @endif
                                                    <img class="img-fit lazyload"
                                                        src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                        data-src="{{ my_asset($product->thumbnail_img) }}"
                                                        alt="{{ __($product->translation_name) }}">
                                                </a>
                                            </div>
                                            <div class="p-3">
                                                @include('frontend.inc.product_list.price_box', ['product' => $product])
                                                <h2 class="product-title p-0 mt-2">
                                                    <a href="{{ route('product', $product->slug) }}"
                                                        class="text-truncate">{{ __($product->translation_name) }}</a>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    @else
        <div style="background-color:{{ $flash_deal->background_color }}">
            <section class="text-center pt-3">
                <div class="container ">
                    <img src="{{ my_asset($flash_deal->banner) }}" alt="{{ $flash_deal->title }}"
                        class="img-fit">
                </div>
            </section>
            <section class="pb-4">
                <div class="container">
                    <div class="text-center text-{{ $flash_deal->text_color }}">
                        <h1 class="h3 my-4">{{ $flash_deal->title }}</h1>
                        <p class="h4">{{ __('This offer has been expired.') }}</p>
                    </div>
                </div>
            </section>
        </div>
    @endif
@endsection
