<div class="sellers-area">
    <div class="container">
        <div id="sellersCarousel" class="carousel slide mt-3" data-ride="carousel" data-interval="5000">
            <div class="carousel-inner">
                @php $chunks = $sellers->chunk(3); @endphp
                @foreach ($chunks as $chunkIndex => $chunk)
                    <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                        <div class="row">
                            @foreach ($chunk as $seller)
                                <div class="col-12 col-md-4">
                                    <div class="row no-gutters box-3 align-items-center border">
                                        <div class="col-4">
                                            <a href="{{ route('shop.visit', $seller->shop->slug) }}" title="{{ $seller->shop->name }}" class="d-block product-image p-3">
                                                <img
                                                    src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                    data-src="@if ($seller->shop->logo !== null) {{ my_asset($seller->shop->logo) }} @else {{ my_asset('frontend/images/placeholder.jpg') }} @endif"
                                                    alt="{{ $seller->shop->name }}"
                                                    class="img-fluid lazyload"
                                                >
                                            </a>
                                        </div>
                                        <div class="col-8 border-left">
                                            <div class="p-3">
                                                <h2 class="product-title mb-0 p-0 text-truncate">
                                                    <a href="{{ route('shop.visit', $seller->shop->slug) }}" title="{{ $seller->shop->name }}">{{ translate($seller->shop->name) }}</a>
                                                </h2>
                                                <div class="star-rating star-rating-sm mb-2" style="background-color: transparent">
                                                    @if ($seller->sumReviewRating > 0)
                                                        {{ renderStarRating($seller->sumReviewRating) }}
                                                    @else
                                                        {{ renderStarRating(0) }}
                                                    @endif
                                                </div>
                                                <div class="">
                                                    <a href="{{ route('shop.visit', $seller->shop->slug) }}" class="icon-anim">
                                                        {{ __('Visit Store') }} <i class="la la-angle-right text-sm"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if ($chunks->count() > 1)
                <a class="carousel-control-prev" href="#sellersCarousel" role="button" data-slide="prev">
                    {{-- <span class="carousel-control-prev-icon" aria-hidden="true"></span> --}}
                    <i class="la la-angle-left"></i>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#sellersCarousel" role="button" data-slide="next">
                    {{-- <span class="carousel-control-next-icon" aria-hidden="true"></span> --}}
                    <i class="la la-angle-right"></i>
                    <span class="sr-only">Next</span>
                </a>
                
                {{-- <ol class="carousel-indicators">
                    @foreach ($chunks as $chunkIndex => $chunk)
                        <li data-target="#sellersCarousel" data-slide-to="{{ $chunkIndex }}" class="{{ $chunkIndex === 0 ? 'active' : '' }}"></li>
                    @endforeach
                </ol> --}}
            @endif
        </div>
    </div>
</div>

<style>
    .carousel-control-next, .carousel-control-prev {
        width: 20px;
    }
    .carousel-control-next {
        right: -20px;
    }
    .carousel-control-prev {
        left: -20px;
    }
    .carousel-control-next, .carousel-control-prev {
        color: #000;
    }

    .carousel-control-next .la, .carousel-control-prev .la {
        font-size: 27px;
    }

    .carousel-control-next:hover, .carousel-control-prev:hover {
        color: #000;
    }
/* 
    .carousel-indicators {
        bottom: -35px;
    }

    .carousel-indicators li {
        background-color: #000;
    } */
</style>