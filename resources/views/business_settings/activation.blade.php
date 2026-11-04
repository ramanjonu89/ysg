@extends('layouts.app')
@section('content')
    <!--
    <h3 class="text-center">{{ __('HTTPS Activation') }}</h3>
    <div class="row">
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('HTTPS Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'FORCE_HTTPS')" <?php if (env('FORCE_HTTPS') == 'On') echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    -->

    <h3 class="text-center">{{ __('Maintenance Mode') }}</h3>
    <div class="row">
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Maintenance Mode Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'maintenance_mode')" <?php if (\App\BusinessSetting::where('type',
                                'maintenance_mode')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!--
    <div class="row">
        <h3 class="text-center">{{ __('Classified Product Activate') }}</h3>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Classified Product') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'classified_product')" <?php if (\App\BusinessSetting::where('type',
                                'classified_product')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    -->

    <!--
    <h3 class="text-center">{{ __('Business Related') }}</h3>
    <div class="row">
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Vendor System Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'vendor_system_activation')" <?php if (\App\BusinessSetting::where('type',
                                'vendor_system_activation')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Wallet System Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'wallet_system')" <?php if (\App\BusinessSetting::where('type',
                                'wallet_system')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Coupon System Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'coupon_system')" <?php if (\App\BusinessSetting::where('type',
                                'coupon_system')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Pickup Point Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'pickup_point')" <?php if (\App\BusinessSetting::where('type',
                                'pickup_point')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Conversation Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'conversation_system')" <?php if (\App\BusinessSetting::where('type',
                                'conversation_system')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Guest Checkout Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'guest_checkout_active')" <?php if (\App\BusinessSetting::where('type',
                                'guest_checkout_active')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Category-based Commission') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'category_wise_commission')" <?php if (\App\BusinessSetting::where('type',
                                'category_wise_commission')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                    <div class="alert"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('After activate this option Seller commision will be disabled and You need to set commission on each category otherwise Admin will not get any commision') }}
                        . <a href="{{ route('categories.index') }}">{{ __('Set Commisssion Now') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Email Verification') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'email_verification')" <?php if (\App\BusinessSetting::where('type',
                                'email_verification')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                    <div class="alert"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        You need to configure SMTP correctly to enable this feature. <a
                            href="{{ route('smtp_settings.index') }}">Configure Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    -->

    <h3 class="text-center">{{ __('Payment Related') }}</h3>
    <div class="row">

        {{--Octifi payment--}}
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading text-center bord-btm">
                    <h3 class="panel-title">{{ __('LatitudePay Payment Activation') }}</h3>
                </div>
                <div class="panel-body">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ static_asset('frontend/images/icons/cards/Latitude-Pay_Logo_Blue_RGB.png') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="toggle_active('{{\App\Models\Order::PAYMENT_TYPE_OCTIFI}}')" <?= paymentSetting('octifi.enable')?'checked':'' ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="alert text-center"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('You need to configure LatitudePay correctly to enable this feature') }}.
                        <br/>
                        <a href="{{ route('payment_method.index') }}">{{ __('Configure Now') }}</a>
                    </div>
                </div>
            </div>
        </div>

        {{--Cybersource payment--}}
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading text-center bord-btm">
                    <h3 class="panel-title">{{ __('Cybersource Payment Activation') }}</h3>
                </div>
                <div class="panel-body">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ my_asset('assets/frontend/images/icons/cards/cybersource_logo.svg') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="toggle_active('{{\App\Models\Order::PAYMENT_TYPE_CYBERSOURCE}}')" <?= paymentSetting('cybersource.enable')?'checked':'' ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="alert text-center"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('You need to configure Cybersource correctly to enable this feature') }}.
                        <br/>
                        <a href="{{ route('payment_method.index') }}">{{ __('Configure Now') }}</a>
                    </div>
                </div>
            </div>
        </div>

        {{--eNET payment--}}
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading text-center bord-btm">
                    <h3 class="panel-title">{{ __('NETS Payment Activation') }}</h3>
                </div>
                <div class="panel-body">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ my_asset('assets/frontend/images/icons/cards/nets_logo.png') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="toggle_active('{{\App\Models\Order::PAYMENT_TYPE_NETS}}')" <?= paymentSetting('nets.enable')?'checked':'' ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="alert text-center"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('You need to configure NETS correctly to enable this feature') }}.
                        <br/>
                        <a href="{{ route('payment_method.index') }}">{{ __('Configure Now') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading text-center bord-btm">
                    <h3 class="panel-title">{{ __('Paypal Payment Activation') }}</h3>
                </div>
                <div class="panel-body">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ my_asset('frontend/images/icons/cards/paypal.png') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="updateSettings(this, 'paypal_payment')" <?php if (\App\BusinessSetting::where('type',
                                    'paypal_payment')->first()->value == 1) echo "checked";?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="alert text-center"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('You need to configure Paypal correctly to enable this feature') }}. <a
                            href="{{ route('payment_method.index') }}">{{ __('Configure Now') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading text-center bord-btm">
                    <h3 class="panel-title text-center">{{ __('Stripe Payment Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ my_asset('frontend/images/icons/cards/stripe.png') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="updateSettings(this, 'stripe_payment')" <?php if (\App\BusinessSetting::where('type',
                                    'stripe_payment')->first()->value == 1) echo "checked";?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="alert"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        You need to configure Stripe correctly to enable this feature. <a
                            href="{{ route('payment_method.index') }}">Configure Now</a>
                    </div>
                </div>
            </div>
        </div>

        <!--
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('SSlCommerz Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ my_asset('frontend/images/icons/cards/sslcommerz.png') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="updateSettings(this, 'sslcommerz_payment')" <?php if (\App\BusinessSetting::where('type',
                                    'sslcommerz_payment')->first()->value == 1) echo "checked";?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="alert"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        You need to configure SSlCommerz correctly to enable this feature. <a
                            href="{{ route('payment_method.index') }}">Configure Now</a>
                    </div>
                </div>
            </div>
        </div>
        -->

        <!--
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Instamojo Payment Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ my_asset('frontend/images/icons/cards/instamojo.png') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="updateSettings(this, 'instamojo_payment')" <?php if (\App\BusinessSetting::where('type',
                                    'instamojo_payment')->first()->value == 1) echo "checked";?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="alert"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('You need to configure Instamojo Payment correctly to enable this feature') }}. <a
                            href="{{ route('payment_method.index') }}">{{ __('Configure Now') }}</a>
                    </div>
                </div>
            </div>
        </div>
        -->

        <!--
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Razor Pay Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ my_asset('frontend/images/icons/cards/rozarpay.png') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="updateSettings(this, 'razorpay')" <?php if (\App\BusinessSetting::where('type',
                                    'razorpay')->first()->value == 1) echo "checked";?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="alert"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('You need to configure Razor correctly to enable this feature') }}. <a
                            href="{{ route('payment_method.index') }}">{{ __('Configure Now') }}</a>
                    </div>
                </div>
            </div>
        </div>
        -->

        <!--
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('PayStack Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ my_asset('frontend/images/icons/cards/paystack.png') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="updateSettings(this, 'paystack')" <?php if (\App\BusinessSetting::where('type',
                                    'paystack')->first()->value == 1) echo "checked";?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="alert"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('You need to configure PayStack correctly to enable this feature')  }}. <a
                            href="{{ route('payment_method.index') }}">{{ __('Configure Now') }}</a>
                    </div>
                </div>
            </div>
        </div>
        -->

        <!--
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('VoguePay Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ my_asset('frontend/images/icons/cards/vogue.png') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="updateSettings(this, 'voguepay')" <?php if (\App\BusinessSetting::where('type',
                                    'voguepay')->first()->value == 1) echo "checked";?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="alert"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('You need to configure VoguePay correctly to enable this feature') }}. <a
                            href="{{ route('payment_method.index') }}">{{ __('Configure Now') }}</a>
                    </div>
                </div>
            </div>
        </div>
        -->

        <!--
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Payhere Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ my_asset('frontend/images/icons/cards/payhere.png') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="updateSettings(this, 'payhere')" <?php if (\App\BusinessSetting::where('type',
                                    'payhere')->first()->value == 1) echo "checked";?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="alert"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('You need to configure VoguePay correctly to enable this feature') }}. <a
                            href="{{ route('payment_method.index') }}">{{ __('Configure Now') }}</a>
                    </div>
                </div>
            </div>
        </div>
        -->

        @if(\App\Addon::where('unique_identifier', 'african_pg')->first() != null && \App\Addon::where('unique_identifier', 'african_pg')->first()->activated)
            <div class="col-lg-4">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">{{ __('MPesa Activation') }}</h3>
                    </div>
                    <div class="panel-body text-center">
                        <div class="clearfix">
                            <img loading="lazy" class="pull-left"
                                 src="{{ my_asset('frontend/images/icons/cards/mpesa.png') }}" height="30">
                            <label class="switch pull-right">
                                <input type="checkbox"
                                       onchange="updateSettings(this, 'mpesa')" <?php if (\App\BusinessSetting::where('type',
                                        'mpesa')->first()->value == 1) echo "checked";?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="alert"
                             style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                            {{ __('You need to configure Mpesa correctly to enable this feature') }}. <a
                                href="{{ route('payment_method.index') }}">{{ __('Configure Now') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">{{ __('flutterwave Activation') }}</h3>
                    </div>
                    <div class="panel-body text-center">
                        <div class="clearfix">
                            <img loading="lazy" class="pull-left"
                                 src="{{ my_asset('frontend/images/icons/cards/flutterwave.png') }}" height="30">
                            <label class="switch pull-right">
                                <input type="checkbox"
                                       onchange="updateSettings(this, 'flutterwave')" <?php if (\App\BusinessSetting::where('type',
                                        'flutterwave')->first()->value == 1) echo "checked";?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="alert"
                             style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                            {{ __('You need to configure flutterwave correctly to enable this feature') }}. <a
                                href="{{ route('payment_method.index') }}">{{ __('Configure Now') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading text-center bord-btm">
                    <h3 class="panel-title text-center">{{ __('Cash Payment Activation') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <div class="clearfix">
                        <img loading="lazy" class="pull-left"
                             src="{{ my_asset('frontend/images/icons/cards/cod.png') }}" height="30">
                        <label class="switch pull-right">
                            <input type="checkbox"
                                   onchange="updateSettings(this, 'cash_payment')" <?php if (\App\BusinessSetting::where('type',
                                    'cash_payment')->first()->value == 1) echo "checked";?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <h3 class="text-center">{{ __('Social Media Login') }}</h3>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Facebook login') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'facebook_login')" <?php if (\App\BusinessSetting::where('type',
                                'facebook_login')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                    <div class="alert"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('You need to configure Facebook Client correctly to enable this feature') }}.
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Google login') }}</h3>
                </div>
                <div class="panel-body text-center">
                    <label class="switch">
                        <input type="checkbox"
                               onchange="updateSettings(this, 'google_login')" <?php if (\App\BusinessSetting::where('type',
                                'google_login')->first()->value == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                    <div class="alert"
                         style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                        {{ __('You need to configure Google Client correctly to enable this feature') }}.
                    </div>
                </div>
            </div>
        </div>
    <!-- <div class="col-lg-4">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title text-center">{{ __('Twitter login') }}</h3>
            </div>
            <div class="panel-body text-center">
                <label class="switch">
                    <input type="checkbox" onchange="updateSettings(this, 'twitter_login')" <?php if (\App\BusinessSetting::where('type',
            'twitter_login')->first()->value == 1) echo "checked";?>>
                    <span class="slider round"></span>
                </label>
                <div class="alert" style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                    {{ __('You need to configure Twitter Client correctly to enable this feature') }}.
                    <a href="{{ route('social_login.index') }}">{{ __('Configure Now') }}</a>
                </div>
            </div>
        </div>
    </div> -->
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function updateSettings(el, type) {
            if ($(el).is(':checked')) {
                var value = 1;
            } else {
                var value = 0;
            }
            $.post('{{ route('business_settings.update.activation') }}', {
                _token: '{{ csrf_token() }}',
                type: type,
                value: value
            }, function (data) {
                if (parseInt(data) === 1) {
                    showAlert('success', 'Settings updated successfully');
                } else {
                    showAlert('danger', 'Something went wrong');
                }
            });
        }

        function toggle_active(method){
            $.post('{{ route('admin.api.payment_setting.toggle_active') }}', {_token:'{{ csrf_token() }}', method: method}, function(data){
                if(data.code == 200){
                    showAlert('success', '{{ __('Toggle Display Successfully') }}');
                }
                else{
                    showAlert('danger', '{{ __('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
