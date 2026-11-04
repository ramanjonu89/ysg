@if (\App\BusinessSetting::where('type', 'best_selling')->first()->value == 1)
<section class="mb-4">
    <div class="container">
        <div class="px-2 py-4 p-md-4 bg-white shadow-sm">
            <div class="section-title-1 clearfix">
                <h3 class="heading-5 strong-700 mb-0 float-left">
                    <span class="mr-4">{{ __('Best Selling') }}</span>
                </h3>
                <ul class="inline-links float-right">
                    <!--  <li><a  class="active">{{ __('Top 20') }}</a></li> -->
                </ul>
            </div>
            <div class="caorusel-box arrow-round gutters-5">
                <div class="slick-carousel" data-slick-items="3" data-slick-lg-items="2"  data-slick-md-items="2" data-slick-sm-items="1" data-slick-xs-items="1" data-slick-rows="1">
                    @foreach ($products as $key => $product)
                    <div class="caorusel-card my-1">
                        <div class="row no-gutters product-box-2 align-items-center">
                            <div class="col-5 border-left">
                                <div class="position-relative overflow-hidden h-100">
                                    @if($product->has_discount)
                                        <div class="notify-badge">{{$product->discount_badge}}</div>
                                    @endif
                                    @if ($product->isProductForAdults())
                                        <a href="{{ route('product', $product->slug) }}" class="d-block product-image h-100">
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
                                    <div class="product-btns">
                                        <button class="btn add-wishlist" title="Add to Wishlist" onclick="addToWishList({{ $product->id }})">
                                            <i class="la la-heart-o"></i>
                                        </button>
                                        <button class="btn add-compare" title="Add to Compare" onclick="addToCompare({{ $product->id }})">
                                            <i class="la la-refresh"></i>
                                        </button>
                                        <button class="btn quick-view" title="Quick view" onclick="showNoticeAgeRestrictedProduct({{ json_encode(['id' => $product->id, 'age' => $product->category->is_age_restricted]) }})">
                                            <i class="la la-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-7 ">
                                <div class="p-3">
                                    <h2 class="product-title p-0">
                                        <a href="{{ route('product', $product->slug) }}" class="text-truncate">
                                            @if(array_key_exists($product->brand_id, $brands))
                                                {{ $brands[$product->brand_id] }}
                                            @endif
                                        </a>
                                    </h2>
                                    <div class="card-jfy-title"><a href="{{ route('product', $product->slug) }}" style="color:#000">{{ __($product->translation_name) }}</a></div>
                                    <div class="star-rating star-rating-sm mb-2">
                                        {{ renderStarRating($product->rating) }}
                                    </div>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            @include('frontend.inc.product_list.price_box', ['product' => $product])
                                        </div>
                                        <div class="float-right">
                                            <button class="add-to-cart btn" title="Add to Cart" onclick="showNoticeAgeRestrictedProduct({{ json_encode(['id' => $product->id, 'age' => $product->category->is_age_restricted]) }})">
                                                <i class="la la-shopping-cart"></i>
                                            </button>
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
</section>
@include('frontend.inc.restricted_age.popup')
@endif
