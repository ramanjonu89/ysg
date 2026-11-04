@if($products = $block->getProducts())
<div class="block shadow-sm block-product-carousel" id="block-{{$block->id}}">
    <div class="caorusel-box arrow-round gutters-5">
        <div class="slick-carousel" data-slick-items="6" data-slick-xl-items="5" data-slick-lg-items="4"
             data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2">
            @foreach ($products as $key => $product)
                <div class="caorusel-card">
                    <div class="product-box-2 bg-white alt-box my-2">
                        <div class="image-box position-relative overflow-hidden">
                            @if($product->has_discount)
                                <div class="notify-badge">{{$product->discount_badge}}</div>
                            @endif
                            @if ($product->isProductForAdults())
                                <a href="{{ route('product', $product->slug) }}" class="d-block product-image h-100 text-center">
                                        <img class="img-fit lazyload" 
                                            src="{{ my_asset('assets/frontend/images/adult_notice.png') }}" 
                                            alt="{{ __($product->translation_name) }}"
                                        >
                                </a>
                            @else
                                <a href="{{ route('product', $product->slug) }}" class="d-block product-image h-100 text-center">
                                    <img class="img-fit lazyload"
                                        src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                        data-src="{{ my_asset($product->thumbnail_img) }}"
                                        alt="{{ __($product->translation_name) }}">
                                </a>
                            @endif
                            <div class="product-btns clearfix">
                                <button class="btn add-wishlist" title="Add to Wishlist"
                                        onclick="addToWishList({{ $product->id }})" tabindex="0">
                                    <i class="la la-heart-o"></i>
                                </button>
                                <button class="btn add-compare" title="Add to Compare"
                                        onclick="addToCompare({{ $product->id }})" tabindex="0">
                                    <i class="la la-refresh"></i>
                                </button>
                                <button class="btn quick-view" title="Quick view"
                                        onclick="showNoticeAgeRestrictedProduct({{ json_encode(['id' => $product->id, 'age' => $product->category->is_age_restricted]) }})" tabindex="0">
                                    <i class="la la-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-md-3 p-2">
                            @include('frontend.inc.product_list.price_box', ['product' => $product])
                            <div class="star-rating star-rating-sm mt-1">
                                {{ renderStarRating($product->rating) }}
                            </div>
                            <h2 class="product-title p-0">
                                @if ($brand = $product->brand)
                                <a href="{{ route('product', $product->slug) }}" class="text-truncate">
                                    {{ $brand->name }}
                                </a>
                                @endif
                            </h2>
                            <div class="card-jfy-title"><a href="{{ route('product', $product->slug) }}">{{ __($product->translation_name) }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@php
    $bgPadding = $block->options->bg_padding ?? "0,0,0,0";
    list($paddingTop, $paddingLeft, $paddingRight, $paddingBottom) = explode(",", $bgPadding);
@endphp

<style>
    #block-{{$block->id}}  {
        padding: calc(0.5rem + {{$paddingTop}}px) calc(1rem + {{$paddingRight}}px) calc(0.5rem + {{$paddingBottom}}px) calc(1rem + {{$paddingLeft}}px);
    }

    @media screen and (min-width: 1200px) {
        #block-{{$block->id}}  {
            background-image: url("{{$block->options->pc_bg ?? ''}}");
        }
    }

    @media screen and (min-width: 766px) and (max-width: 1199px) {
        #block-{{$block->id}}  {
            background-image: url("{{$block->options->tablet_bg ?? ''}}");
        }
    }

    @media screen and (max-width: 765px) {
        #block-{{$block->id}}  {
            padding-right: calc(0.5rem + {{$paddingTop}}px) !important;
            padding-left: calc(0.5rem + {{$paddingTop}}px) !important;
            background-image: url("{{$block->options->mobile_bg ?? ''}}");
        }
    }

</style>
@endif



