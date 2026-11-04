@extends('frontend.layouts.app')
@section('meta_title'){{ $detailedProduct->translation_meta_title }}@stop
@section('meta_description'){{ $detailedProduct->translation_meta_description }}@stop
@section('meta_keywords'){{ $detailedProduct->tags }}@stop
@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $detailedProduct->translation_meta_title }}">
    <meta itemprop="description" content="{{ $detailedProduct->translation_meta_description }}">
    <meta itemprop="image" content="{{ my_asset($detailedProduct->meta_img) }}">
    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $detailedProduct->translation_meta_title }}">
    <meta name="twitter:description" content="{{ $detailedProduct->translation_meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ my_asset($detailedProduct->meta_img) }}">
    <meta name="twitter:data1" content="{{ single_price($detailedProduct->unit_price) }}">
    <meta name="twitter:label1" content="Price">
    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $detailedProduct->translation_meta_title }}" />
    <meta property="og:type" content="og:product" />
    <meta property="og:url" content="{{ route('product', $detailedProduct->slug) }}" />
    <meta property="og:image" content="{{ my_asset($detailedProduct->meta_img) }}" />
    <meta property="og:description" content="{{ $detailedProduct->translation_meta_description }}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
    <meta property="og:price:amount" content="{{ single_price($detailedProduct->unit_price) }}" />
    <meta property="product:price:currency"
        content="{{ \App\Currency::findOrFail($businessSettings['system_default_currency'])->code }}" />
    <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">

    <meta property="og:image:width" content="300">
    <meta property="og:image:height" content="300">
