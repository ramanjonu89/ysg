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
                        <div class="icon-block icon-block--style-1-v5 text-center active">
                            <div class="block-icon mb-0">
                                <i class="la la-credit-card"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('2. Payment') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="icon-block icon-block--style-1-v5 text-center">
                            <div class="block-icon c-gray-light mb-0">
                                <i class="la la-check-circle"></i>
                            </div>
                            <div class="block-content d-none d-md-block">
                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('3. Confirmation') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-3 gry-bg">
            <div class="container">
                <div class="row cols-xs-space cols-sm-space cols-md-space">
                    <div class="col-lg-8">
                        <form action="{{ route('payment.checkout') }}" class="form-default" data-toggle="validator" role="form" method="POST" id="checkout-form">
                            @csrf
                            <div class="card">
                                <div class="card-title px-4 py-3">
                                    <h3 class="heading heading-5 font-weight-bold">
                                        {{ __('Choose Payment Method') }}
                                    </h3>
                                </div>

                                @if($total == 0)
                                <div class="card-body text-center">
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <h3 class="heading heading-5 font-weight-bold">
                                                {{ __("No Payment Required.")}}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="card-body text-center">
                                    <div class="row">
                                        @if(paymentSetting('octifi.enable') && $total >= paymentSetting('octifi.minimumOrderTotal', 0))
                                        <div class="col-sm-6 mb-4">
                                            <div class="border-payment-method mx-5">
                                                <label class="payment_option mt-1" data-toggle="tooltip" data-title="Octifi Payment Gateway">
                                                    <input type="radio" id="" name="payment_option" value="{{\App\Models\Order::PAYMENT_TYPE_OCTIFI}}" class="online_payment" checked>
                                                    <span class="w-75">
                                                        <img loading="lazy"  src="{{ static_asset('frontend/images/icons/cards/Latitude-Pay_Logo_Blue_RGB.png') }}" class="img-fluid" style="height: 55px; background-color: #ffffff;">
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        @endif

                                        @if(paymentSetting('cybersource.enable'))
                                            <div class="col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option mt-1" data-toggle="tooltip" data-title="Cybersource Payment Gateway" data-original-title="" title="">
                                                        <input type="radio" id="" name="payment_option" value="cybersource" class="online_payment" checked="">
                                                        <span class="w-75">
                                                            <img loading="lazy" src="{{ static_asset('frontend/images/icons/cards/visa_master.png') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        @if(paymentSetting('nets.enable'))
                                            <div class="col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option my-2 pt-1" data-toggle="tooltip" data-title="Nets Payment Gateway">
                                                        <input type="radio" id="" name="payment_option" value="{{\App\Models\Order::PAYMENT_TYPE_NETS}}" class="online_payment" checked>
                                                        <span class="">
                                                            <img loading="lazy"  src="{{ my_asset('assets/frontend/images/icons/cards/nets_logo.png') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        @if(\App\BusinessSetting::where('type', 'paypal_payment')->first()->value == 1)
                                            <div class="col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option my-2" data-toggle="tooltip" data-title="Paypal">
                                                        <input type="radio" id="" name="payment_option" value="paypal" class="online_payment" checked>
                                                        <span style="width:40%">
                                                            <img loading="lazy"  src="{{ my_asset('frontend/images/icons/cards/paypal.png') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        @if(\App\BusinessSetting::where('type', 'stripe_payment')->first()->value == 1)
                                            <div class="col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option my-2" data-toggle="tooltip" data-title="Credit Card Payment Gateway">
                                                        <input type="radio" id="" name="payment_option" value="{{\App\Models\Order::PAYMENT_TYPE_STRIPE}}" class="online_payment" checked>
                                                        <span style="width:90%">
                                                            <img loading="lazy"  src="{{ my_asset('frontend/images/accepted_c22e0.png') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        @if(\App\BusinessSetting::where('type', 'sslcommerz_payment')->first()->value == 1)
                                            <div class="col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option mt-1" data-toggle="tooltip" data-title="sslcommerz">
                                                        <input type="radio" id="" name="payment_option" value="sslcommerz" class="online_payment" checked>
                                                        <span style="width:40%">
                                                            <img loading="lazy"  src="{{ my_asset('frontend/images/icons/cards/sslcommerz.png') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        @if(\App\BusinessSetting::where('type', 'instamojo_payment')->first()->value == 1)

                                            <div class="col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option mt-1" data-toggle="tooltip" data-title="Instamojo">
                                                        <input type="radio" id="" name="payment_option" value="instamojo" class="online_payment" checked>
                                                        <span style="width:40%">
                                                            <img loading="lazy"  src="{{ my_asset('frontend/images/icons/cards/instamojo.png') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        @if(\App\BusinessSetting::where('type', 'razorpay')->first()->value == 1)
                                            <div class="col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option mt-1" data-toggle="tooltip" data-title="Razorpay">
                                                        <input type="radio" id="" name="payment_option" value="razorpay" class="online_payment" checked>
                                                        <span style="width:40%">
                                                            <img loading="lazy"  src="{{ my_asset('frontend/images/icons/cards/rozarpay.png') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        @if(\App\BusinessSetting::where('type', 'paystack')->first()->value == 1)
                                            <div class="col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option mt-1" data-toggle="tooltip" data-title="Paystack">
                                                        <input type="radio" id="" name="payment_option" value="paystack" class="online_payment" checked>
                                                        <span style="width:40%">
                                                            <img loading="lazy"  src="{{ my_asset('frontend/images/icons/cards/paystack.png') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        @if(\App\BusinessSetting::where('type', 'voguepay')->first()->value == 1)
                                            <div class="col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option mt-1" data-toggle="tooltip" data-title="VoguePay">
                                                        <input type="radio" id="" name="payment_option" value="voguepay" class="online_payment" checked>
                                                        <span style="width:40%">
                                                            <img loading="lazy"  src="{{ my_asset('frontend/images/icons/cards/vogue.png') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        @if(\App\BusinessSetting::where('type', 'payhere')->first()->value == 1)
                                            <div class="col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option mt-1" data-toggle="tooltip" data-title="payhere">
                                                        <input type="radio" id="" name="payment_option" value="payhere" class="online_payment" checked>
                                                        <span style="width:40%">
                                                            <img loading="lazy"  src="{{ my_asset('frontend/images/icons/cards/payhere.png') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        @if(\App\Addon::where('unique_identifier', 'paytm')->first() != null && \App\Addon::where('unique_identifier', 'paytm')->first()->activated)
                                            <div class="col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option mt-1" data-toggle="tooltip" data-title="Paytm">
                                                        <input type="radio" id="" name="payment_option" value="paytm" class="online_payment" checked>
                                                        <span style="width:40%">
                                                            <img loading="lazy" src="{{ my_asset('frontend/images/icons/cards/paytm.jpg') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        {{-- cash on delivery --}}
                                        @if($enabledCashPayment)
                                            <div class="col-md-6 col-sm-6 mb-4">
                                                <div class="border-payment-method mx-5">
                                                    <label class="payment_option mt-1" data-toggle="tooltip" data-title="Cash on Delivery">
                                                        <input type="radio" id="" name="payment_option" value="cash_on_delivery" class="online_payment" checked>
                                                        <span style="width:40%">
                                                            <img loading="lazy"  src="{{ my_asset('frontend/images/icons/cards/cod.png') }}" class="img-fluid">
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        @if (Auth::check())
                                            @if (\App\Addon::where('unique_identifier', 'offline_payment')->first() != null && \App\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                                                @foreach(\App\ManualPaymentMethod::all() as $method)
                                                    <div class="col-6">
                                                        <label class="payment_option mb-4" data-toggle="tooltip" data-title="{{ $method->heading }}">
                                                            <input type="radio" id="" name="payment_option" value="{{ $method->heading }}" onchange="toggleManualPaymentData({{ $method->id }})">
                                                            <span>
                                                                <img loading="lazy"  src="{{ my_asset($method->photo)}}" class="img-fluid">
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                                @foreach(\App\ManualPaymentMethod::all() as $method)
                                                    <div id="manual_payment_info_{{ $method->id }}" class="d-none">
                                                        @php echo $method->description @endphp
                                                        @if ($method->bank_info != null)
                                                            <ul>
                                                                @foreach (json_decode($method->bank_info) as $key => $info)
                                                                    <li>Bank Name - {{ $info->bank_name }}, Account Name - {{ $info->account_name }}, Account Number - {{ $info->account_number}}, Routing Number - {{ $info->routing_number }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                    <div class="card mb-3 bg-gray text-left p-3 d-none">
                                        <div id="manual_payment_description">
                                        </div>
                                    </div>
                                    @if (Auth::check() && \App\BusinessSetting::where('type', 'wallet_system')->first()->value == 1)
                                        <div class="or or--1 mt-2">
                                            <span>or</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-xxl-6 col-lg-8 col-md-10 mx-auto">
                                                <div class="text-center bg-gray py-4">
                                                    <i class="fa"></i>
                                                    <div class="h5 mb-4">{{ __('Your wallet balance :') }} <strong>{{ single_price(Auth::user()->balance) }}</strong></div>
                                                    @if(Auth::user()->balance < $total)
                                                        <button type="button" class="btn btn-base-2" disabled>{{ __('Insufficient balance') }}</button>
                                                    @else
                                                        <button  type="button" onclick="use_wallet()" class="btn btn-base-1">{{ __('Pay with wallet') }}</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="pt-3">
                                <span class="text-danger" style="vertical-align: middle; font-size: 20px;">
                                    *
                                </span>
                                <input id="agree_checkbox" type="checkbox" required>
                                <label for="agree_checkbox">{{ __('By placing on order on ysg.com.sg you agree with the') }}</label>
                                <a href="{{ route('terms') }}">{{ __('terms and conditions') }}</a>,
                                <a href="{{ route('returnpolicy') }}">{{ __('return policy') }}</a> &
                                <a href="{{ route('privacypolicy') }}">{{ __('privacy policy') }}</a>
                            </div>
                            @if($hasPrivateSaleProducts)
                                <div class="pt-3">
                                    <span class="text-danger" style="vertical-align: middle; font-size: 20px;">
                                        *
                                    </span>
                                    <input id="private_sale_checkbox" type="checkbox" required>
                                    <label for="private_sale_checkbox" style="display: inline;">{{ __("Private Sale Products are subjected to the NO REFUND/NO RETURN/NO CANCELLATION policy. You acknowledge that it is sold 'AS IS' with minor imperfections as disclosed in the listing, and agree to no returns or refunds shall be permitted except as required by the Applicable Laws.") }}</label>
                                </div>
                            @endif
                            <div class="row align-items-center pt-3">
                                <div class="col-6">
                                    <a href="{{ route('cart') }}" style="border-radius:30px;" class="btn  link link--style-3">
                                        <i class="la la-mail-reply"></i>
                                        <strong>{{ __('Return To Cart') }}</strong>
                                    </a>
                                </div>
                                <div class="col-6 text-right">
                                    <button id="complete_order" type="button" onclick="submitOrder(this)"  style="border-radius:30px;" class="btn btn-styled btn-base-1">{{ __('Complete Order') }}</button>
                                </div>
                            </div>
                            <div class="info-warning-process-payment row text-danger pt-3 pl-5 pr-2">
                                <div>
                                    {{ __('To keep the payment process running smoothly, please enable JavaScript and turn off any ad-blockers.') }}
                                </div>
                                <div class="pt-2">
                                    {{ __('If you’re experiencing any other issues, please contact our customer service at') }}
                                    <strong>
                                        <a class="text-underline" href="mailto:cs@ysg.com.sg">{{ __('cs@ysg.com.sg') }}</a>.
                                    </strong>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-4 ml-lg-auto">
                        @include('frontend.partials.cart_summary')
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div>

            </div>
        </section>
    </div>
    @include('frontend.inc.delivery.popup')
    @include('frontend.inc.coupon.popup_by_admin')
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function(){
          // $('#next-step-button').click(function () {
          //   $.LoadingOverlay("show", {
          //     size: '75px',
          //     image: "",
          //     fontawesome: "fa fa-refresh fa-spin"
          //   });
          // });
            $(".online_payment").click(function(){
                $('#manual_payment_description').parent().addClass('d-none');
            });
        });
        function use_wallet(){
            $('input[name=payment_option]').val('wallet');
            if($('#agree_checkbox').is(":checked")){
                @if($hasPrivateSaleProducts)
                    if($('#private_sale_checkbox').is(":checked")){
                        $('#checkout-form').submit();
                    } else {
                        showFrontendAlert('error','{{ __('You need to agree with the private sale policy') }}');
                    }
                @else
                    $('#checkout-form').submit();
                @endif
            }else{
                showFrontendAlert('error','{{ __('You need to agree with our policies') }}');
            }
        }
        function submitOrder(el) {
            $(el).prop('disabled', true);
            @if (!$cart->isShowSelectAddress())
                const address_id = 1;
            @else
                const address_id = $('#address_id').val();
            @endif
            let isOnlySelfCollection = {{ $tempCart->isAllShopsSelfCollection() ? 'true' : 'false' }};
            if (!address_id && !isOnlySelfCollection) {
                showFrontendAlert('warning','{{ __('Please select delivery address') }}');
                $(el).prop('disabled', false);
            } else {
                if ($('#agree_checkbox').is(":checked")) {
                    @if ($hasPrivateSaleProducts)
                        if(!$('#private_sale_checkbox').is(":checked")){
                            showFrontendAlert('error','{{ __('You need to agree with the private sale policy') }}');
                            $(el).prop('disabled', false);
                            return false;
                        }
                    @endif
                    $.LoadingOverlay("show", {
                        size: '75px',
                        image: "",
                        fontawesome: "fa fa-refresh fa-spin"
                    });

                    $('#checkout-form').submit();
                } else {
                    showFrontendAlert('error','{{ __('You need to agree with our policies') }}');
                    $(el).prop('disabled', false);
                }
            }
        }
        function toggleManualPaymentData(id){
            $('#manual_payment_description').parent().removeClass('d-none');
            $('#manual_payment_description').html($('#manual_payment_info_'+id).html());
        }

        $('#delivery-address-edit').click(function() {
            $('#delivery-address-popup').modal('show');
            const address_checked = $('#delivery_address_default #address_id').val();
            getDeliveryAddresses(address_checked);
        });
    </script>
@endsection
