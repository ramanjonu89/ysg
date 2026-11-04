<a href="" class="nav-box-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="la la-shopping-cart d-inline-block nav-box-icon"></i>
    <span class="nav-box-text d-none d-xl-inline-block">{{ __('Cart') }}</span>
    <span class="nav-box-number"> {{ $cart->getTotalItems() }} </span>
</a>

<ul class="dropdown-menu dropdown-menu-right px-0">

    <li>

        <div class="dropdown-cart px-0">

            @if($cart->getTotalItems() > 0)

                    <div class="dc-header">

                        <h3 class="heading heading-6 strong-700">{{ __('Cart Items') }}</h3>

                    </div>

                    <div class="dropdown-cart-items c-scrollbar">

                        @foreach($cart->getItems() as $key => $cartItem)

                            <div class="dc-item">

                                <div class="d-flex align-items-center">

                                    <div class="dc-image">

                                        <a href="{{ route('product', $cartItem->getSlug()) }}">

                                            <img loading="lazy"  src="{{ my_asset($cartItem->getThumbnailImg()) }}" class="img-fluid" alt="">

                                        </a>

                                    </div>

                                    <div class="dc-content">

                                        <span class="d-block dc-product-name text-capitalize strong-600 mb-1">

                                            <a href="{{ route('product', $cartItem->getSlug()) }}">

                                                {{ __($cartItem->getName()) }}

                                            </a>

                                        </span>



                                        <span class="dc-quantity">x{{ $cartItem->getQuantity() }}</span>

                                        <span class="dc-price">{{ single_price($cartItem->getTotalPrice()) }}</span>

                                    </div>

                                    <div class="dc-actions">

                                        <button onclick="removeFromCart(event, '{{ $key }}')">

                                            <i class="la la-close"></i>

                                        </button>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                    <div class="dc-item py-3">

                        <span class="subtotal-text">{{ __('Subtotal') }}</span>

                        <span class="subtotal-amount">{{ single_price($cart->getSubtotal()) }}</span>

                    </div>

                    <div class="py-2 text-center dc-btn">
                        <ul class="inline-links inline-links--style-3 d-flex align-items-center justify-content-between">
                            <li class="pl-3">
                                <span class="badge badge-md badge-success">{{ $cart->getTotalItems() }} {{ __('Items') }}</span>
                            </li>
                            <li class="pr-3">
                                <a href="{{ route('cart') }}" class="link link--style-1 text-capitalize btn btn-base-1 px-3 py-1 light-text">
                                    <i class="la la-mail-forward"></i> {{ __('View cart') }}
                                </a>
                            </li>
                        </ul>
                    </div>
            @else

                <div class="dc-header">

                    <h3 class="heading heading-6 strong-700">{{ __('Your Cart is empty') }}</h3>

                </div>

            @endif

        </div>

    </li>

</ul>

