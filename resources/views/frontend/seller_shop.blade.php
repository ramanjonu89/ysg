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
@php

$total = 0;
$rating = 0;
foreach ($shop->user->products as $key => $seller_product) {
$total += $seller_product->reviews->count();
$rating += $seller_product->reviews->sum('rating');
}
$company_type =$shop->user->company_type;
@endphp

@if (!isMicrosite())
<section class="gry-bg pt-4 pb-2">
    <div class="container">
        <div class="row align-items-baseline">
            <div class="col-md-6">
                <div class="d-flex">
                    <img
                    height="70"
                    class="lazyload"
                    src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                    data-src="@if ($shop->logo !== null) {{ my_asset($shop->logo) }} @else {{ my_asset('frontend/images/placeholder.jpg') }} @endif"
                    alt="{{ $shop->name }}"
                    >
                    <div class="pl-4">
                        <h3 class="strong-700 heading-4 mb-0">{{ $shop->name }} @if ($company_type == 2) <span class="btn btn-success">PRIVATE EVENT</span> @endif
                            @if ($shop->user->seller->verification_status == 1)
                            <span class="ml-2"><i class="fa fa-check-circle" style="color:green"></i></span>
                            @else
                            <span class="ml-2"><i class="fa fa-times-circle" style="color:red"></i></span>
                            @endif
                        </h3>
                        <div class="star-rating star-rating-sm mb-1">
                            @if ($total > 0)
                            {{ renderStarRating($rating/$total) }}
                            @else
                            {{ renderStarRating(0) }}
                            @endif
                        </div>
                        <div class="location alpha-6">{{ $shop->address }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="text-md-right mt-4 mt-md-0 social-nav model-2">
                    @if ($shop->facebook != null)
                    <li>
                        <a href="{{ $shop->facebook }}" class="facebook social_a" target="_blank" data-toggle="tooltip" data-original-title="Facebook">
                            <i class="fa fa-facebook" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                    @if ($shop->twitter != null)
                    <li>
                        <a href="{{ $shop->twitter }}" class="twitter social_a" target="_blank" data-toggle="tooltip" data-original-title="Twitter">
                            <i class="fa fa-twitter" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                    @if ($shop->google != null)
                    <li>
                        <a href="{{ $shop->google }}" class="google-plus social_a" target="_blank" data-toggle="tooltip" data-original-title="Google">
                            <i class="fa fa-google-plus" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                    @if ($shop->youtube != null)
                    <li>
                        <a href="{{ $shop->youtube }}" class="youtube social_a" target="_blank" data-toggle="tooltip" data-original-title="Youtube">
                            <i class="fa fa-youtube" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                    @if ($shop->instagram != null)
                    <li>
                        <a href="{{ $shop->instagram }}" class="instagram social_a" target="_blank" data-toggle="tooltip" data-original-title="instagram">
                            <i class="fa fa-instagram" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                    @if ($shop->linkedin != null)
                    <li>
                        <a href="{{ $shop->linkedin }}" class="youtube social_a" target="_blank" data-toggle="tooltip" data-original-title="linkedin">
                            <i class="fa fa-linkedin" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</section>
@endif

@php
    $currentUserId = $currentUser ? $currentUser->id : "";
    $startDate = \Carbon\Carbon::parse($shop->user->company_start_date)->format('Y-m-d 00:00:00');
    $endDate = \Carbon\Carbon::parse($shop->user->company_end_date)->format('Y-m-d 23:59:59');
    $now = Carbon\Carbon::now();
@endphp

<pre id="popupTitle" class="d-none"></pre>

@if ($shop->user->company_type == 2 && ($startDate > $now || $endDate < $now))
    <section class="sct-color-1 pt-5 pb-4">
        <div class="container">
            <div class="section-title section-title--style-1 text-center mb-4">
                <h3 class="section-title-inner heading-3 strong-600">
                    {{ __('Coming soon') }}
                </h3>
            </div>
            <div class="row">
                <div class="col mb-4">
                    <div class="flipper" data-reverse="true" data-datetime="{{$shop->user->company_start_date}} 00:00:00" data-template="dd|HH|ii|ss" data-labels="Days|Hours|Minutes|Seconds" id="myFlipper"></div>
                </div>
            </div>
        </div>
    </section>
@else
    @if ($shop->user->company_type == 2 && ($startDate <= $now) && ($endDate > $now) &&
        $needAccessCode
    )
        <script>
            $(document).keyup(function(e) {
                if (e.key === "Escape") { // escape key maps to keycode `27`
                    window.location = "{{ config('app.url') }}";
                }
            });

            $( document ).ready(function() {
              if ("{{$currentUser}}" == "") {
                swal({
                  text: "Please login first to continue!",
                }).then(function() {
                  window.location = "{{ route('user.login') }}";
                });
              }
            });

        </script>
    @endif

    <section class="bg-white">
        <div class="container">
            <div class="row sticky-top">
                <div class="col">
                    <div class="seller-shop-menu">
                        <ul class="inline-links">
                            <li @if(!$type) class="active" @endif>
                                @if(isMicrosite())
                                    <a href="{{ route('microsite.index', $shop->slug) }}">{{ __('Store Home') }}</a>
                                @else
                                    <a href="{{ route('shop.visit', $shop->slug) }}">{{ __('Store Home') }}</a>
                                @endif
                            </li>
{{--                            <li @if($type && $type == 'top_selling') class="active" @endif><a href="{{ route('shop.visit.type', ['slug'=>$shop->slug, 'type'=>'top_selling']) }}">{{ __('Top Selling') }}</a></li>--}}
                            <li @if($type && $type == 'all_products') class="active" @endif><a href="{{ route('shop.visit.type', ['slug'=>$shop->slug, 'type'=>'all_products']) }}">{{ __('All Products') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (!$type)
        <section class="mb-4">
        <div class="container">
            <div class="px-2 py-4 p-md-4 shadow-sm" style="background: #4F136D !important; color: #fff;">
                <div class="section-title-1 section-title-1-1 clearfix">
                    <h3 class="heading-5 strong-700 mb-0 float-left">
                        <span class="mr-4">{{ __('Featured Products') }}</span>
                    </h3>
                </div>
                <div class="caorusel-box arrow-round gutters-5">
                    <div class="slick-carousel" data-slick-items="6" data-slick-xl-items="5" data-slick-lg-items="4"  data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2" data-slick-autoplay="true">
                              @foreach ($shop->user->products->where('published', 1)->where('featured', 1)->where('only_microsite', 0) as $key => $product)
                        <div class="caorusel-card">
                            <div class="product-card-2 card card-product shop-cards shop-tech">
                                <div class="card-body p-0">

                                 @if(home_price($product->id) != home_discounted_price($product->id))
                                     <div class="notify-badge">{{ $product->discount_badge }}</div>
                                     @endif


                                    <div class="card-image">
                                        <a href="{{ route('product', $product->slug) }}" class="d-block">
                                            <img class="img-fit lazyload mx-auto" src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($product->thumbnail_img) }}" alt="{{ __($product->name) }}">
                                        </a>
                                    </div>
                                    <div class="p-md-3 p-2">
                                        @include('frontend.inc.product_list.price_box', ['product' => $product])
                                        <div class="star-rating star-rating-sm mt-1">
                                            {{ renderStarRating($product->rating) }}
                                        </div>
                                        <h2 class="product-title p-0">
                                            <a href="{{ route('product', $product->slug) }}" class="text-truncate">
                                                @foreach($brands as $brand)
                                                    @if($brand->id == $product->brand_id)
                                                        {{ $brand->name }}
                                                        @break
                                                    @endif
                                                @endforeach
                                            </a>
                                        </h2>
                                        <div class="card-jfy-title"><a href="{{ route('product', $product->slug) }}" style="color:#000">{{ __($product->name) }}</a></div>
                                        @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                            <div class="club-point mt-2 bg-soft-base-1 border-light-base-1 border">
                                                {{ __('Club Point') }}:
                                                <span class="strong-700 float-right">{{ $product->earn_point }}</span>
                                            </div>
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

        <style>
            .section-title-1-1:before{
                background:white;
            }
            .section-title-1-1:after{
                background:none;
            }
        </style>
    @endif

    <section class="@if (!$type) gry-bg @endif py-4">
    <div class="container sm-px-0">
        <form class="" id="search-form" action="" method="GET">
            <div class="row">
                @if($is_include_filter)
                <div class="col-xl-3 side-filter d-xl-block">
                    <div class="filter-overlay filter-close"></div>
                    <div class="filter-wrapper c-scrollbar" style="max-height: 70vh; overflow-y: scroll">
                        <div class="filter-title d-flex d-xl-none justify-content-between pb-3 align-items-center">
                            <h3 class="h6">Filters</h3>
                            <button type="button" class="close filter-close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="bg-white sidebar-box">
                            <!-- Category Filter -->
                            <div class="box-title bg-white border-bottom-0 border-top">
                                <h6 class="font-weight-bold mb-0">{{ __('Categories') }}</h6>
                            </div>
                            <div class="box-content pr-3 pl-3 pb-3">
                                <!-- Filter by color -->
                                @foreach($categories as $key => $category)
                                    <div class="custom-control custom-checkbox {{ $key >= 5 ? 'category-hidden-item' : '' }}" style="{{ $key >= 5 ? 'display:none' : '' }}">
                                        <input name="category_ids[]" type="checkbox" class="custom-control-input"
                                               id="custom-cb-category-{{ $category->id }}" value="{{ $category->id }}"
                                               @isset($category_ids) @if (in_array($category->id, $category_ids)) checked @endif @endisset
                                               onchange="filter()"
                                        >
                                        <label class="custom-control-label" for="custom-cb-category-{{ $category->id }}">{{ $category->name }}</label>
                                    </div>
                                @endforeach
                                @if(count($categories) >= 6)
                                    <div class="text-center text-black-50">
                                            <span role="button" data-status="hide" id="view-more-categories">
                                                <span>{{ __('View more') }}</span>
                                                <i class="caret-icon fa fa-caret-down"></i>
                                            </span>
                                    </div>
                                @endif
                            </div>
                            <!-- END -->

                            <!-- Brand Filter -->
                            <div class="box-title bg-white border-bottom-0 border-top">
                                <h6 class="font-weight-bold mb-0">{{ __('Brands') }}</h6>
                            </div>
                            <div class="box-content pr-3 pl-3 pb-3">
                                <!-- Filter by brand -->
                                @foreach($brands as $key => $brand)
                                    <div class="custom-control custom-checkbox {{ $key >= 5 ? 'brand-hidden-item' : '' }}" style="{{ $key >= 5 ? 'display:none' : '' }}">
                                        <input name="brand_ids[]" type="checkbox" class="custom-control-input"
                                               id="custom-cb-brand-{{ $brand->id }}" value="{{ $brand->id }}"
                                               @isset($brand_ids) @if (in_array($brand->id, $brand_ids)) checked @endif @endisset
                                               onchange="filter()"
                                        >
                                        <label class="custom-control-label" for="custom-cb-brand-{{ $brand->id }}">{{ $brand->name }}</label>
                                    </div>
                                @endforeach
                                @if(count($brands) >= 6)
                                    <div class="text-center text-black-50">
                                            <span role="button" data-status="hide" id="view-more-brands">
                                                <span>{{ __('View more') }}</span>
                                                <i class="caret-icon fa fa-caret-down"></i>
                                            </span>
                                    </div>
                                @endif
                            </div>
                            <!-- END -->

                            <!-- Price Filter -->
                            <div class="box-title bg-white border-bottom-0 border-top">
                                <h6 class="font-weight-bold mb-0">{{ __('Price range') }}</h6>
                            </div>
                            <div class="box-content pr-3 pl-3 pb-3">
                                <div class="form-row d-flex align-items-center justify-content-between">
                                    <input name="min_price" value="{{ $min_price }}" type="number" placeholder="Min" class="form-control col-md-5">
                                    <div class="col-md-1 border-bottom border-dark"></div>
                                    <input name="max_price" value="{{ $max_price }}"  type="number" placeholder="Max" class="form-control col-md-5">
                                </div>
                            </div>
                            <!-- END -->

                            <!-- Color Filter -->
                            @if(count($all_colors) > 0)
                                <div class="box-title bg-white border-bottom-0 border-top">
                                    <h6 class="font-weight-bold mb-0">{{ __('Filter by color') }}</h6>
                                </div>
                                <div class="box-content pr-3 pl-3 pb-3">
                                    <!-- Filter by color -->
                                    <ul class="list-inline checkbox-color checkbox-color-circle mb-0">
                                        @foreach ($all_colors as $key => $color)
                                            <li>
                                                <input type="radio" id="color-{{ $key }}" name="color"
                                                       value="{{ $color }}"
                                                       @if(isset($selected_color) && $selected_color == $color) checked
                                                       @endif onchange="filter()">
                                                <label style="background: {{ $color }};" for="color-{{ $key }}"
                                                       data-toggle="tooltip"
                                                       data-original-title="{{ \App\Color::where('code', $color)->first()->name }}"></label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @foreach ($attributes as $key => $attribute)
                                @if (array_key_exists($attribute['id'], $existing_attributes))
                                    <div class="box-title bg-white border-bottom-0 border-top">
                                        <h6 class="font-weight-bold mb-0">
                                            Filter by {{ $existing_attributes[$attribute['id']] }}
                                        </h6>
                                    </div>
                                    <div class="box-content pr-3 pl-3 pb-3">
                                        <!-- Filter by others -->
                                        @foreach ($attribute['values'] as $key => $value)
                                            @php
                                                $flag = false;
                                                if (array_key_exists($attribute['id'], $selected_attributes)) {
                                                    if (in_array($value, $selected_attributes[$attribute['id']])) {
                                                        $flag = true;
                                                    }
                                                }

                                            @endphp
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="attribute_{{ $attribute['id'] }}_value_{{ $value }}"
                                                       name="attribute_{{ $attribute['id'] }}[]"
                                                       value="{{ $value }}" @if ($flag) checked
                                                       @endif onchange="filter()">
                                                <label class="custom-control-label"
                                                       for="attribute_{{ $attribute['id'] }}_value_{{ $value }}">{{ $value }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    </div>
                </div>
                @endif
                <div class="@if($is_include_filter) col-xl-9 @else col-xl-12 @endif">
                    @if($is_include_filter)
                    <div class="sort-by-bar row no-gutters bg-white mb-3 px-3 pt-2">
                        <div class="col-xl-7 d-flex d-xl-block justify-content-between align-items-end ">
                            <div class="sort-by-box flex-grow-1">
                                <div class="form-group">
                                    <label>{{ __('Search') }}</label>
                                    <div class="search-widget">
                                        <input class="form-control input-lg" type="text" name="q"
                                               placeholder="{{ __('Search products') }}"
                                               @isset($keyword) value="{{ $keyword }}" @endisset>
                                        <button type="submit" class="btn-inner">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-xl-none ml-3 form-group">
                                <button type="button" class="btn p-1 btn-sm" id="side-filter">
                                    <i class="la la-filter la-2x"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-xl-4 offset-xl-1">
                            <div class="row no-gutters">
                                <div class="col-12">
                                    <div class="sort-by-box px-1">
                                        <div class="form-group">
                                            <label>{{ __('Sort by') }}</label>
                                            <select class="form-control sortSelect"
                                                    data-minimum-results-for-search="Infinity" name="sort_by"
                                                    onchange="filter()">
                                                <option value="1"
                                                        @isset($sort_by) @if ($sort_by == '1') selected @endif @endisset>{{ __('Newest') }}</option>
                                                <option value="2"
                                                        @isset($sort_by) @if ($sort_by == '2') selected @endif @endisset>{{ __('Oldest') }}</option>
                                                <option value="3"
                                                        @isset($sort_by) @if ($sort_by == '3') selected @endif @endisset>{{ __('Price low to high') }}</option>
                                                <option value="4"
                                                        @isset($sort_by) @if ($sort_by == '4') selected @endif @endisset>{{ __('Price high to low') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="products-box-bar p-3 bg-white">
                        <div class="row sm-no-gutters gutters-5">
                            @foreach ($products as $key => $product)
                                <div class="col-xxl-3 col-xl-4 col-lg-3 col-md-4 col-6">
                                    <div class="card product-box-1 mb-3">
                                        <div class="card-image">
                                            @if(home_price($product->id) != home_discounted_price($product->id))
                                                <div class="notify-badge">{{ $product->discount_badge }}</div>
                                            @endif
                                            <a href="{{ route('product', $product->slug) }}" class="d-block text-center">
                                                <img class="img-fit lazyload" src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($product->thumbnail_img) }}" alt="{{  __($product->name) }}">
                                            </a>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="px-3 py-2">
                                                @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                                    <div class="club-point mb-2 bg-soft-base-1 border-light-base-1 border">
                                                        {{ __('Club Point') }}:
                                                        <span class="strong-700 float-right">{{ $product->earn_point }}</span>
                                                    </div>
                                                @endif
                                                <h2 class="product-title p-0">
                                                    <a href="{{ route('product', $product->slug) }}" class="text-truncate-1">
                                                        @foreach($brands as $brand)
                                                            @if($brand->id == $product->brand_id)
                                                                {{ $brand->name }}
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    </a>
                                                </h2>
                                                <div class="card-jfy-title"><a href="{{ route('product', $product->slug) }}" style="color:#000">{{ __($product->name) }}</a></div>
                                            </div>
                                            <div class="price-bar row no-gutters">
                                                <div class="price col-md-7">
                                                    @if(home_price($product->id) != home_discounted_price($product->id))
                                                        <del class="old-product-price">{{ home_base_price($product->id) }}</del>
                                                        <span class="product-price strong-600">{{ home_discounted_base_price($product->id) }}</span>
                                                    @else
                                                        <span class="product-price strong-600">{{ home_discounted_base_price($product->id) }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="star-rating star-rating-sm float-md-right">
                                                        {{ renderStarRating($product->rating) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cart-add d-flex">
                                                <button class="btn add-wishlist border-right" title="Add to Wishlist" onclick="addToWishList({{ $product->id }})">
                                                    <i class="la la-heart-o"></i>
                                                </button>
                                                <button class="btn add-compare border-right" title="Add to Compare" onclick="addToCompare({{ $product->id }})">
                                                    <i class="la la-refresh"></i>
                                                </button>
                                                @if ($product->current_stock > 0)
                                                <button type="button" class="btn btn-block btn-icon-left" onclick="showAddToCartModal({{ $product->id }})">
                                                    <span class="d-none d-sm-inline-block">{{ __('Add to cart') }}</span><i class="la la-shopping-cart ml-2"></i>
                                                </button>
                                                @else
                                                <button type="button" class="btn btn-block btn-icon-left bg-red" disabled="">
                                                    <span class="d-none d-sm-inline-block">{{ __('Out of stock') }}</span><i class="la la-shopping-cart ml-2"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="products-pagination bg-white p-3">
                        <nav aria-label="Center aligned pagination">
                            <ul class="pagination justify-content-center">
                                {{ $products->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endif

@endsection


@section('script')
    <script src="{{ static_asset('/js/view_more.js') }}"></script>
@endsection


<script type="text/javascript">
  function filter(){
    $('#search-form').submit();
  }
</script>

    <style>
        body {
        font-family: sans-serif;
        }
        .flipper {
        color: #333;
        display: block;
        font-size: 50px;
        line-height: 100%;
        padding: 0;
        margin: 0;
        height: 1.7em;
        }
        .flipper.flipper-invisible {
        font-size: 0px !important;
        }
        .flipper-group {
        position: relative;
        white-space: nowrap;
        display: block;
        float: left;
        padding: 0;
        margin: 0;
        }
        .flipper-group label {
        position: absolute;
        color: #000;
        font-size: 20%;
        top: 100%;
        line-height: 1em;
        left: 50%;
        -webkit-transform: translate(-50%, 0);
        transform: translate(-50%, 0);
        text-align: center;
        padding-top: .5em;
        }
        .flipper-digit {
        white-space: nowrap;
        position: relative;
        padding: 0;
        margin: 0;
        display: inline-block;
        float: left;
        height: 1.2em;
        overflow-y: hidden;
        }
        .flipper-digit span {
        font-size: 25%;
        }
        .flipper-delimiter {
        white-space: nowrap;
        display: block;
        float: left;
        padding: 0;
        margin: 0;
        color: #fff;
        min-width: .1em;
        white-space: nowrap;
        display: block;
        padding-top: 0.1em;
        padding-bottom: 0.1em;
        line-height: 1em;
        }
        .digit-face {
        display: block;
        visibility: hidden;
        position: relative;
        border-radius: 0.1em;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 8;
        padding-top: 0.1em;
        padding-bottom: 0.1em;
        padding-left: 0.1em;
        padding-right: 0.1em;
        box-sizing: border-box;
        text-align: center;
        }
        .digit-next {
        display: block;
        position: relative;
        border-radius: 0.1em;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 8;
        height: 1.2em;
        background: #fff;
        padding-top: 0.1em;
        padding-bottom: 0.1em;
        padding-left: 0.1em;
        padding-right: 0.1em;
        box-sizing: border-box;
        text-align: center;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        }
        .digit-top {
        z-index: 10;
        top: 0;
        left: 0;
        right: 0;
        height: 50%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        pointer-events: none;
        overflow: hidden;
        position: absolute;
        background: #fff;
        padding-top: 0.1em;
        padding-bottom: 0;
        padding-left: 0.1em;
        padding-right: 0.1em;
        border-top-left-radius: 0.1em;
        border-top-right-radius: 0.1em;
        box-sizing: border-box;
        text-align: center;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        transition: background 0s linear, -webkit-transform 0s linear;
        transition: transform 0s linear, background 0s linear;
        transition: transform 0s linear, background 0s linear, -webkit-transform 0s linear;
        -webkit-transform-origin: 0 0.6em 0 !important;
        transform-origin: 0 0.6em 0 !important;
        -webkit-transform-style: preserve-3d !important;
        transform-style: preserve-3d !important;
        z-index: 20;
        }
        .digit-top.r {
        transition: background 0.2s linear, -webkit-transform 0.2s linear;
        transition: transform 0.2s linear, background 0.2s linear;
        transition: transform 0.2s linear, background 0.2s linear, -webkit-transform 0.2s linear;
        -webkit-transform: rotateX(90deg);
        transform: rotateX(90deg);
        background: #cccccc;
        }
        .digit-top2 {
        visibility: hidden;
        position: absolute;
        height: 50%;
        left: 0;
        right: 0;
        background: #cccccc;
        transition: -webkit-transform 0.2s linear;
        transition: transform 0.2s linear;
        transition: transform 0.2s linear, -webkit-transform 0.2s linear;
        line-height: 0em !important;
        top: 50% !important;
        bottom: auto !important;
        padding-top: 0;
        padding-bottom: 0.1em;
        padding-left: 0.1em;
        padding-right: 0.1em;
        border-bottom-left-radius: 0.1em;
        border-bottom-right-radius: 0.1em;
        overflow: hidden;
        text-align: center;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        transition: background 0s linear, -webkit-transform 0s linear;
        transition: transform 0s linear, background 0s linear;
        transition: transform 0s linear, background 0s linear, -webkit-transform 0s linear;
        -webkit-transform: rotateX(-90deg);
        transform: rotateX(-90deg);
        -webkit-transform-style: preserve-3d !important;
        transform-style: preserve-3d !important;
        -webkit-transform-origin: 0 0 0 !important;
        transform-origin: 0 0 0 !important;
        z-index: 20;
        }
        .digit-top2.r {
        visibility: visible;
        transition: background 0.2s linear 0.2s, -webkit-transform 0.2s linear 0.2s;
        transition: transform 0.2s linear 0.2s, background 0.2s linear 0.2s;
        transition: transform 0.2s linear 0.2s, background 0.2s linear 0.2s, -webkit-transform 0.2s linear 0.2s;
        -webkit-transform: rotateX(0deg);
        transform: rotateX(0deg);
        background: #fff;
        }
        .digit-bottom {
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        pointer-events: none;
        position: absolute;
        overflow: hidden;
        background: #fff;
        height: 50%;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9;
        line-height: 0em;
        padding-top: 0;
        padding-bottom: 0.1em;
        padding-left: 0.1em;
        padding-right: 0.1em;
        border-bottom-left-radius: 0.1em;
        border-bottom-right-radius: 0.1em;
        box-sizing: border-box;
        text-align: center;
        transition: none;
        }
        .digit-bottom.r {
        transition: background 0.2s linear;
        background: #cccccc;
        }
        .flipper-digit:after {
        content: "";
        position: absolute;
        height: 2px;
        background: rgba(0, 0, 0, 0.5);
        top: 50%;
        display: block;
        z-index: 30;
        left: 0;
        right: 0;
        }
        .flipper-dark {
        color: #fff;
        }
        .flipper-dark .flipper-delimiter {
        color: #333;
        }
        .flipper-dark .digit-next {
        background: #333;
        }
        .flipper-dark .digit-top {
        background: #333;
        }
        .flipper-dark .digit-top.r {
        background: black;
        }
        .flipper-dark .digit-top2 {
        background: black;
        }
        .flipper-dark .digit-top2.r {
        background: #333;
        }
        .flipper-dark .digit-bottom {
        background: #333;
        }
        .flipper-dark-labels .flipper-group label {
        color: #333;
        }
        .slick-slide {
        display: none;
        float: left;
        height: auto !important;
        min-height: 1px;
        }
        .swal-text{
            text-align:center !important;
        }
        .featured-back .slick-list{
            background: #4F136D !important;
        }
        .text-truncate-1 {
        }
    </style>



