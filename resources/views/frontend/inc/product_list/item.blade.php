<div class="col-xxl-3 col-xl-4 col-lg-3 col-md-4 col-6">
    <div class="product-box-2 bg-white alt-box my-md-2 btn-add-cart-item">
        <div class="position-relative overflow-hidden">
            @if($product->isProductForAdults())
                <a href="{{ route('product', $product->slug) }}" class="d-block product-image text-center" tabindex="0">
                    @if ($product->has_discount)
                        <div class="notify-badge">{{ $product->discount_badge }}</div>
                    @endif
                    <img class="img-fit lazyload"
                        src="{{ my_asset('assets/frontend/images/adult_notice.png') }}"
                        alt="{{ __($product->translation_name) }}">
                </a>
            @else
                <a href="{{ route('product', $product->slug) }}"
                    class="d-block product-image text-center">
                    @if($product->has_discount)
                        <div class="notify-badge">{{ $product->discount_badge }}</div>
                    @endif
                    <img class="img-fit lazyload"
                        src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                        data-src="{{ my_asset($product->thumbnail_img) }}"
                        alt="{{ __($product->translation_name) }}">
                </a>
            @endif
            <div class="product-btns clearfix">
                <button class="btn add-wishlist" title="Add to Wishlist"
                        onclick="addToWishList({{ $product->id }})" type="button">
                    <i class="la la-heart-o"></i>
                </button>
                <button class="btn add-compare" title="Add to Compare"
                        onclick="addToCompare({{ $product->id }})" type="button">
                    <i class="la la-refresh"></i>
                </button>
                <button class="btn quick-view" title="Quick view"
                        type="button"
                        onclick="showNoticeAgeRestrictedProduct({{ json_encode(['id' => $product->id, 'age' => $product->getAgeRestricted()]) }})">
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
                <a href="{{ route('product', $product->slug) }}" class="text-truncate">
                    @foreach ($brands as $brand)
                        @if ($brand->id == $product->brand_id)
                            {{ $brand->name }}
                            @break
                        @endif
                    @endforeach
                </a>
            </h2>
            <div class="card-jfy-title"><a href="{{ route('product', $product->slug) }}"
                                           style="color:#000">{{ __($product->translation_name) }}</a></div>
            @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null &&
                \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                <div class="club-point mt-2 bg-soft-base-1 border-light-base-1 border">
                    {{ __('Club Point') }}:
                    <span
                        class="strong-700 float-right">{{ $product->earn_point }}</span>
                </div>
            @endif
        </div>
        @include('frontend.inc.product_list.add_to_cart_box', ['product' => $product])
    </div>
</div>
