@if ($shop->sliders != null && $shop->sliders != '[]')
    <section class="py-4">
        <div class="container">
            <div class="home-slide">
                <div class="slick-carousel" style="max-height:450px;" data-slick-arrows="true" data-slick-dots="true"
                     data-slick-autoplay='true'>
                    @foreach (json_decode($shop->sliders) as $key => $slide)
                        <div class="">
                            <img class="d-block w-100 lazyload"
                                 src="{{ my_asset('frontend/images/placeholder-rect.jpg') }}"
                                 data-src="{{ my_asset($slide) }}" alt="{{ $key }} slide">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
