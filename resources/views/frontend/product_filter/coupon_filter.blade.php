<div class="coupon-title-bar">
    <div class="col-sm-7 detail-coupon">
        <div class="coupon-wrap">
            <div class="d-flex align-items-center c-base-5 @if($coupon->isYsgCoupon()) bg-purple @else bg-coupon-filter @endif">
                <div class="coupon-title-bar-icon"></div>
                <div class="w-100 ml-0 mr-0">
                    <table class="w-100">
                        <tr>
                            <td class="related-shop" style="width:35%">
                                <div class="w-100">
                                    <div class="d-flex align-items-center justify-content-center padding-detail-coupon">
                                        <div class="centered-container c-base-5">
                                            @if (!$coupon->isYsgCoupon())
                                                <img height="70" class="lazyload"
                                                    src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                    data-src="{{ my_asset($coupon->shop->getLogoShop()) }}"
                                                    alt="{{ $coupon->shop->name }}">
                                                <a
                                                    href="{{ route('shop.visit', $coupon->shop->slug) }}" 
                                                    class="strong-500 c-base-5 heading-6 pt-1 mb-0 text-center">{{ $coupon->shop->name }} 
                                                </a>
                                            @else
                                                <img height="70" class="lazyload"
                                                    src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                    data-src="{{ $logoUrl }}"
                                                    alt="{{ $logoAlt }}">
                                                <h6 class="strong-500 heading-4 pt-1 mb-0">
                                                    {{ ('YSG') }} 
                                                </h6>
                                            @endif
                                            
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="coupon-title">
                                    <div class="w-100">
                                        <div
                                            class="d-flex align-items-center justify-content-between padding-detail-coupon">
                                            <div class="col-sm-9 c-base-5 content-coupon-by-seller">
                                                <h5>{{ __('coupon.title', ['offValue' => $coupon->discount_label]) }}</h5>
                                                <div class="mb-0">
                                                    @if ($coupon->min_buy && $coupon->max_discount)
                                                        {{ __('coupon.has_min_buy_and_max_discount', ['min_buy' => format_price($coupon->min_buy), 'max_discount' => format_price($coupon->max_discount)]) }}
                                                    @elseif ($coupon->min_buy)
                                                        {{ __('coupon.min_buy', ['value' => format_price($coupon->min_buy)]) }}
                                                    @elseif ($coupon->max_discount)
                                                        {{ __('coupon.max_discount', ['value' => format_price($coupon->max_discount)]) }}
                                                    @else
                                                        {{ __('No Min. Spending') }}
                                                    @endif
                                                </div>
                                                <p class="end-date-coupon">
                                                    {{ __('coupon.use_before', ['date' => $coupon->end_date_formatted]) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-5">

    </div>
</div>
