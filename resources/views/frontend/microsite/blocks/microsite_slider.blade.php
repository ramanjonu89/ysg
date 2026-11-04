{{--<div class="col-lg-12 order-1 order-lg-0 home-slider-full">--}}
@php
    if(!$block->isExpired()) {
        $blockContents = $block->blockContents;
    }
@endphp
@if(count($blockContents))
<div class="container px-0">
    <div class="home-slide">
        <div class="slick-carousel" data-slick-arrows="true" data-slick-dots="true"
             data-slick-autoplay="true">
            @foreach ($blockContents as $key => $content)
                    <div class="home-slide-item" style="height:450px;">
                        @if (!empty($content->options->link))
                        <a href="{{ $content->options->link }}">
                            <img class="d-block w-100 h-100 lazyload"
                                src="{{ my_asset('frontend/images/placeholder-rect.jpg') }}"
                                data-src="{{ my_asset($content->image) }}"
                                alt="{{ $content->title }}">
                        </a>
                        @else
                            <img class="d-block w-100 h-100 lazyload"
                                src="{{ my_asset('frontend/images/placeholder-rect.jpg') }}"
                                data-src="{{ my_asset($content->image) }}"
                                alt="{{ $content->title }}">
                        @endif
                    </div>
            @endforeach
        </div>
    </div>
</div>
@endif