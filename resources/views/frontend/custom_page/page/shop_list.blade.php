<div class="block shop_list mt-5 mb-5">
    <div class="wrap container">
        <div class="bg-white pt-3">
            <div class="row">
                @foreach($blockService->getListSeller($block) as $seller)
                    @if($seller->user->shop && ($seller->microsite == null || ($seller->microsite != null && $seller->microsite->active !== 1)))
                        <div class="col-xxl-2 col-lg-4 col-sm-6 text-center">
                            <a href="{{ route('shop.visit', $seller->user->shop->slug) }}" class="d-block p-3 mb-3 border rounded">
                                <img src="{{ ($seller->user->shop->logo)?my_asset($seller->user->shop->logo):url('/assets/img/sellers/shop-logo.jpg') }}" class="lazyload img-fit" height="50" alt="{{$seller->user->name}}">
                                <div class="name text-truncate"><b>{{ __($seller->user->name) }}</b></div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
