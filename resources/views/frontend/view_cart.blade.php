@extends('frontend.layouts.app')
@section('meta')
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
@endsection

@section('content')
<section class="slice-xs sct-color-2 border-bottom">
    <div class="container container-sm">
        <div class="row cols-delimited justify-content-center">
            <div class="col">
                <div class="icon-block icon-block--style-1-v5 text-center active">
                    <div class="block-icon mb-0">
                        <i class="la la-shopping-cart"></i>
                    </div>
                    <div class="block-content d-none d-md-block">
                        <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('1. My Cart') }}</h3>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="icon-block icon-block--style-1-v5 text-center">
                    <div class="block-icon c-gray-light mb-0">
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

<section class="py-4 gry-bg">
    <div class="container">
        @if ($cart->getTotalItems() > 0)
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-xl-8">
                    @include('frontend.partials.cart_details', [
                        'cart' => $cart,
                        'tempCart' => $tempCart,
                        'coupons' => $coupons,
                        'couponsYsg' => $couponsYsg
                    ])
                </div>
                <div class="col-xl-4 ml-lg-auto">
                    @include('frontend.partials.cart_summary', [
                        'cart' => $cart,
                        'tempCart' => $tempCart,
                        'couponsYsg' => $couponsYsg
                    ])
                </div>
            </div>
        @else
            <div class="dc-header">
                <h3 class="heading heading-6 strong-700">{{ __('Your Cart is empty') }}</h3>
            </div>
        @endif
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="GuestCheckout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-zoom" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">{{ __('Login') }}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="p-3">
                    <form class="form-default" role="form" action="{{ route('cart.login.submit') }}" method="POST">
                        @csrf
                        @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                        <span>{{  __('Use country code before number') }}</span>
                        @endif
                        <div class="form-group">
                            @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                            <input type="text" class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ __('Email Or Phone') }}" name="email" id="email">
                            @else
                            <input type="email" class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  __('Email') }}" name="email">
                            @endif
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control h-auto form-control-lg" placeholder="{{ __('Password') }}">
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <a href="{{ route('password.request') }}" class="link link-xs link--style-3">{{ __('Forgot password?') }}</a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-styled btn-base-1 px-4">{{ __('Sign in') }}</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="text-center pt-3">
                    <p class="text-md">
                        {{ __('Need an account?') }}
                        <a href="{{ route('user.registration') }}" class="strong-600">{{ __('Register Now') }}</a>
                    </p>
                </div>

                @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                <div class="or or--1 my-3 text-center">
                    <span>{{ __('or') }}</span>
                </div>

                <div class="p-3 pb-0">
                    @if (\App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                    <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="btn btn-styled btn-block btn-facebook btn-icon--2 btn-icon-left px-4 mb-3">
                        <i class="icon fa fa-facebook"></i> {{ __('Login with Facebook') }}
                    </a>

                    @endif
                    @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                    <a href="{{ route('social.login', ['provider' => 'google']) }}" class="btn btn-styled btn-block btn-google btn-icon--2 btn-icon-left px-4 mb-3">
                        <i class="icon fa fa-google"></i> {{ __('Login with Google') }}
                    </a>

                    @endif
                    @if (\App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                    <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="btn btn-styled btn-block btn-twitter btn-icon--2 btn-icon-left px-4 mb-3">
                        <i class="icon fa fa-twitter"></i> {{ __('Login with Twitter') }}
                    </a>
                    @endif
                </div>

                @endif
                @if (\App\BusinessSetting::where('type', 'guest_checkout_active')->first()->value == 1)
                <div class="or or--1 mt-0 text-center">
                    <span>{{ __('or') }}</span>
                </div>

                <div class="text-center">
                    <a href="{{ route('checkout.shipping_info') }}" class="btn btn-styled btn-base-1">{{ __('Guest Checkout') }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script type="text/javascript">

    function removeFromCartView(e, key){
        e.preventDefault();
        removeFromCart(e, key);
    }

    function confirmToDetele(e, name, element) {
        const key = $(element).attr('id').replace('btn-minus-', '');
        const oldValue = $(element).attr('data-qty');
        if (oldValue == 1) {
            swal.fire({
                title: '<h5><strong> Do you want to remove this item ?</strong></h5>',
                text: `${name}`,
                showCloseButton: true,
                showCancelButton: true,
                width: 600,
                confirmButtonColor: '#ff825b',
                cancelButtonColor: '#ED4C67',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then(function(isConfirm) {
                if (isConfirm.value) {
                    removeFromCartView(e, key);
                } else {
                    $('#btn-minus-'+ key).prop('disabled', false);
                }
            });
        }
    }

    function removeCouponFormCartView(e, key) {
        e.preventDefault();
        $.post('{{ route('cart.remove_coupon_form_cart') }}', { _token: '{{ csrf_token() }}', key: key}, function(data) {
            $("#cart-summary-container").html(data.summary);
            showFrontendAlert('success', 'Coupon remove successfully');
        });
    }

    function updateQuantity(key, element){
        $.post('{{ route('cart.updateQuantity') }}', { _token:'{{ csrf_token() }}', key:key, quantity: element.value}, function(data){
            updateNavCart();
            $("#cart-details-container").html(data.details);
            $("#cart-summary-container").html(data.summary);
        });
    }

    function showCheckoutModal(){
        $('#GuestCheckout').modal();
    }

    $.post('{{ route('home.section.best_selling') }}', {_token:'{{ csrf_token() }}'}, function(data){
        $('#section_best_selling').html(data);
        slickInit();
    });

</script>
@endsection