@endsection
@section('content')

    @php
        $shop           = \App\Shop::where('user_id', $detailedProduct->user->shop->user_id)->first();
        $startDate      = \Carbon\Carbon::parse($shop->user->company_start_date)->format('Y-m-d 00:00:00');
        $endDate        = \Carbon\Carbon::parse($shop->user->company_end_date)->format('Y-m-d 23:59:59');
        $now            = Carbon\Carbon::now();
        $currentUserId  = $currentUser ? $currentUser->id : '';
        $isConfirmAge   = Session::get('is_confirm_age');
        $attributes     = json_decode($detailedProduct->attributes);
    @endphp
    <pre id="popupTitle" class="d-none"></pre>
    @if ($shop->user->company_type == 2 && $startDate <= $now && $endDate > $now)
        <script>
            $(document).keyup(function(e) {
                if (e.key === "Escape") { // escape key maps to keycode `27`
                    window.location = "{{ config('app.url') }}";
                }
            });

            if ("{{ $currentUser }}" == "") {
                swal({
                    text: "Please login first to continue!",
                }).then(function() {
                    window.location = "{{ route('user.login') }}";
                });
            }
        </script>
    @endif

    <!-- SHOP GRID WRAPPER -->
    <section class="product-details-area gry-bg">
        <div class="container">
            <div class="bg-white">
                <!-- Product gallery and Description -->
                <div class="row no-gutters cols-xs-space cols-sm-space cols-md-space">
                    <div class="col-lg-6">
                        <div class="product-gal sticky-top d-flex flex-row-reverse normal-image">
                            @if (is_array(json_decode($detailedProduct->photos)) && count(json_decode($detailedProduct->photos)) > 0)
                                <div class="product-gal-img">
                                    <img src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                        class="xzoom img-fluid lazyload"
                                        src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                        style="max-height: 550px; margin: auto; left: 0; right: 0; display: block;"
                                        data-src="{{ my_asset(json_decode($detailedProduct->photos)[0]) }}"
                                        xoriginal="{{ my_asset(json_decode($detailedProduct->photos)[0]) }}" />
                                </div>
                                <div class="product-gal-thumb">
                                    <div class="xzoom-thumbs">
                                        @foreach (json_decode($detailedProduct->photos) as $key => $photo)
                                            <a href="{{ my_asset($photo) }}" @if($photo == $detailedProduct->thumbnail_img) preview="1" @endif>
                                                <img class="xzoom-gallery lazyload"
                                                    src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                    data-src="{{ my_asset($photo) }}"
                                                    xpreview="{{ my_asset($photo) }}"
                                                    style="width:50px;height:50px;object-fit:contain;">
                                            </a>
                                        @endforeach

                                        @foreach ($detailedProduct->stocks as $stockIndex => $stock)
                                            @if ($stock->image)
                                                <a href="{{ my_asset($stock->image) }}" data-variant="{{$stock->variant}}">
                                                    <img src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                        class="xzoom-gallery lazyload"
                                                        src="{{ my_asset('frontend/images/placeholder.jpg') }}" width="80"
                                                        style="width: 50px; height: 50px; object-fit: contain; object-position: center;"
                                                        data-src="{{ my_asset($stock->image) }}"
                                                        @if ($key == 0) xpreview="{{ my_asset($stock->image) }}" @endif>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <!-- Product description -->
                        <div class="product-description-wrapper">
                            <!-- Product title -->
                            <h1 class="product-title mb-2">
                                {{ __($detailedProduct->translation_name) }}
                            </h1>
                            <div class="row align-items-center my-1">
                                <div class="col-6">
                                    <!-- Rating stars -->
                                    <div class="rating">
                                        @php
                                            $total = 0;
                                            $total += $detailedProduct->reviews->count();
                                        @endphp
                                        <span class="star-rating">
                                            {{ renderStarRating($detailedProduct->rating) }}
                                        </span>
                                        <span class="rating-count ml-1">({{ $total }} {{ __('reviews') }})</span>
                                    </div>
                                </div>
                                <div class="col-6 text-right">
                                    <ul class="inline-links inline-links--style-1">
                                        @if ($detailedProduct->quantity > 0)
                                            <li>
                                                <span
                                                    class="badge badge-md badge-pill bg-green">{{ __('In stock') }}</span>
                                            </li>
                                        @else
                                            <li>
                                                <span
                                                    class="badge badge-md badge-pill bg-red">{{ __('Out of stock') }}</span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <hr>
                            <div class="row align-items-center">
                                <div class="sold-by col-auto">
                                    <small class="mr-2">{{ __('Sold by') }}: </small><br>
                                    @if ($detailedProduct->added_by == 'seller' && array_key_exists('vendor_system_activation', $businessSettings) && $businessSettings['vendor_system_activation'] == 1)
                                        <a
                                            href="{{ getShopLink($detailedProduct->user->shop->slug) }}">{{ $detailedProduct->user->shop->name }}</a>
                                    @else
                                        {{ __('Inhouse product') }}
                                    @endif
                                </div>
                                @if (array_key_exists('conversation_system', $businessSettings) && $businessSettings['conversation_system'] == 1)
                                    <div class="col-auto">
                                        <button class="btn btn-secondary"
                                            onclick="show_chat_modal()">{{ __('Message Seller') }}</button>
                                    </div>
                                @endif
                                <!-- @if ($detailedProduct->brand != null)
    <div class="col-auto">
                                    <img src="{{ my_asset($detailedProduct->brand->logo) }}" alt="{{ $detailedProduct->brand->name }}" height="30">
                                    </div>
    @endif-->
                            </div>
                            <hr>
                            @if ($detailedProduct->stocks->count())
                                <div class="row align-items-center no-gutters mt-4">
                                    <div class="col-3">
                                        <div class="product-description-label">{{ __('Price') }}:</div>
                                    </div>
                                    <div class="col-9">
                                        <div class="row d-flex align-items-center">
                                            <div class="d-flex pr-0 text-center" id="del_unit_price">
                                                @if ($detailedProduct->range_unit_price != home_discounted_price($detailedProduct->id))
                                                    <del class="product-price-old pr-3 pl-2 mt-1">
                                                        <span class="" id="range_unit_price">
                                                            {{ $detailedProduct->range_unit_price }}
                                                        </span>
                                                        <span class="d-none" id="chonse_unit_price">

                                                        </span>
                                                    </del>
                                                    <div class="product-price font-weight-bold" id="lowest_highest_price">
                                                        {{ home_discounted_price($detailedProduct->id) }}
                                                    </div>
                                                    <div class="product-price d-none" id="variant_price">
                                                        <strong id="chosen_price">

                                                        </strong>
                                                    </div>
                                                    <div class="product-price-old pr-2 pl-3 mt-1">
                                                        <span class="" id="range_unit_price">({{ $detailedProduct->discount_badge }})</span>
                                                        <span class="d-none" id="chonse_discount_badge">

                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="product-price font-weight-bold" id="lowest_highest_price">
                                                        {{ home_discounted_price($detailedProduct->id) }}
                                                    </div>
                                                    <div class="product-price d-none" id="variant_price">
                                                        <strong id="chosen_price">

                                                        </strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row no-gutters mt-3">
                                    <div class="col-3">
                                        <div class="product-description-label">{{ __('Price') }}:</div>
                                    </div>
                                    <div class="col-9">
                                        <div class="row d-flex align-items-center">
                                            @if ($detailedProduct->has_discount)
                                                <del class="product-price-old pr-3 pl-2 mt-1" id="del_unit_price">
                                                    <span class="" id="range_unit_price">
                                                        {{ single_price($detailedProduct->unit_price) }}
                                                    </span>
                                                    <span class="d-none" id="chonse_unit_price">

                                                    </span>
                                                </del>
                                                <div class="product-price font-weight-bold" id="lowest_highest_price">
                                                        {{ single_price($detailedProduct->final_price) }}
                                                </div>
                                                <div class="product-price d-none" id="variant_price">
                                                    <strong id="chosen_price">
                                                    </strong>
                                                </div>
                                                <div class="product-price-old pr-2 pl-3 mt-1">
                                                    <span class="">({{ $detailedProduct->discount_badge }})</span>
                                                </div>
                                            @else
                                                <div class="product-price" id="lowest_highest_price">
                                                    <strong>
                                                        {{ single_price($detailedProduct->unit_price) }}
                                                    </strong>
                                                </div>
                                                <div class="product-price d-none" id="variant_price">
                                                    <strong id="chosen_price">
                                                    </strong>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ($detailedProduct->tax)
                                <div class="row no-gutters mt-3">
                                    <div class="col-4">
                                        <div class="product-description-label mt-1">{{ __('Tax') }}:</div>
                                    </div>
                                    <div class="col-8">
                                        <div class="product-tax">
                                            <strong>
                                                {{ single_price($detailedProduct->tax_amount) }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if(count($coupons) > 0)
                                <div class="row no-gutters mt-3">
                                    <div class="col-4">
                                        <div class="product-description-label">{{ translate('Shop Vouchers')}}:</div>
                                    </div>
                                    <div class="col-8">
                                        <div>
                                            @foreach($coupons as $coupon)
                                                <span data-toggle="modal" data-target="#coupon-list" role="button" class="badge bg-orange">{{ __('coupon.title', ['offValue' => $coupon->discount_label]) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                @include('frontend.inc.coupon.popup', ['modalId' => 'coupon-list', 'canApply' => false])
                            @endif
                            @if(paymentSetting('octifi.enable'))
                                <div class="row no-gutters mt-3">
                                    <div class="col-12 d-flex align-items-center">
                                        <div class="">
                                            <span>{{ paymentSetting('octifi.productDetailText') }}</span>
                                        </div>
                                        <div class="ml-2">
                                            <a href="{{ paymentSetting('octifi.web') }}" target="_blank">
                                                <img loading="lazy" class="pull-left"
                                                    src="{{ static_asset('frontend/images/icons/cards/Latitude-Pay_Logo_Blue_RGB.png') }}"
                                                    height="30">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated && $detailedProduct->earn_point > 0)
                                <div class="row no-gutters mt-4">
                                    <div class="col-2">
                                        <div class="product-description-label">{{ __('Club Point') }}:</div>
                                    </div>
                                    <div class="col-10">
                                        <div class="d-inline-block club-point bg-soft-base-1 border-light-base-1 border">
                                            <span class="strong-700">{{ $detailedProduct->earn_point }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <form id="option-choice-form">
                                @csrf
                                <input type="hidden" name="id" value="{{ $detailedProduct->id }}">
                                <input type="hidden" name="is_age_restricted" value="{{ $isAgeRestricted }}">
                                <input type="hidden" name="is_confirm_age" value="{{ $isConfirmAge }}">
                                @if ($detailedProduct->choice_options != null)
                                    @foreach (json_decode($detailedProduct->choice_options) as $key => $choice)
                                        <div class="row no-gutters">
                                            <div class="col-2">
                                                <div class="product-description-label mt-2 ">
                                                    {{ \App\Attribute::find($choice->attribute_id)->name }}:</div>
                                            </div>
                                            <div class="col-10">
                                                <ul
                                                    class="list-inline checkbox-alphanumeric checkbox-alphanumeric--style-1 mb-2">
                                                    @foreach ($choice->values as $itemKey => $value)
                                                        <li>
                                                            <input type="radio" 
                                                                class="product-variants"
                                                                id="{{ $choice->attribute_id }}-{{ $value }}"
                                                                name="attribute_id_{{ $choice->attribute_id }}"
                                                                value="{{ $value }}"
                                                                data-offset-key="{{ $key }}">
                                                            <label
                                                                class="variant-label variant-{{ $choice->attribute_id }}" 
                                                                for="{{ $choice->attribute_id }}-{{ $value }}">
                                                                {{ $value }}
                                                            </label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                @if (count(json_decode($detailedProduct->colors)) > 0)
                                    <div class="row no-gutters">
                                        <div class="col-2">
                                            <div class="product-description-label mt-2">{{ __('Color') }}:</div>
                                        </div>
                                        <div class="col-10">
                                            <ul class="list-inline checkbox-color mb-1">
                                                @foreach (json_decode($detailedProduct->colors) as $key => $color)
                                                    <li>
                                                        <input type="radio"
                                                            id="{{ $detailedProduct->id }}-color-{{ $key }}"
                                                            name="color" value="{{ $color }}">
                                                        <label style="background: {{ $color }};"
                                                            for="{{ $detailedProduct->id }}-color-{{ $key }}"
                                                            data-toggle="tooltip"></label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <hr>
                                @endif
                                <!-- Quantity + Add to cart -->
                                <div class="row no-gutters">
                                    <div class="col-2">
                                        <div class="product-description-label mt-2">{{ __('Quantity') }}:</div>
                                    </div>
                                    <div class="col-10">
                                        <div class="product-quantity d-flex align-items-center">
                                            <div class="input-group input-group--style-2 pr-3" style="width: 160px;">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-number" type="button" data-type="minus"
                                                        data-field="quantity" disabled="disabled">
                                                        <i class="la la-minus"></i>
                                                    </button>
                                                </span>
                                                <input type="hidden" class="product-min_qty" name="min_qty"
                                                    value="{{ $detailedProduct->min_qty }}" />
                                                <input type="text" style="padding: 0px;" name="quantity"
                                                    class="form-control h-auto input-number text-center" placeholder="1"
                                                    value="1" min="1" max="{{ $detailedProduct->quantity }}">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-number" type="button" data-type="plus"
                                                        data-field="quantity">
                                                        <i class="la la-plus"></i>
                                                    </button>
                                                </span>
                                            </div>
                                            <!--removed as per justin comment, 5nov2020-->
                                            <!--<div class="avialable-amount">(<span id="available-quantity">{{ $detailedProduct->quantity }}</span> {{ __('available') }})</div>-->
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </form>
                            <div class="d-table width-100 mt-3">
                                <div class="d-table-cell">
                                    @if (\App\Seller::where('user_id', $detailedProduct->user_id)->first()->verification_status == 1)
                                        <!-- Buy Now button -->
                                        @if ($detailedProduct->quantity > 0)
                                            <button type="button"
                                                class="btn btn-styled btn-base-1 btn-icon-left strong-700 hov-bounce hov-shaddow buy-now"
                                                onclick="buyNow()">
                                                <i class="la la-shopping-cart"></i> {{ __('Buy Now') }}
                                            </button>
                                            <button type="button"
                                                class="btn btn-styled btn-alt-base-1 c-white btn-icon-left strong-700 hov-bounce hov-shaddow ml-2 add-to-cart"
                                                onclick="addToCart()">
                                                <i class="la la-shopping-cart"></i>
                                                <span class=""> {{ __('Add to cart') }}</span>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-styled btn-icon-left bg-gray strong-700"
                                                disabled>
                                                <i class="la la-cart-arrow-down"></i> {{ __('Out of Stock') }}
                                            </button>
                                        @endif
                                    @else
                                        <button type="button" class="btn btn-styled btn-icon-left bg-gray strong-700" disabled>
                                            <i class="la la-cart-arrow-down"></i> {{ __('product Unavailable') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="d-table width-100 mt-3">
                                <div class="d-table-cell">
                                    <!-- Add to wishlist button -->
                                    <button type="button" class="btn pl-0 btn-link strong-700"
                                        onclick="addToWishList({{ $detailedProduct->id }})">
                                        {{ __('Add to wishlist') }}
                                    </button>
                                    <!-- Add to compare button -->
                                    <button type="button" class="btn btn-link btn-icon-left strong-700"
                                        onclick="addToCompare({{ $detailedProduct->id }})">
                                        {{ __('Add to compare') }}
                                    </button>
                                    @if (Auth::check() && \App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated && (\App\AffiliateOption::where('type', 'product_sharing')->first()->status || \App\AffiliateOption::where('type', 'category_wise_affiliate')->first()->status) && Auth::user()->affiliate_user != null && Auth::user()->affiliate_user->status)
                                        @php
                                            if (Auth::check()) {
                                                if (Auth::user()->referral_code == null) {
                                                    Auth::user()->referral_code = substr(Auth::user()->id . Str::random(10), 0, 10);
                                                    Auth::user()->save();
                                                }
                                                $referral_code = Auth::user()->referral_code;
                                                $referral_code_url = URL::to('/product') . '/' . $detailedProduct->slug . "?product_referral_code=$referral_code";
                                            }
                                        @endphp
                                        <div class="form-group">
                                            <textarea id="referral_code_url" class="form-control" readonly type="text"
                                                style="display:none">{{ $referral_code_url }}</textarea>
                                        </div>
                                        <button type=button id="ref-cpurl-btn" class="btn btn-sm btn-secondary"
                                            data-attrcpy="{{ __('Copied') }}"
                                            onclick="CopyToClipboard('referral_code_url')">{{ __('Copy the Promote Link') }}</button>
                                    @endif
                                </div>
                            </div>
                            <hr class="mt-2">
                            <div class="row no-gutters mt-3">
                                <div class="col-5">
                                    @if ($detailedProduct->getIsFreeLocalDeliverys())
                                        <div
                                            style="font-weight: 600;background: red; color: white; padding: 5px; width: 60%; text-align: center; border-radius: 20px;">
                                            Free Local Delivery </div>
                                    @endif
                                </div>
                            </div>
                            @if ($shop->user->shareable == 1)
                                <div class="row no-gutters mt-4">
                                    <div class="col-2">
                                        <div class="product-description-label mt-2">{{ __('Share') }}:</div>
                                    </div>
                                    <div class="col-10">


                                        <div id="share"></div>

                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="gry-bg">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 d-none d-xl-block">
                    <div class="seller-info-box mb-3">
                        <div class="sold-by position-relative">
                            @if ($detailedProduct->added_by == 'seller' && array_key_exists('vendor_system_activation', $businessSettings) && $businessSettings['vendor_system_activation'] == 1 && $detailedProduct->user->seller->verification_status == 1)
                                <div class="position-absolute medal-badge">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve"
                                        viewBox="0 0 287.5 442.2">
                                        <polygon style="fill:#F8B517;"
                                            points="223.4,442.2 143.8,376.7 64.1,442.2 64.1,215.3 223.4,215.3 " />
                                        <circle style="fill:#FBD303;" cx="143.8" cy="143.8" r="143.8" />
                                        <circle style="fill:#F8B517;" cx="143.8" cy="143.8" r="93.6" />
                                        <polygon style="fill:#FCFCFD;" points="143.8,55.9 163.4,116.6 227.5,116.6 175.6,154.3 195.6,215.3 143.8,177.7 91.9,215.3 111.9,154.3
                                    60,116.6 124.1,116.6 " />
                                    </svg>
                                </div>
                            @endif
                            <div class="title">{{ __('Sold By') }}</div>
                            @if ($detailedProduct->added_by == 'seller' && array_key_exists('vendor_system_activation', $businessSettings) && $businessSettings['vendor_system_activation'] == 1)
                                <a href="{{ getShopLink($detailedProduct->user->shop->slug) }}"
                                    class="name d-block">{{ $detailedProduct->user->shop->name }}
                                    @if ($detailedProduct->user->seller->verification_status == 1)
                                        <span class="ml-2"><i class="fa fa-check-circle"
                                                style="color:green"></i></span>
                                    @else
                                        <span class="ml-2"><i class="fa fa-times-circle"
                                                style="color:red"></i></span>
                                    @endif
                                </a>
                                <div class="location">{{ $detailedProduct->user->shop->address }}</div>
                            @else
                                {{ env('APP_NAME') }}
                            @endif
                            @php
                                $total = $detailedProduct->user->product_reviews_count;
                                $rating = $detailedProduct->user->sum_review_rating;
                            @endphp
                            <div class="rating text-center d-block">
                                <span class="star-rating star-rating-sm d-block">
                                    @if ($total > 0)
                                        {{ renderStarRating($rating / $total) }}
                                    @else
                                        {{ renderStarRating(0) }}
                                    @endif
                                </span>
                                <span class="rating-count d-block ml-0">({{ $total }}
                                    {{ __('customer reviews') }})</span>
                            </div>
                        </div>
                        <div class="row no-gutters align-items-center">
                            @if ($detailedProduct->added_by == 'seller')
                                <div class="col">
                                    <a href="{{ getShopLink($detailedProduct->user->shop->slug) }}"
                                        class="d-block store-btn">{{ __('Visit Store') }}</a>
                                </div>
                                <div class="col">
                                    <ul class="social-media social-media--style-1-v4 text-center">
                                        @if ($detailedProduct->user->shop->facebook != '')
                                            <li>
                                                <a href="{{ $detailedProduct->user->shop->facebook }}"
                                                    class="facebook" target="_blank" data-toggle="tooltip"
                                                    data-original-title="Facebook">
                                                    <i class="fa fa-facebook"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($detailedProduct->user->shop->google != '')
                                            <li>
                                                <a href="{{ $detailedProduct->user->shop->google }}"
                                                    class="google" target="_blank" data-toggle="tooltip"
                                                    data-original-title="Google">
                                                    <i class="fa fa-google"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($detailedProduct->user->shop->twitter != '')
                                            <li>
                                                <a href="{{ $detailedProduct->user->shop->twitter }}"
                                                    class="twitter" target="_blank" data-toggle="tooltip"
                                                    data-original-title="Twitter">
                                                    <i class="fa fa-twitter"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($detailedProduct->user->shop->youtube != '')
                                            <li>
                                                <a href="{{ $detailedProduct->user->shop->youtube }}"
                                                    class="youtube" target="_blank" data-toggle="tooltip"
                                                    data-original-title="Youtube">
                                                    <i class="fa fa-youtube"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($detailedProduct->user->shop->instagram != '')
                                            <li>
                                                <a href="{{ $detailedProduct->user->shop->instagram }}"
                                                    class="instagram" target="_blank" data-toggle="tooltip"
                                                    data-original-title="instagram">
                                                    <i class="fa fa-instagram"></i>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="seller-top-products-box bg-white sidebar-box mb-3">
                        <div class="box-title">
                            {{ __('Top Selling Products From This Seller') }}
                        </div>
                        <div class="box-content">
                            @foreach ($topSellingProducts as $key => $top_product)
                                <div class="mb-3 product-box-3">
                                    <div class="clearfix">
                                        <div class="product-image float-left">
                                            @include('frontend.inc.product_list.image_box', ['product' => $top_product])
                                        </div>
                                        <div class="product-details float-left">
                                            <h4 class="title text-truncate">
                                                <a href="{{ route('product', $top_product->slug) }}"
                                                    class="d-block">{{ $top_product->translation_name }}</a>
                                            </h4>
                                            <div class="star-rating star-rating-sm mt-1">
                                                {{ renderStarRating($top_product->rating) }}
                                            </div>
                                            @include('frontend.inc.product_list.price_box', ['product' => $top_product])
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div class="product-desc-tab bg-white">
                        <div class="tabs tabs--style-2">
                            <ul class="nav nav-tabs justify-content-center sticky-top bg-white">
                                <li class="nav-item">
                                    <a href="#tab_default_1" data-toggle="tab"
                                        class="nav-link text-uppercase strong-600 active show">{{ __('Description') }}</a>
                                </li>
                                @if ($detailedProduct->video_link != null)
                                    <li class="nav-item">
                                        <a href="#tab_default_2" data-toggle="tab"
                                            class="nav-link text-uppercase strong-600">{{ __('Video') }}</a>
                                    </li>
                                @endif
                                @if ($detailedProduct->pdf != null)
                                    <li class="nav-item">
                                        <a href="#tab_default_3" data-toggle="tab"
                                            class="nav-link text-uppercase strong-600">{{ __('Downloads') }}</a>
                                    </li>
                                @endif
                                <li class="nav-item">
                                    <a href="#tab_default_4" data-toggle="tab"
                                        class="nav-link text-uppercase strong-600">{{ __('Reviews') }}</a>
                                </li>
                            </ul>
                            <div class="tab-content pt-0">
                                <div class="tab-pane active show" id="tab_default_1">
                                    <div class="py-2 px-4">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mw-100 overflow--hidden aiz-product-description">
                                                    <?php
                                                    $description = $detailedProduct->translation_description;
                                                    $description = str_replace('', '', $description);
                                                    //$description =  strip_tags($description);
                                                    print_r($description);
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_default_2">
                                    <div class="fluid-paragraph py-2">
                                        <!-- 16:9 aspect ratio -->
                                        <div class="embed-responsive embed-responsive-16by9 mb-5">
                                            @if ($detailedProduct->video_provider == 'youtube' && $detailedProduct->video_link != null)
                                                <iframe class="embed-responsive-item"
                                                    src="https://www.youtube.com/embed/{{ explode('=', $detailedProduct->video_link)[1] }}"></iframe>
                                            @elseif ($detailedProduct->video_provider == 'dailymotion' && $detailedProduct->video_link != null)
                                                <iframe class="embed-responsive-item"
                                                    src="https://www.dailymotion.com/embed/video/{{ explode('video/', $detailedProduct->video_link)[1] }}"></iframe>
                                            @elseif ($detailedProduct->video_provider == 'vimeo' && $detailedProduct->video_link != null)
                                                <iframe
                                                    src="https://player.vimeo.com/video/{{ explode('vimeo.com/', $detailedProduct->video_link)[1] }}"
                                                    width="500" height="281" frameborder="0" webkitallowfullscreen
                                                    mozallowfullscreen allowfullscreen></iframe>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_default_3">
                                    <div class="py-2 px-4">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <a
                                                    href="{{ my_asset($detailedProduct->pdf) }}">{{ __('Download') }}</a>
                                            </div>
                                        </div>
                                        <span class="space-md-md"></span>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_default_4">
                                    <div class="fluid-paragraph py-4">
                                        @foreach ($detailedProduct->reviews as $key => $review)
                                            <div class="block block-comment">
                                                <div class="block-image">
                                                    <img src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                        @if ($review->user->avatar_original != null) data-src="{{ my_asset($review->user->avatar_original) }}"
                                            @else
                                            data-src="{{ my_asset('img/avatar-place.png') }}" @endif
                                                        class="rounded-circle lazyload">
                                                </div>
                                                <div class="block-body">
                                                    <div class="block-body-inner">
                                                        <div class="row no-gutters">
                                                            <div class="col">
                                                                <h3 class="heading heading-6">
                                                                    <a href="javascript:;">{{ $review->user->name }}</a>
                                                                </h3>
                                                                <div class="d-flex align-items-center flex-wrap">
                                                                    <span class="comment-date mr-2">
                                                                        {{ date('d-m-Y', strtotime($review->created_at)) }}
                                                                    </span>
                                                                    @if($review->reviewCategories && $review->reviewCategories->count() > 0)
                                                                        <div class="review-category-badges">
                                                                            @foreach($review->reviewCategories as $reviewCat)
                                                                                <span class="review-category-badge">{{ $reviewCat->title }}</span>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <div class="rating text-right clearfix d-block">
                                                                    <span class="star-rating star-rating-sm float-right">
                                                                        @for ($i = 0; $i < $review->rating; $i++)
                                                                            <i class="fa fa-star active"></i>
                                                                        @endfor
                                                                        @for ($i = 0; $i < 5 - $review->rating; $i++)
                                                                            <i class="fa fa-star"></i>
                                                                        @endfor
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="comment-text">
                                                            {{ $review->comment }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if (count($detailedProduct->reviews) <= 0)
                                            <div class="text-center">
                                                {{ __('There have been no reviews for this product yet.') }}
                                                <br>
                                                @if (!Auth::check())
                                                    {{ __('Please login to your account to review the products.') }}
                                                    <br> <button class="btn btn-styled btn-base-1"
                                                        onclick="showCheckoutModal()">{{ __('Login to Review') }}</button>
                                                @endif
                                            </div>
                                        @endif
                                        @if (Auth::check())
                                            @php
                                                $commentable = false;
                                            @endphp
                                            @foreach ($detailedProduct->orderDetails as $key => $orderDetail)
                                                @if ($orderDetail->order != null &&
                                                    $orderDetail->order->user_id == Auth::user()->id &&
                                                    $orderDetail->delivery_status == 'delivered' &&
                                                    \App\Review::where('user_id', Auth::user()->id)->where('product_id', $detailedProduct->id)->first() == null)
                                                    @php
                                                        $commentable = true;
                                                    @endphp
                                                @endif
                                            @endforeach
                                            @if ($commentable)
                                                <div class="leave-review">
                                                    <div class="section-title section-title--style-1">
                                                        <h3
                                                            class="section-title-inner heading-6 strong-600 text-uppercase">
                                                            {{ __('Write a review') }}
                                                        </h3>
                                                    </div>
                                                    <form class="form-default" role="form"
                                                        action="{{ route('reviews.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_id"
                                                            value="{{ $detailedProduct->id }}">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for=""
                                                                        class="text-uppercase c-gray-light">{{ __('Your name') }}</label>
                                                                    <input type="text" name="name"
                                                                        value="{{ Auth::user()->name }}"
                                                                        class="form-control" disabled required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for=""
                                                                        class="text-uppercase c-gray-light">{{ __('Email') }}</label>
                                                                    <input type="text" name="email"
                                                                        value="{{ Auth::user()->email }}"
                                                                        class="form-control" required disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="c-rating mt-1 mb-1 clearfix d-inline-block">
                                                                    <input type="radio" id="star5" name="rating" value="5"
                                                                        checked required />
                                                                    <label class="star" for="star5"
                                                                        title="Awesome" aria-hidden="true"></label>
                                                                    <input type="radio" id="star4" name="rating" value="4"
                                                                        required />
                                                                    <label class="star" for="star4" title="Great"
                                                                        aria-hidden="true"></label>
                                                                    <input type="radio" id="star3" name="rating" value="3"
                                                                        required />
                                                                    <label class="star" for="star3"
                                                                        title="Very good" aria-hidden="true"></label>
                                                                    <input type="radio" id="star2" name="rating" value="2"
                                                                        required />
                                                                    <label class="star" for="star2" title="Good"
                                                                        aria-hidden="true"></label>
                                                                    <input type="radio" id="star1" name="rating" value="1"
                                                                        required />
                                                                    <label class="star" for="star1" title="Bad"
                                                                        aria-hidden="true"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-sm-12">
                                                                <textarea class="form-control" rows="4" name="comment"
                                                                    placeholder="{{ __('Your review') }}"
                                                                    required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-sm-12">
                                                                <label style="cursor: pointer; user-select: none;">
                                                                    <input type="checkbox" name="is_recommended" value="1" style="margin-right: 8px; cursor: pointer;">
                                                                    <span>{{ __('I recommend this product') }}</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @if(isset($reviewCategories) && $reviewCategories->count() > 0)
                                                        <div class="row mt-3">
                                                            <div class="col-sm-12">
                                                                <label class="d-block mb-2"><strong>{{ __('Review Categories') }}</strong></label>
                                                                <div class="review-category-tags">
                                                                    @foreach($reviewCategories as $reviewCategory)
                                                                        <label class="review-category-tag" data-category-id="{{ $reviewCategory->id }}">
                                                                            <input type="checkbox" name="review_categories[]" value="{{ $reviewCategory->id }}" style="display: none;">
                                                                            <span class="tag-text">{{ $reviewCategory->title }}</span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        <div class="text-right">
                                                            <button type="submit"
                                                                class="btn btn-styled btn-base-1 btn-circle mt-4">
                                                                {{ __('Send review') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="my-4 bg-white p-3">
                        <div class="section-title-1">
                            <h3 class="heading-5 strong-700 mb-0">
                                <span class="mr-4">{{ __('Related products') }}</span>
                            </h3>
                        </div>
                        <div class="caorusel-box arrow-round gutters-5">
                            <div class="slick-carousel" data-slick-items="3" data-slick-xl-items="2"
                                data-slick-lg-items="3" data-slick-md-items="2" data-slick-sm-items="1"
                                data-slick-xs-items="1" data-slick-rows="2">
                                @foreach ($relatedProducts as $key => $related_product)
                                    <div class="caorusel-card my-1">
                                        <div class="row no-gutters product-box-2 align-items-center">
                                            <div class="col-5">
                                                <div class="position-relative overflow-hidden">
                                                    @include('frontend.inc.product_list.image_box', ['product' => $related_product])
                                                </div>
                                            </div>
                                            <div class="col-7 border-left">
                                                <div class="p-3">
                                                    <h2 class="product-title mb-0 p-0 text-truncate">
                                                        <a
                                                            href="{{ route('product', $related_product->slug) }}">{{ __($related_product->translation_name) }}
                                                        </a>
                                                    </h2>
                                                    <div class="star-rating star-rating-sm mb-2">
                                                        {{ renderStarRating($related_product->rating) }}
                                                    </div>
                                                    <div class="clearfix">
                                                        <div class="float-left">
                                                            @include('frontend.inc.product_list.price_box', ['product' => $related_product])
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title strong-600 heading-5">{{ __('Any query about this product') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="" action="{{ route('conversations.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detailedProduct->id }}">
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="form-group">
                            <input type="text" class="form-control mb-3" name="title"
                                value="{{ $detailedProduct->translation_name }}" placeholder="{{ __('Product Name') }}"
                                required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="8" name="message" required
                                placeholder="{{ __('Your Question') }}">{{ route('product', $detailedProduct->slug) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-base-1 btn-styled">{{ __('Send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="login_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ __('Login') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <form class="form-default" role="form" action="{{ route('cart.login.submit') }}"
                            method="POST">
                            @csrf
                            @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                <span>{{ __('Use country code before number') }}</span>
                            @endif
                            <div class="form-group">
                                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                    <input type="text"
                                        class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                        value="{{ old('email') }}" placeholder="{{ __('Email Or Phone') }}"
                                        name="email" id="email">
                                @else
                                    <input type="email"
                                        class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                        value="{{ old('email') }}" placeholder="{{ __('Email') }}" name="email">
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control form-control-lg h-auto"
                                    placeholder="{{ __('Password') }}">
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <a href="#" class="link link-xs link--style-3">{{ __('Forgot password?') }}</a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit"
                                        class="btn btn-styled btn-base-1 px-4">{{ __('Sign in') }}</button>
                                </div>
                            </div>
                        </form>
                        <div class="text-center pt-3">
                            <p class="text-md">
                                {{ __('Need an account?') }}
                                <a href="{{ route('user.registration') }}" class="strong-600">
                                    {{ __('Register Now') }}</a>
                            </p>
                        </div>
                        @if ((array_key_exists('google_login', $businessSettings) && $businessSettings['google_login'] == 1) || (array_key_exists('facebook_login', $businessSettings) && $businessSettings['facebook_login'] == 1) || (array_key_exists('twitter_login', $businessSettings) && $businessSettings['twitter_login'] == 1))
                            <div class="or or--1 my-3 text-center">
                                <span>{{ __('or') }}</span>
                            </div>
                            @if (array_key_exists('google_login', $businessSettings) && $businessSettings['google_login'] == 1)
                                <a href="{{ route('social.login', ['provider' => 'google']) }}"
                                    class="btn btn-styled btn-block btn-google btn-icon--2 btn-icon-left px-4 mb-3">
                                    <i class="icon fa fa-google"></i> {{ __('Login with Google') }}
                                </a>
                            @endif
                            @if (array_key_exists('facebook_login', $businessSettings) && $businessSettings['facebook_login'] == 1)
                                <a href="{{ route('social.login', ['provider' => 'facebook']) }}"
                                    class="btn btn-styled btn-block btn-facebook btn-icon--2 btn-icon-left px-4 mb-3">
                                    <i class="icon fa fa-facebook"></i> {{ __('Login with Facebook') }}
                                </a>
                            @endif
                            @if (array_key_exists('twitter_login', $businessSettings) && $businessSettings['twitter_login'] == 1)
                                <a href="{{ route('social.login', ['provider' => 'twitter']) }}"
                                    class="btn btn-styled btn-block btn-twitter btn-icon--2 btn-icon-left px-4 mb-3">
                                    <i class="icon fa fa-twitter"></i> {{ __('Login with Twitter') }}
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('frontend.inc.restricted_age.popup')

@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            @if (session('is_confirm_age'))
                sessionStorage.setItem('isConfirmedRestrictedAge', {{ session('is_confirm_age') }});
            @endif

            $('#share').jsSocials({
                text: "Hey! Check out this Fabulous Product",
                showLabel: false,
                showCount: false,
                shares: ["email", "twitter", "facebook", "linkedin", "pinterest", "whatsapp"]
            });

            $('#option-choice-form input').on('change', function () {
                let currentVariant = [];
                $('#option-choice-form input.product-variants').each(function () {
                    if(this.checked) {
                        currentVariant.push(this.value);
                    }
                });
                currentVariant = currentVariant.join('{{ \App\Models\ProductStock::SPLIT_SYMBOL }}');
                $('.product-gal-thumb a').each(function () {
                    if ($(this).data('variant') == currentVariant) {
                        $(this).trigger('click');
                    }
                });
            });

            const $initThumb = $('.xzoom-thumbs a[preview="1"]');
            if ($initThumb.length) {
                setTimeout(function () {
                    $initThumb.trigger('click');
                }, 0);
            }

            const productId  = @json($detailedProduct->id);
            const attributes = @json($attributes);

            if (attributes.length === 1) {
                checkStock({ productId, attributes });
            }

            $(document).on('change', '.product-variants', function () {
                let params = { productId, attributes };

                if (attributes.length === 1) {
                    checkStock(params);
                    return;
                }

                const selections = getSelections();
                const variantMapping = buildVariants(selections);

                checkStock({
                    ...params,
                    variantMapping,
                    selections
                });
            });

            function getSelections() {
                let selections = {};
                $(".product-variants:checked").each(function () {
                    const attrId = $(this).attr('name').replace('attribute_id_', '');
                    selections[attrId] = $(this).val();
                });
                return selections;
            }
        });

        function CopyToClipboard(containerid) {
            if (document.selection) {
                var range = document.body.createTextRange();
                range.moveToElementText(document.getElementById(containerid));
                range.select().createTextRange();
                document.execCommand("Copy");
            } else if (window.getSelection) {
                var range = document.createRange();
                document.getElementById(containerid).style.display = "block";
                range.selectNode(document.getElementById(containerid));
                window.getSelection().addRange(range);
                document.execCommand("Copy");
                document.getElementById(containerid).style.display = "none";
            }
            showFrontendAlert('success', 'Copied');
        }

        function show_chat_modal() {
            @if (Auth::check())
                $('#chat_modal').modal('show');
            @else
                $('#login_modal').modal('show');
            @endif
        }

        // Javascript to enable link to tab
        var url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        }

        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function(e) {
            window.location.hash = e.target.hash;
        })

        function showCheckoutModal() {
            $('#login_modal').modal('show');
        }
        
        $(document).ready(function() {
            $('.review-category-tag').on('click', function(e) {
                e.preventDefault();
                $(this).toggleClass('selected');
                var checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop('checked', !checkbox.prop('checked'));
            });
        });
    </script>
@endsection

@section('css')
<style>
    .swal-text {
        text-align: center !important;
        font-size: 15px !important;
    }

    .xzoom,
    .xzoom-lens,
    .xzoom-preview {
        max-height: 550px;
        margin: auto;
        left: 0;
        right: 0;
        display: block;

    }

    .hidden-image {
        display: none !important;
    }
    
    .review-category-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .review-category-tag {
        display: inline-block;
        padding: 8px 16px;
        border: 2px solid #ddd;
        border-radius: 20px;
        background-color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
        margin: 0;
    }
    
    .review-category-tag:hover {
        border-color: #999;
        background-color: #f5f5f5;
    }
    
    .review-category-tag input[type="checkbox"]:checked + .tag-text {
        font-weight: 600;
    }
    
    .review-category-tag.selected {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }
    
    .review-category-tag.selected:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
    
    .review-category-tag .tag-text {
        display: block;
    }
    
    .review-category-badges {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    
    .review-category-badge {
        display: inline-block;
        padding: 3px 10px;
        background-color: #e3f2fd;
        color: #1976d2;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
    }

</style>
@endsection