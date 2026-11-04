<div class="block image_link">
    <div class="wrap container">
        <div class="row">
            @foreach($blockService->getListCorporateSeller($block) as $seller)
                @if ($seller->image)
                    <div class="col-6" style="padding: 0.1rem;">
                        <a href="{{ route('shop.visit', $seller->user->shop->slug) }}">
                            <img src="{{ $seller->image }}" alt="{{$seller->user->name}}" class="width-100">
                        </a>
                    </div>
                @endif
            @endforeach
            @foreach($block->blockContents as $item)
                <div class="col-6" style="padding: 0.1rem;">
                    @if (isset($item->link) && trim($item->link) != '')
                        <a href="{{ $item->link }}">
                            <img src="{{$item->image??url('/assets/img/sellers/shop-logo.jpg') }}" alt="{{$item->getTranslationTitleAttribute()}}" class="width-100">
                        </a>
                    @else
                        <img src="{{$item->image??url('/assets/img/sellers/shop-logo.jpg') }}" alt="{{$item->getTranslationTitleAttribute()}}" class="width-100">
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
