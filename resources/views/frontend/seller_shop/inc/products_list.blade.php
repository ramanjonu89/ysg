<section class="@if (!$type) gry-bg @endif py-4">
    @if (!empty($products->items()))
        <div class="container">
            <form class="" id="search-form" action="" method="GET">
                <input type="hidden" name="q" @isset($keyword) value="{{ $keyword }}" @endisset />
                <input type="hidden" name="sort_by"
                    @isset($sort_by) value="{{ $sort_by }}" @endisset/>
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
                                    @include('frontend.product_filter.category_filter')
                                    <!-- END -->

                                    <!-- Brand Filter -->
                                    @include('frontend.product_filter.brand_filter')
                                    <!-- END -->

                                    <!-- Price Filter -->
                                    @include('frontend.seller_shop.inc.price_filter')
                                    <!-- END -->

                                    <!-- Color Filter -->
                                    @include('frontend.product_filter.color_filter')
                                    <!-- END -->
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="@if($is_include_filter) col-xl-9 @else col-xl-12 @endif">
                        @if($is_include_filter)
                            @include('frontend.seller_shop.inc.sort_by_bar_for_shop')
                        @endif
                        <div class="products-box-bar p-3 bg-white">
                            <div class="row sm-no-gutters gutters-5">
                                <input type="hidden" class="is-shop-adult" value="{{ $isShopAdult }}">
                                @foreach ($products as $key => $product)
                                    <div class="col-xxl-3 col-xl-4 col-lg-3 col-md-4 col-6">
                                        <div class="card product-box-1 mb-3">
                                            <div class="card-image">
                                                @if($product->has_discount)
                                                    <div class="notify-badge">{{ $product->discount_badge }}</div>
                                                @endif
                                                <a href="{{ route('product', $product->slug) }}"
                                                    class="d-block text-center">
                                                    <img class="img-fit lazyload normal-image"
                                                        src="{{ my_asset($product->getThumbnailImg()) }}"
                                                        alt="{{  __($product->translation_name) }}">
                                                </a>
                                                <input type="hidden" id="age-restricted" name="age-restricted-{{ $key }}" value="{{ $product->getAgeRestricted() }}">
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="px-3 py-2">
                                                    @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                                        <div
                                                            class="club-point mb-2 bg-soft-base-1 border-light-base-1 border">
                                                            {{ __('Club Point') }}:
                                                            <span
                                                                class="strong-700 float-right">{{ $product->earn_point }}</span>
                                                        </div>
                                                    @endif
                                                    <h2 class="product-title p-0">
                                                        <a href="{{ route('product', $product->slug) }}"
                                                        class="text-truncate-1">
                                                            @foreach($brands as $brand)
                                                                @if($brand->id == $product->brand_id)
                                                                    {{ $brand->name }}
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                        </a>
                                                    </h2>
                                                    <div class="card-jfy-title"><a
                                                            href="{{ route('product', $product->slug) }}"
                                                            style="color:#000">{{ __($product->translation_name) }}</a></div>
                                                </div>
                                                <div class="price-bar row no-gutters">
                                                    <div class="price col-md-8">
                                                        @include('frontend.inc.product_list.price_box', ['product' => $product])
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="star-rating star-rating-sm float-md-right">
                                                            {{ renderStarRating($product->rating) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="cart-add d-flex">
                                                    <button class="btn add-wishlist border-right" title="Add to Wishlist"
                                                            onclick="addToWishList({{ $product->id }})">
                                                        <i class="la la-heart-o"></i>
                                                    </button>
                                                    <button class="btn add-compare border-right" title="Add to Compare"
                                                            onclick="addToCompare({{ $product->id }})">
                                                        <i class="la la-refresh"></i>
                                                    </button>
                                                    @if ($product->quantity)
                                                        <button type="button" class="btn btn-block btn-icon-left"
                                                                onclick="showNoticeAgeRestrictedProduct({{ json_encode(['id' => $product->id, 'age' => $product->category->is_age_restricted]) }})">
                                                            <span
                                                                class="d-none d-sm-inline-block">{{ __('Add to cart') }}</span><i
                                                                class="la la-shopping-cart ml-2"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-block btn-icon-left bg-red"
                                                                disabled="">
                                                            <span
                                                                class="d-none d-sm-inline-block">{{ __('Out of stock') }}</span><i
                                                                class="la la-shopping-cart ml-2"></i>
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
                            <div class="d-none d-md-block">
                                <nav aria-label="Center aligned pagination">
                                    <ul class="pagination justify-content-center">
                                        {{ $products->links() }}
                                    </ul>
                                </nav>
                            </div>

                            <div class="d-flex d-md-none justify-content-between">
                                @if ($products->onFirstPage())
                                    <span class="btn btn-outline-secondary disabled">Prev</span>
                                @else
                                    <a href="{{ $products->previousPageUrl() }}" class="btn btn-outline-primary">
                                        Prev
                                    </a>
                                @endif

                                @if ($products->hasMorePages())
                                    <a href="{{ $products->nextPageUrl() }}" class="btn btn-outline-primary">
                                        Next
                                    </a>
                                @else
                                    <span class="btn btn-outline-secondary disabled">Next</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="d-flex flex-column align-items-center justify-content-center mt-5 mb-3">
            <div class="w-25 text-center">
                <img class="image-no-result" src="{{ my_asset('assets/frontend/images/result_search.png') }}" alt="ysg-result-search-img"  width="60%">
            </div>
            <div class="">
                <h6 class="product-no-result font-weight-bold">No products found in this shop</h6>
            </div>
            <div class="mt-4">
                <a class="search-for-ysg" href="{{ route('search', ['q' => $keyword]) }}">Search all products in YSG</a>
            </div>
        </div>
    @endif
    <style>
        .star-rating i {
            margin-right: 1px !important;
        }
    </style>
</section>
@include('frontend.inc.restricted_age.popup')
