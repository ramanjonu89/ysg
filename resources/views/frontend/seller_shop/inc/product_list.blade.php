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
                <button type="button" class="btn btn-block btn-icon-left" onclick="showNoticeAgeRestrictedProduct({{ json_encode(['id' => $product->id, 'age' => $product->category->is_age_restricted, 'shop' => $shop->user->id]) }})">
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
