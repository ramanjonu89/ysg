@foreach ($homeCategories as $key => $homeCategory)
    @if ($homeCategory->is_enable_of_homecategory)
        <section class="mb-4">
            <div class="container">
                <div class="px-2 py-4 p-md-4 bg-white shadow-sm">
                    <div class="section-title-1 clearfix">
                        <h3 class="heading-5 strong-700 mb-0 float-lg-left">
                            <span class="mr-4">{{ translate($homeCategory->category->translation_name) }}</span>
                        </h3>
                        <ul class="inline-links float-lg-right nav mt-3 mb-2 m-lg-0">
                            <li><a href="{{ route('category.view', $homeCategory->category->slug) }}" class="active">View More</a></li>
                        </ul>
                    </div>
                    <div class="caorusel-box arrow-round gutters-5">
                        <div class="slick-carousel" data-slick-items="6" data-slick-xl-items="5" data-slick-lg-items="4"  data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2">
                            @foreach ($products[$homeCategory->category->id] as $key => $product)
                                <div class="caorusel-card">
                                    <div class="product-box-2 bg-white alt-box my-2">
                                        <div class="position-relative overflow-hidden">
                                            @if($product->has_discount)
                                                <div class="notify-badge">{{$product->discount_badge}}</div>
                                            @endif
                                            @if ($product->isProductForAdults())
                                                <a href="{{ route('product', $product->slug) }}" class="d-block product-image h-100 text-center">
                                                    <img class="img-fit lazyload mx-auto" 
                                                        src="{{ my_asset('assets/frontend/images/adult_notice.png') }}" 
                                                        alt="{{ __($product->translation_name) }}">
                                                </a>
                                            @else
                                                <a href="{{ route('product', $product->slug) }}" class="d-block product-image h-100">
                                                    <img class="img-fit lazyload mx-auto" 
                                                        src="{{ my_asset('frontend/images/placeholder.jpg') }}" 
                                                        data-src="{{ my_asset($product->thumbnail_img) }}" 
                                                        alt="{{ __($product->translation_name) }}"
                                                    >
                                                </a>
                                            @endif
                                            <div class="product-btns clearfix">
                                                <button class="btn add-wishlist" title="Add to Wishlist" onclick="addToWishList({{ $product->id }})" tabindex="0">
                                                    <i class="la la-heart-o"></i>
                                                </button>
                                                <button class="btn add-compare" title="Add to Compare" onclick="addToCompare({{ $product->id }})" tabindex="0">
                                                    <i class="la la-refresh"></i>
                                                </button>
                                                <button class="btn quick-view" title="Quick view" onclick="showNoticeAgeRestrictedProduct({{ json_encode(['id' => $product->id, 'age' => $product->category->is_age_restricted]) }})" tabindex="0">
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
                                                    {{ $product->brand_name }}
                                                </a>
                                            </h2>
                                            <div class="card-jfy-title"><a href="{{ route('product', $product->slug) }}" style="color:#000">{{ __($product->translation_name) }}</a></div>
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
@endforeach
