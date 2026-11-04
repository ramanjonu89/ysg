<section class="mb-4">
    <div class="container">
        <div class="px-2 py-4 p-md-4 shadow-sm" style="background: {{ getMenuBarAreaColor() }} !important; color: #fff;">
            <div class="section-title-1 section-title-1-1 clearfix">
                <h3 class="heading-5 strong-700 mb-0 float-left">
                    <span class="mr-4">{{ __('Featured Products') }}</span>
                </h3>
            </div>
            <div class="caorusel-box arrow-round gutters-5">
                <div class="slick-carousel" data-slick-items="6" data-slick-xl-items="5" data-slick-lg-items="4"  data-slick-md-items="3" data-slick-sm-items="2" data-slick-xs-items="2" data-slick-autoplay="true">
                    @foreach ($shop->user->products->where('published', 1)->where('featured', 1) as $key => $product)
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
                                <div class="p-md-2 p-2">
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
