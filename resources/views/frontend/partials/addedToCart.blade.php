<div class="modal-body p-4 added-to-cart">

    <div class="text-center text-success">

        <i class="fa fa-check"></i>

        <h3>{{ __('Item added to your cart!') }}</h3>

    </div>

    <div class="product-box">

        <div class="block">

            <div class="block-image">

                <img src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($item->getThumbnailImg()) }}" class="lazyload" alt="{{ $item->getName() }}">

            </div>

            <div class="block-body">

                <h6 class="strong-600">

                    {{ __($item->getName()) }}

                </h6>

                <div class="row align-items-center no-gutters mt-2 mb-2">

                    <div class="col-sm-2">

                        <div>{{ __('Price') }}:</div>

                    </div>

                    <div class="col-sm-10">

                        <div class="heading-6 text-danger">

                            <strong>

                                {{ single_price($item->getTotalPrice()) }}

                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="text-center">

        <button class="btn btn-styled btn-base-1 btn-outline mb-3 mb-sm-0" data-dismiss="modal">{{ __('Back to shopping') }}</button>

        <a href="{{ route('cart') }}" class="btn btn-styled btn-base-1 mb-3 mb-sm-0">{{ __('Proceed to Checkout') }}</a>

    </div>

</div>

