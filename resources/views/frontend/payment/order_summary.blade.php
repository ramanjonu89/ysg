<div class="card sticky-top">
    <div class="card-title py-3">
        <div class="row align-items-center">
            <div class="col-6">
                <h3 class="heading heading-3 strong-400 mb-0">
                    <span>{{ __('Summary') }}</span>
                </h3>
            </div>
            <div class="col-6 text-right">
                <span class="badge badge-md badge-success">{{ $tempCart->getTotalItems() }} {{ __('Items') }}</span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table-cart table-cart-review">
            <thead>
            <tr>
                <th class="product-name">{{ __('Product') }}</th>
                <th class="product-total text-right">{{ __('Total') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($tempCart->getItems() as $key => $cartItem)
                <tr class="cart_item">
                    <td class="product-name">
                        <div>
                            {{ $cartItem->getName() }}
                            <strong class="product-quantity">× {{ $cartItem->getQuantity() }}</strong>
                        </div>
                        @if ($tempCart->isShowCollectionAddress($cartItem->getShopId()))
                        <div class="self-collection-address">
                            <span>{{ __('Self-Collection address: ') }} </span>
                            <br />
                            <span class="self-collection-address-text">
                                @if (!empty($tempCart->getShopAddressTitle($cartItem->getShopId())))
                                    <i>[{{ $tempCart->getShopAddressTitle($cartItem->getShopId()) }}] - </i>
                                @endif
                                {{ $tempCart->getShopAddress($cartItem->getShopId()) }}
                            </span>
                        </div>
                        @endif
                    </td>
                    <td class="product-total text-right">
                        <span class="pl-4">{{ single_price($cartItem->getTotalPrice()) }}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <table class="table-cart table-cart-review">
            <tfoot>
            <tr class="cart-subtotal">
                <th>{{ __('Subtotal') }}</th>
                <td class="text-right">
                    <span class="strong-600">{{ single_price($tempCart->getSubtotal()) }}</span>
                </td>
            </tr>
            @if ($tempCart->hasCoupons())
                <tr class="cart-shipping border-bottom-0">
                    <th class="width-40">{{__('Coupon Discount')}}
                    </th>
                </tr>
                <tr class="cart-shipping">
                    <td class="pt-0" colspan="2">
                        @foreach($tempCart->getCoupons() as $key => $coupon)
                            <div class="d-flex justify-content-between">
                                <div class="dropdown-coupon width-50">
                                    <p class="mb-0"><span role="button"
                                                          class="text-left badge @if($coupon->isYsgCoupon()) bg-purple @else bg-orange @endif">{{ $coupon->getCode() }}</span>
                                    </p>
                                    <div class="dropdown-discount">
                                        <span role="button"
                                              class="badge bg-black bg-base-6 opacity-5">{{ $coupon->getOwnerName() }}</span>
                                    </div>
                                </div>
                                <p class="width-30 mb-0"><span
                                        class="badge bg-black bg-base-6 opacity-5">{{ __('coupon.title', ['offValue' => $coupon->getDiscountLabel()] ) }}</span>
                                </p>
                                <p class="width-20 mb-0 text-right"><span
                                        class="text-italic">-{{ single_price($tempCart->getCouponDiscountValue($coupon)) }}</span>
                                </p>
                            </div>
                        @endforeach
                    </td>
                </tr>
                <tr class="cart-shipping">
                    <th>{{__('Total Discount')}}</th>
                    <td class="text-right">
                        <span class="text-italic">-{{ single_price($tempCart->getCouponDiscount()) }}</span>
                    </td>
                </tr>
            @endif
            <tr class="cart-shipping">
                <th>{{ __('Shipping Fee') }}</th>
                <td class="text-right">
                    <span class="text-italic">{{ single_price($tempCart->getShippingFee()) }}</span>
                </td>
            </tr>
            <tr class="cart-total">
                <th><span class="strong-600">{{ __('Total') }}<br> ( {{ __('Inclusive of Taxes') }})</span></th>
                <td class="text-right">
                    <strong><span>{{ single_price($tempCart->getGrandTotal()) }}</span></strong>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
