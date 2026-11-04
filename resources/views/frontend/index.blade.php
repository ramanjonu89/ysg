@extends('frontend.layouts.app')

@section('content')

    <section class="home-banner-area mb-4">
        <div class="container">
            <div class="row no-gutters position-relative">
                <div
                    class="@if($numTodaysDeal > 0) col-lg-10 @else col-lg-12 @endif order-1 order-lg-0 @if(count($featuredCategories) == 0) home-slider-full @endif">
                    <div class="home-slide">
                        <div class="home-slide">
                            <div class="slick-carousel" data-slick-arrows="true" data-slick-dots="true"
                                data-slick-autoplay="true" data-slick-autoplay-speed="8000" data-slick-fade="true"
                                data-slick-speed="1200">
                                @inject('sliderService', 'App\Services\SliderService')
                                @foreach ($sliderService->getPublishedList() as $key => $slider)
                                    @if(!$slider->isExpired())
                                        <div class="home-slide-item" style="height:450px;">
                                            <a href="{{ $slider->link }}">
                                                <img class="d-block w-100 h-100 lazyload"
                                                    src="{{ my_asset('frontend/images/placeholder-rect.jpg') }}"
                                                    data-src="{{ my_asset($slider->photo) }}"
                                                    alt="{{ env('APP_NAME') }} promo">
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @if (isset($numTodaysDeal) && $numTodaysDeal > 0)
                    <div class="col-lg-2 d-none d-lg-block">
                        <div class="flash-deal-box bg-white h-100">
                            <div class="title text-center p-2 gry-bg">
                                <h3 class="heading-6 mb-0">
                                    {{ __('Todays Deal') }}
                                    <span class="badge badge-danger">{{ __('Hot') }}</span>
                                </h3>
                            </div>
                            <div class="flash-content c-scrollbar c-height">
                                @foreach (filter_products(\App\Models\Product::where('published', 1)->where('todays_deal', '1'))->get() as $key => $product)
                                    @if ($product != null)
                                        <a href="{{ route('product', $product->slug) }}" class="d-block flash-deal-item">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col">
                                                    <div class="img">
                                                        <img class="lazyload img-fit"
                                                            src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                            data-src="{{ my_asset($product->thumbnail_img) }}"
                                                            alt="{{ __($product->translation_name) }}">
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="price">
                                                        <span
                                                            class="d-block">{{ home_discounted_base_price($product->id) }}</span>
                                                        @if (home_base_price($product->id) != home_discounted_base_price($product->id))
                                                            <del
                                                                class="d-block">{{ home_base_price($product->id) }}</del>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if (isset($flashDeal) && $flashDeal != null && strtotime(date('d-m-Y')) >= $flashDeal->start_date && strtotime(date('d-m-Y')) <= $flashDeal->end_date)
        <section class="mb-4">
            <div class="container">
                <div class="px-2 py-4 p-md-4 bg-white shadow-sm">
                    <div class="section-title-1 clearfix ">
                        <h3 class="heading-5 strong-700 mb-0 float-left">
                            {{ __('Hot Deals') }}
                        </h3>
                        <div class="flash-deal-box float-left">
                            @php
                                $date = strtotime('+1 day', $flashDeal->end_date);
                            @endphp
                            <div class="countdown countdown--style-1 countdown--style-1-v1 "
                                data-countdown-date="{{ date('m/d/Y', $date) }}" data-countdown-label="show"></div>
                        </div>
                        <ul class="inline-links float-right nav mt-3 mb-2 m-lg-0">
                            <li><a href="{{ route('flash-deal-details', $flashDeal->slug) }}"
                                    class="active">{{ __('View More') }}</a></li>
                        </ul>
                    </div>
                    <div class="caorusel-box arrow-round gutters-5">
                        <div class="slick-carousel" data-slick-items="6" data-slick-xl-items="5" data-slick-lg-items="4"
                            data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2">
                            @foreach ($flashDeal->flashDealProducts as $key => $flashDeal_product)
                                @php
                                    $product = $flashDeal_product->product;
                                @endphp
                                @if ($product != null && $product->published != 0)
                                    <div class="caorusel-card">
                                        <div class="product-card-2 card card-product shop-cards">
                                            <div class="card-body p-0">
                                                <div class="card-image">
                                                    @if (home_base_price($product->id) != home_discounted_base_price($product->id))
                                                        <div class="notify-badge">{{ $product->discount_badge }}</div>
                                                    @endif
                                                    <a href="{{ route('product', $product->slug) }}"
                                                        class="d-block">
                                                        <img class="img-fit lazyload mx-auto"
                                                            src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                            data-src="{{ my_asset($product->thumbnail_img) }}"
                                                            alt="{{ __($product->translation_name) }}">
                                                    </a>
                                                </div>
                                                <div class="p-md-3 p-2">
                                                    @include('frontend.inc.product_list.price_box', ['product' => $product])
                                                    <div class="star-rating star-rating-sm mt-1">
                                                        {{ renderStarRating($product->rating) }}
                                                    </div>
                                                    <h2 class="product-title p-0">
                                                        <a href="{{ route('product', $product->slug) }}"
                                                            class=" text-truncate">{{ __($product->translation_name) }}</a>
                                                    </h2>
                                                    @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                                        <div
                                                            class="club-point mt-2 bg-soft-base-1 border-light-base-1 border">
                                                            {{ __('Club Point') }}:
                                                            <span
                                                                class="strong-700 float-right">{{ $product->earn_point }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @component('frontend.home.banner', ['listBanner' => $banners1])
    @endcomponent

    @include('frontend.home.home_blocks')

    @if (config('business_setting.vendor_system_activation') == 1)
        <div id="section_best_sellers">
        </div>
    @endif

    <div id="section_featured">
    </div>
    <div id="section_best_selling">
    </div>
    <div id="section_home_categories">
    </div>
    @if (checkNullThenCompareValue($businessSetting, 'classified_product', 1))
        @if (isset($customerProducts) && count($customerProducts) > 0)
            <section class="mb-4">
                <div class="container">
                    <div class="px-2 py-4 p-md-4 bg-white shadow-sm">
                        <div class="section-title-1 clearfix">
                            <h3 class="heading-5 strong-700 mb-0 float-left">
                                <span class="mr-4">{{ __('Classified Ads') }}</span>
                            </h3>
                            <ul class="inline-links float-right">
                                <li><a href="{{ route('customer.products') }}"
                                        class="active">{{ __('View More') }}</a></li>
                            </ul>
                        </div>
                        <div class="caorusel-box arrow-round">
                            <div class="slick-carousel" data-slick-items="6" data-slick-xl-items="5" data-slick-lg-items="4"
                                data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2">
                                @foreach ($customerProducts as $key => $customer_product)
                                    <div class="product-card-2 card card-product my-2 mx-1 mx-sm-2 shop-cards shop-tech">
                                        <div class="card-body p-0">
                                            <div class="card-image">
                                                <a href="{{ route('customer.product', $customer_product->slug) }}"
                                                    class="d-block">
                                                    <img class="img-fit lazyload mx-auto"
                                                        src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                        data-src="{{ my_asset($customer_product->thumbnail_img) }}"
                                                        alt="{{ __($customer_product->name) }}">
                                                </a>
                                            </div>
                                            <div class="p-sm-3 p-2">
                                                <div class="price-box">
                                                    <span
                                                        class="product-price strong-600">{{ single_price($customer_product->unit_price) }}</span>
                                                </div>
                                                <h2 class="product-title p-0 text-truncate-1">
                                                    <a
                                                        href="{{ route('customer.product', $customer_product->slug) }}">{{ __($customer_product->name) }}</a>
                                                </h2>
                                                <div>
                                                    @if ($customer_product->conditon == 'new')
                                                        <span class="product-label label-hot">{{ __('new') }}</span>
                                                    @elseif($customer_product->conditon == 'used')
                                                        <span class="product-label label-hot">{{ __('Used') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endif

    @component('frontend.home.banner', ['listBanner' => $banners2])
    @endcomponent
    @if(count($topCategories) > 0 && count($topBrands) > 0)
        <section class="mb-3">
            <div class="container">
                <div class="row gutters-10">
                    <div class="col-lg-6">
                        <div class="section-title-1 clearfix">
                            <h3 class="heading-5 strong-700 mb-0 float-left">
                                <span class="mr-4">{{ __('Shop by Categories') }}</span>
                            </h3>
                            <ul class="float-right inline-links">
                                <li>
                                    <a href="{{ route('categories.all') }}"
                                        class="active">{{ __('View All Categories') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="row gutters-5">
                            @foreach ($topCategories as $category)
                                <div class="mb-3 col-6">
                                    <a href="{{ route('category.view', $category->slug) }}"
                                        class="bg-white border d-block c-base-2 box-2 icon-anim pl-2">
                                        <div class="row align-items-center no-gutters">
                                        <!--<div class="col-3 text-center">
                                        <img src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($category->banner) }}" alt="{{ $category->getTranslationNameAttribute() }}" class="img-fluid img lazyload">
                                    </div>-->
                                            <div class="info col-10">
                                                <div
                                                    class="name text-truncate pl-3 py-4">{{ $category->getTranslationNameAttribute() }}</div>
                                            </div>
                                            <div class="col-2 text-center">
                                                <i class="la la-angle-right c-base-1"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="section-title-1 clearfix">
                            <h3 class="heading-5 strong-700 mb-0 float-left">
                                <span class="mr-4">{{ __('Shop by Brands') }}</span>
                            </h3>
                            <ul class="float-right inline-links">
                                <li>
                                    <a href="{{ route('brands.all') }}"
                                        class="active">{{ __('View All Brands') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="row gutters-5">
                            @foreach ($topBrands as $brand)
                                <div class="mb-3 col-6">
                                    <a href="{{ URL::route('search', ['brand_ids' => [$brand->id]]) }}"
                                        class="bg-white border d-block c-base-2 box-2 icon-anim pl-2">
                                        <div class="row align-items-center no-gutters">
                                            <div class="col-3 text-center">
                                                <img src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                    data-src="{{ my_asset($brand->logo) }}"
                                                    alt="{{ __($brand->name) }}" class="img-fluid img lazyload">
                                            </div>
                                            <div class="info col-7">
                                                <div class="name text-truncate pl-3 py-4">{{ __($brand->name) }}</div>
                                            </div>
                                            <div class="col-2 text-center">
                                                <i class="la la-angle-right c-base-1"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $.post('{{ route('home.section.featured') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_featured').html(data);
                slickInit();
            });
            $.post('{{ route('home.section.best_selling') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_best_selling').html(data);
                slickInit();
            });
            $.post('{{ route('home.section.home_categories') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_home_categories').html(data);
                slickInit();
            });
            @if (config('business_setting.vendor_system_activation') == 1)
                $.post('{{ route('home.section.best_sellers') }}', {_token: '{{ csrf_token() }}'}, function (data) {
                $('#section_best_sellers').html(data);
                slickInit();
                });
            @endif
        });
        localStorage.setItem("welcome", "tome1");
    </script>
    <style>
        @media (max-width: 692px) {
            .float-lg-right {
                float: right !important;
                position: relative;
                margin-top: -20px !important;
            }
        }

    </style>
@endsection
