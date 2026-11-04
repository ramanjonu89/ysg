<section class="bg-white">
    <div class="container">
        <div class="row sticky-top">
            <div class="col">
                <div class="seller-shop-menu">
                    <ul class="inline-links">
                        <li @if(!$type) class="active" @endif>
                            @if(isMicrosite())
                                <a href="{{ route('microsite.index', $shop->slug) }}">{{ __('Store Home') }}</a>
                            @else
                                <a href="{{ route('shop.visit', $shop->slug) }}">{{ __('Store Home') }}</a>
                            @endif
                        </li>
                        <li @if($type && $type == 'all_products') class="active" @endif><a href="{{ route('shop.visit.type', ['slug'=>$shop->slug, 'type'=>'all_products']) }}">{{ __('All Products') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>