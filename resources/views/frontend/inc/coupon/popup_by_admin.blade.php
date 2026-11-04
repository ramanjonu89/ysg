<div class="modal fade coupon-popup-by-admin" id="coupon_by_admin_popup" tabindex="-1" aria-labelledby="coupon-by-admin-label"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="coupon-by-admin-label">{{ __('Platform Vouchers') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" size="25"
                             color="#999999" style="color: rgb(153, 153, 153);" height="25" width="25"
                             xmlns="http://www.w3.org/2000/svg"><path
                                d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg>
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="coupon-wrap">
                    @foreach($couponsYsg as $coupon)
                        <div class="coupon c-base-5 bg-purple">
                            <div class="coupon-left"></div>
                            <table class="coupon-con w-100">
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between padding-apply-coupon" >
                                            <div class="col-sm-9 content-coupon-admin">
                                                <h5 class="title-coupon">{{ __('coupon.title', ['offValue' => $coupon->discount_label]) }}</h5>
                                                <p class="mb-0">
                                                    @if ($coupon->min_buy && $coupon->max_discount)
                                                        {{ __('coupon.has_min_buy_and_max_discount', ['min_buy' => format_price($coupon->min_buy), 'max_discount' => format_price($coupon->max_discount)]) }}
                                                    @elseif ($coupon->min_buy)
                                                        {{ __('coupon.min_buy', ['value' => format_price($coupon->min_buy)]) }}
                                                    @elseif ($coupon->max_discount)
                                                        {{ __('coupon.max_discount', ['value' => format_price($coupon->max_discount)]) }}
                                                    @else
                                                        {{ __('No Min. Spending') }}
                                                    @endif
                                                </p>
                                                <p class="mb-1">
                                                    <div class="d-flex align-items-center">
                                                        @if ($coupon->type === 'cart_base' && (!$coupon->shop_ids || in_array(0, $coupon->shop_ids)))
                                                            <span>
                                                                {{ __('All Products') }}
                                                            </span>
                                                        @else
                                                            <span>
                                                                {{ __('Selected Products Only') }}
                                                            </span>
                                                            <a href="{{ route('search', ['coupon_id' => encrypt($coupon->id)]) }}" class="coupon_for_specific_store_products pt-1 pl-2">
                                                                <i id="coupon_for_shop" class="fa fa-info-circle font-size-1 c-gray-dark"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </p>
                                                <p class="mb-0">{{ __('coupon.use_before', ['date' => $coupon->end_date_formatted] ) }}</p>
                                            </div>
                                            <div class="col-sm-3 apply-coupon-admin pl-0 pr-0 text-center">
                                                @if ($coupon->free_shipping)
                                                    <div class="pb-4">{{ __('Free Shipping')}}</div>
                                                @endif
                                                <div>
                                                    <form class="form-inline mb-2 justify-content-center" action="{{ route('checkout.apply_coupon_code') }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" value="{{$coupon->code}}" name="code" />
                                                        @if(!empty($coupon->is_can_apply))
                                                            <button class="btn btn-primary">{{ __("Apply") }}</button>
                                                        @else
                                                            <button class="btn btn-info opacity-5 disabled" disabled>{{ __("Apply") }}</button>
                                                        @endif
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .coupon-popup-by-admin .modal-body {
        max-height: 550px;
        overflow-y: auto !important;
    }
    .content-coupon-admin {
        padding-left: 0.5rem !important;
        padding-right: 0 !important;
    }
    .content-coupon-admin .title-coupon {
        margin-top: 0.125rem !important;
    }

    .apply-coupon-admin .disabled {
        cursor: not-allowed !important;
        pointer-events: none !important;
    }
</style>
