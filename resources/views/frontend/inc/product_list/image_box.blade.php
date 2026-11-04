@if ($product->isProductForAdults() && !(isset($isConfirmAge)))
    <a href="{{ route('product', $product->slug) }}" class="d-block product-image text-center">
        <img class="img-fit lazyload cover-image adult-image"
            src="{{ my_asset('assets/frontend/images/adult_notice.png') }}"
            alt="{{ __($product->translation_name) }}">

        <img class="img-fit lazyload normal-image hidden-image" src="{{ my_asset('frontend/images/placeholder.jpg') }}"
            data-src="{{ my_asset($product->getThumbnailImg()) }}" alt="{{ __($product->translation_name) }}">
    </a>
@else
    <a href="{{ route('product', $product->slug) }}" class="d-block product-image text-center">
        <img class="img-fit lazyload normal-image" src="{{ my_asset('frontend/images/placeholder.jpg') }}"
            data-src="{{ my_asset($product->getThumbnailImg()) }}" alt="{{ __($product->translation_name) }}">
    </a>
@endif
