<div class="mb-4">
    <div class="container">
        <div class="row gutters-5">
            @foreach ($listBanner as $key => $banner)
                @if (!$banner->isExpired())
                    @switch($banner->template)
                        @case('1-col')
                            <div class="col-lg-4" style="padding-top:5px;padding-bottom:5px;">
                        @break
                        @case('2-col')
                            <div class="col-lg-8" style="padding-top:5px;padding-bottom:5px;">
                        @break
                        @case('3-col')
                            <div class="col-lg-12" style="padding-top:5px;padding-bottom:5px;">
                        @break
                        @default
                            <div class="col-lg-4" style="padding-top:5px;padding-bottom:5px;">
                    @endswitch
                    <div class="media-banner mb-3 mb-lg-0">
                        @if(isset($banner->url))
                            <a href="{{ $banner->url }}" class="banner-container">
                                <img src="{{ my_asset('frontend/images/placeholder-rect.jpg') }}"
                                    data-src="{{ my_asset($banner->photo) }}" alt="{{ env('APP_NAME') }} promo"
                                    class="img-fluid lazyload">
                            </a>
                        @else
                            <div>
                                <img src="{{ my_asset('frontend/images/placeholder-rect.jpg') }}"
                                    data-src="{{ my_asset($banner->photo) }}" alt="{{ env('APP_NAME') }} promo"
                                    class="img-fluid lazyload">
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
