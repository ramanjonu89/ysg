@extends('frontend.layouts.app')
@section('content')
    <div id="page-content">
        <section class="slice-xs sct-color-2 border-bottom">
            <div class="container container-sm">
                <div class="row cols-delimited justify-content-center">
                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center ">
                            <div class="block-icon c-gray-light mb-0">
                                <i class="la la-shopping-cart"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('1. My Cart') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center ">
                            <div class="block-icon mb-0 c-gray-light">
                                <i class="la la-map-o"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('2. Shipping info') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center active">
                            <div class="block-icon mb-0">
                                <i class="la la-truck"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('3. Delivery info') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center">
                            <div class="block-icon c-gray-light mb-0">
                                <i class="la la-credit-card"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('4. Payment') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center">
                            <div class="block-icon c-gray-light mb-0">
                                <i class="la la-check-circle"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">5. {{ __('Confirmation') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-4 gry-bg">
            <div class="container">
                <div class="row cols-xs-space cols-sm-space cols-md-space">
                    <div class="col-xl-8">
                        @foreach ($cart->getItemsBySellerGroup() as $sellerId => $seller)
                            <div class="card mb-3">
                                <div class="card-header bg-white py-3">
                                    <h5 class="heading-6 mb-0">
                                        <b> {{ $seller['info']['name'] }}
                                            ({{ single_price($cart->getSubTotalAfterDiscountByShopId($seller['info']['shopId'])) }}
                                            ) <i class="fa fa-info-circle" aria-hidden="true" data-html="true"
                                                 data-toggle="tooltip" data-placement="top"
                                                 title="<b>SubTotal</b>: {{ single_price($cart->getSubTotal(['shopId' => $seller['info']['shopId']])) }} <br /><b>Discount</b>: {{ single_price($cart->getCouponDiscountByShopId($seller['info']['shopId'])) }} <br />"></i>
                                            @if ($seller['info']['companyShippingType'] == 2) <span
                                                class="btn btn-success"
                                                style="border-radius:10px; font-size:10px;float:right">Seller Direct Shipping</span>@else
                                                <span class="btn btn-success"
                                                      style="border-radius:10px; font-size:10px;float:right">{{ __('YSG Shipping') }}</span> @endif
                                        </b></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row no-gutters">
                                        <div class="col-md-6">
                                            <table class="table-cart">
                                                <tbody>
                                                @foreach ($seller['items'] as $item)
                                                    <tr class="cart-item">
                                                        <td class="product-image" width="25%">
                                                            <a href="{{ route('product', $item->getSlug()) }}"
                                                               target="_blank">
                                                                <img loading="lazy"
                                                                     src="{{ my_asset($item->getThumbnailImg()) }}">
                                                            </a>
                                                        </td>
                                                        <td class="product-name strong-600">
                                                            <a href="{{ route('product', $item->getSlug()) }}"
                                                               target="_blank" class="d-block c-base-2">
                                                                {{ $item->getName() }} x {{ $item->getQuantity() }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label
                                                        class="d-flex align-items-center p-3 border rounded gry-bg c-pointer">
                                                        <input type="radio" name="shipping_type_{{ $sellerId }}"
                                                               value="home_delivery" checked class="d-none"
                                                               onchange="show_pickup_point(this)"
                                                               data-target=".pickup_point_id_{{ $sellerId }}">
                                                        <span class="radio-box"></span>
                                                        <span class="d-block ml-2 strong-600">
                                                                {{  __('Home Delivery') }}
                                                            </span>
                                                    </label>
                                                </div>
                                                <div class="col-6">
                                                    <label
                                                        class="d-flex align-items-center p-3 border rounded gry-bg c-pointer">
                                                        Shipping Fee : <span
                                                            id="directshippingcost-{{$sellerId}}">{{ single_price($cart->getShippingFeeByShopId($seller['info']['shopId'])) }}</span>
                                                    </label>
                                                </div>
                                                <div class="col-12">
                                                    {!! sprintf(__('Fulfilled by Seller. Free shipping above $%s spend.'), '<span id="directabove">'.$seller['info']['freeShippingAbove'].'</span>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if (!empty($coupons[$seller['info']['shopId']]))
                                    <div class="card-footer">
                                        <div class="row no-gutters">
                                            <div class="col-4">
                                                <div class="product-description-label">{{ translate('Shop Vouchers')}}
                                                    :
                                                </div>
                                            </div>
                                            <div class="col-8 text-right">
                                                <div>
                                                    @foreach(array_slice($coupons[$seller['info']['shopId']], 0, 2) as $coupon)
                                                        <span
                                                            class="badge bg-orange">{{ $coupon->discount_label }} OFF</span>
                                                    @endforeach
                                                    <span data-toggle="modal" data-target="#{{$sellerId}}" role="button">
                                                            {{ __("See all and apply shop vouchers") }}
                                                            <i class="fa fa-angle-right"></i>
                                                        </span>
                                                </div>
                                            </div>
                                            @include('frontend.inc.coupon.popup', ['modalId' => $sellerId, 'coupons' => $coupons[$seller['info']['shopId']], 'canApply' => true])
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        <div class="row align-items-center pt-4">
                            <div class="col-md-6">
                                <a href="/checkout" class="link link--style-3">
                                    <i class="ion-android-arrow-back"></i>
                                    {{ __('Back')}}
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('checkout.payment_info') }}" id="next-step-button" type="submit"
                                   class="btn btn-styled btn-base-1">{{ __('Continue to Payment')}}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 ml-lg-auto">
                        @include('frontend.partials.cart_summary')
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
      $(function() {
        $('#next-step-button').click(function () {
          $.LoadingOverlay("show", {
            size: '75px',
            image: "",
            fontawesome: "fa fa-refresh fa-spin"
          });
        });
      });
        function display_option(key){
        }
        function show_pickup_point(el) {
        	var value = $(el).val();
        	var target = $(el).data('target');
            console.log(value);
        	if(value == 'home_delivery'){
                if(!$(target).hasClass('d-none')){
                    $(target).addClass('d-none');
                }
        	}else{
        		$(target).removeClass('d-none');
        	}
        }
    </script>
@endsection
