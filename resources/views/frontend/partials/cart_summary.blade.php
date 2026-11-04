<div class="card sticky-top pr-0 mr-0" id="cart-summary-container">
    <div id="cart-summary-loading" class="cart-summary-loading d-none">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    @if (Auth::check() && $cart->isShowSelectAddress())
        @include('frontend.shipping_info')
    @endif
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
            @foreach ($tempCart->getItems() as $key => $tempItem)
                <tr class="cart_item">
                    <td class="product-name">
                        <div>
                            {{ $tempItem->getName() }}
                            <strong class="product-quantity">× {{ $tempItem->getQuantity() }}</strong>
                        </div>
                        @if ($tempCart->isShowCollectionAddress($tempItem->getShopId()))
                        <div class="self-collection-address">
                            <span>{{ __('Self-Collection address: ') }} </span>
                            <br />
                            <span class="self-collection-address-text">
                                @if (!empty($tempCart->getShopAddressTitle($tempItem->getShopId())))
                                    <i>[{{ $tempCart->getShopAddressTitle($tempItem->getShopId()) }}] - </i>
                                @endif
                                {{ $tempCart->getShopAddress($tempItem->getShopId()) }}
                            </span>
                        </div>
                        @endif
                        <div class="mt-1 float-left">
                            @if(!$tempCart->validatePurchaseRules($tempItem)['valid'])
                                <span class="message-purchase">{{ $tempCart->validatePurchaseRules($tempItem)['message'] }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="product-total text-right">
                        <span class="pl-4">{{ single_price($tempItem->getTotalPrice()) }}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <table class="table-cart table-cart-review">
            <tfoot>
            @if ($tempCart->hasNormalCoupons() && $tempCart->hasSellerCoupons())
                <tr class="cart-shipping border-bottom-0">
                    <th class="width-40"><b>{{__('Store Discount')}}</b></th>
                </tr>
                <tr class="cart-shipping border-bottom-dash">
                    <td class="pt-0" colspan="2">
                        @foreach($tempCart->getCoupons() as $key => $coupon)
                            @if (!$coupon->isCashVoucher() && $coupon->isSellerCoupon())
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="dropdown-coupon width-50">
                                        <p class="mb-0"><span role="button" class="text-left badge @if($coupon->isYsgCoupon()) bg-purple @else bg-orange @endif">{{ $coupon->getCode() }}</span></p>
                                        <div class="dropdown-discount">
                                            <span role="button" class="badge bg-black bg-base-6 opacity-5">{{ $coupon->getOwnerName() }}</span>
                                        </div>
                                    </div>
                                    <p class="width-30 mb-0"><span class="badge bg-black bg-base-6 opacity-5">{{ __('coupon.title', ['offValue' => $coupon->getDiscountLabel()] ) }}</span></p>
                                    <div class="@if(empty($paymentStep)) d-flex @endif justify-content-between width-20 mb-0 text-right">
                                        <p class="mb-0"><span class="text-italic">-{{ single_price($tempCart->getCouponDiscountValue($coupon)) }}</span></p>
                                        @if(empty($paymentStep))
                                            <div class="remove-coupon">
                                                <a href="#" onclick="removeCouponFormCartView(event, '{{ $coupon->getId() }}')"><i class="la la-trash"></i></a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </td>
                </tr>
                <tr class="cart-shipping border-bottom-dash">
                    <th>{{ __('Total Discount') }}</th>
                    <td class="text-right">
                        <span class="text-italic">- {{ single_price($tempCart->getSellersDiscount()) }}</span>
                    </td>
                </tr>
            @endif
            <tr class="cart-shipping border-bottom-dash">
                <th>{{ __('Shipping Fee') }}</th>
                <td class="text-right">
                    <span class="text-italic">{{ single_price($tempCart->getShippingFee()) }}</span>
                </td>
            </tr>
            <tr class="cart-shipping">
                <th>{{ __('Subtotal') }}</th>
                <td class="text-right">
                    <span class="strong-600">{{ single_price($tempCart->getSubtotal() - $tempCart->getSellersDiscount() + $tempCart->getShippingFee()) }}</span>
                </td>
            </tr>
            @if ($tempCart->hasNormalCoupons() && $tempCart->hasYsgCoupons())
                <tr class="cart-shipping border-bottom-0">
                    <th class="width-50"><b>{{__('Platform Discount')}}</b></th>
                </tr>
                <tr class="cart-shipping border-bottom-dash">
                    <td class="pt-1 pb-1" colspan="2">
                        @foreach($tempCart->getCoupons() as $key => $coupon)
                            @if (!$coupon->isCashVoucher() && $coupon->isYsgCoupon())
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="dropdown-coupon width-50">
                                        <p class="mb-0"><span role="button" class="text-left badge @if($coupon->isYsgCoupon()) bg-purple @else bg-orange @endif">{{ $coupon->getCode() }}</span></p>
                                        <div class="dropdown-discount">
                                            <span role="button" class="badge bg-black bg-base-6 opacity-5">{{ $coupon->getOwnerName() }}</span>
                                        </div>
                                    </div>
                                    <p class="width-30 mb-0"><span class="badge bg-black bg-base-6 opacity-5">{{ __('coupon.title', ['offValue' => $coupon->getDiscountLabel()] ) }}</span></p>
                                    <div class="@if(empty($paymentStep)) d-flex @endif justify-content-between width-20 mb-0 text-right">
                                        <p class="mb-0"><span class="text-italic">-{{ single_price($tempCart->getCouponDiscountValue($coupon)) }}</span></p>
                                        @if(empty($paymentStep))
                                            <div class="remove-coupon">
                                                <a href="#" onclick="removeCouponFormCartView(event, '{{ $coupon->getId() }}')"><i class="la la-trash"></i></a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </td>
                </tr>
            @endif
            <tr class="border-bottom-dash">
                <td class="pt-1 pb-1" colspan="2">
                    <div class="d-flex justify-content-between">
                        @if ($couponsYsg)
                            <span id="platform_vouchers" role="button" class="">
                                <span class="badge bg-purple">{{ __('PLATFORM VOUCHERS') }}</span>
                                <span class="badge bg-purple flatform-voucher-icon">
                                    <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i>
                                </span>
                            </span>
                        @endif
                    </div>
                </td>
            </tr>

            <tr class="cart-total">
                <th><span class="strong-600">{{ __('Total') }}<br> ( {{ __('Inclusive of Taxes') }})</span></th>
                <td class="text-right">
                    <strong><span>{{ single_price($tempCart->getGrandTotalAfterNormalCoupon()) }}</span></strong>
                </td>
            </tr>
            {{-- display cash voucher --}}
            @if ($tempCart->hasCoupons())
                <tr class="cash-voucher">
                    <td class="" colspan="2">
                        @foreach($tempCart->getCoupons() as $key => $coupon)
                            @if ($coupon->isCashVoucher())
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="dropdown-coupon width-50">
                                        <p class="mb-0"><span role="button" class="text-left badge bg-green-dark">{{ $coupon->getCode() }}</span></p>
                                        <div class="dropdown-discount">
                                            <span role="button" class="badge bg-black bg-base-6 opacity-5">{{ $coupon->getOwnerName() }}</span>
                                        </div>
                                    </div>
                                    <p class="width-30 mb-0"><span class="badge bg-black bg-base-6 opacity-5">{{ __('coupon.title_cash_voucher', ['offValue' => $coupon->getDiscountLabel()] ) }}</span></p>
                                    <div class="@if(empty($paymentStep)) d-flex @endif justify-content-between width-20 mb-0 text-right">
                                        <p class="mb-0"><span class="text-italic">-{{ single_price($coupon->getDiscount()) }}</span></p>
                                        @if(empty($paymentStep))
                                            <div class="remove-coupon">
                                                <a href="#" onclick="removeCouponFormCartView(event, '{{ $coupon->getId() }}')"><i class="la la-trash"></i></a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </td>
                </tr>
            @endif
            {{-- end display cash voucher --}}
            <tr class="grand-total">
                <th>
                    <span class="strong-600">{{ __('Grand Total') }}</span>
                </th>
                <td class="text-right">
                    <strong><span>{{ single_price($tempCart->getGrandTotal()) }}</span></strong>
                </td>
            </tr>
            @if (Auth::check() && \App\BusinessSetting::where('type', 'coupon_system')->first()->value == 1)
                <tr>
                    <td class="pt-0" colspan="2">
                        @if (Session::has('coupon_discount'))
                            <form class="form-inline" action="{{ route('checkout.remove_coupon_code') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex w-100">
                                    <div class="form-group flex-grow-1">
                                        <div class="form-control bg-gray w-100">{{ \App\Coupon::find(Session::get('coupon_id'))->code }}</div>
                                    </div>
                                    <button type="submit" class="btn btn-base-1">{{ __('Change Coupon') }}</button>
                                </div>
                            </form>
                        @else
                            <form class="form-inline" action="{{ route('checkout.apply_coupon_code') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex w-100">
                                    <div class="form-group flex-grow-1">
                                        <input type="text" class="form-control w-100" name="code" placeholder="{{ __('Have coupon code? Enter here') }}" required>
                                    </div>
                                    <button type="submit" class="form-group btn btn-base-1">{{ __('Apply') }}</button>
                                </div>
                            </form>
                        @endif
                    </td>
                </tr>
            @endif

            @if(paymentSetting('octifi.enable') && $tempCart->getGrandTotal() >= paymentSetting('octifi.minimumOrderTotal', 0))
                @if($cartSummaryTexts)
                    <tr class="cart-summary-text">
                        <td colspan="2">
                            <div class="align-items-center">
                                <span class="">{{ sprintf(__($cartSummaryTexts[0]), round($tempCart->getGrandTotal()*0.33, 2) ) }}</span>
                                <a class="" href="{{ paymentSetting('octifi.web') }}" target="_blank">
                                    <img loading="lazy" class="" src="{{ static_asset('frontend/images/icons/cards/Latitude-Pay_Logo_Blue_RGB.png') }}" style="height: 25px;">
                                </a>
                                <span>{{ __($cartSummaryTexts[1]) }}</span>
                            </div>
                            <div class="d-flex align-items-center mt-1">
                                <span class="mr-1">{{ __('Visit') }}</span>
                                <a class="mr-1" href="http://support.octifi.com/support/solutions/81000010762" target="_blank">
                                    {{ __('here') }}
                                </a>
                                <span class="">{{ __('for more information on') }}</span>
                                <a href="{{ paymentSetting('octifi.web') }}" target="_blank">
                                    <img loading="lazy" class="" src="{{ static_asset('frontend/images/icons/cards/Latitude-Pay_Logo_Blue_RGB.png') }}" style="height: 25px;">
                                </a>
                            </div>
                        </td>
                    </tr>
                @endif
            @endif
            <tr>
                <td colspan="2">
                    <div class="d-lex w-100">
                        @if(empty($paymentStep))
                            <a href="{{ route('checkout.payment_info') }}" id="next-step-button" class="btn btn-styled btn-base-1 w-100">{{ __('PROCEED TO PAYMENT') }}</a>
                        @endif
                        <p class="validation-address text-danger mt-2 mb-0 d-none">
                            <span>
                                <i class="fa fa-exclamation-triangle"></i>
                                {{ __('Please select a delivery address') }}
                            </span>
                        </p>
                        <p class="validation-cart text-danger mt-2 mb-0 d-none">
                            <span>
                                <i class="fa fa-exclamation-triangle"></i>
                                {{ __('Please select a item to checkout') }}
                            </span>
                        </p>
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
    .bg-green-dark {
        background-color: #006400 !important;
        color: #FFF;
    }

    .remove-coupon {
        display: inline-block;
        position: relative;
    }

    .remove-coupon {
        z-index: 100;
        padding: 0.125rem;
        padding-right: 0 !important;
        border-radius: 0.25rem;
    }

    .dropdown-discount a {
        text-decoration: none;
        display: block;
    }

    .table-cart tfoot tr.border-bottom-dash {
        border-bottom: 1px dashed #f1f1f1;
    }

    .validation-address {
        cursor: pointer;
    }

    .self-collection-address {
        font-weight: 300;
    }

    .self-collection-address-text {
        font-weight: normal!important;
    }

    .blink {
        animation: blink 1s step-start infinite;
    }

    @keyframes blink {
        0% {
            background-color: #f0f0f0;
        }
        50% {
            background-color: #f0f0f0;
        }
    }
</style>

@if ($cart->isShowSelectAddress())
<script type="text/javascript">
    $(document).ready(function() {
        let validNextStep = 0;

        const isCartNotEmpty      = {{ $tempCart->getTotalItems() > 0 ? 'true' : 'false' }};
        const allItemsMeetMinQty  = {{ $tempCart->allItemsMeetMinQuantity() ? 'true' : 'false' }};
        const isOnlySelfCollection = {{ $tempCart->isAllShopsSelfCollection() ? 'true' : 'false' }};

        const addressId = $('#address_id').val();

        if (!addressId) {
            $('.validation-address').removeClass('d-none');
        } else {
            $('.validation-address').addClass('d-none');
            validNextStep++;
        }

        if (isOnlySelfCollection) {
            $('.validation-address').addClass('d-none');
            validNextStep++;
        }

        if (isCartNotEmpty && allItemsMeetMinQty) {
            $('.validation-cart').addClass('d-none');
            validNextStep++;
        } else {
            $('.validation-cart').removeClass('d-none');
        }

        if (validNextStep >= 2) {
            $('#next-step-button').removeClass('btn-disabled-next-step');
        } else {
            $('#next-step-button').addClass('btn-disabled-next-step');
        }

        $('#platform_vouchers').on('click', function() {
            $('#coupon_by_admin_popup').modal('show');
        });
    });
</script>
@endif

<script>
    $(document).ready(function() {
        $('.validation-address').click(function() {
            $('html, body').animate({
                scrollTop: $(".shipping-info-container").offset().top - 120
            }, 500);

            $('.shipping-info-container form').addClass('blink');
            setTimeout(function() {
                $('.shipping-info-container form').removeClass('blink');
            }, 2000);
        });
    });
</script>
