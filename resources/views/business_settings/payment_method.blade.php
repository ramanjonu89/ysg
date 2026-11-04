@extends('layouts.app')
@section('content')
    <div class="row">

        @if(paymentSetting('octifi.enable')??0)
            <div class="col-lg-6">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">{{ __('LatitudePay Credential') }}</h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Public key') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="octifi.publicKey" value="{{ paymentSetting('octifi.publicKey')??'' }}" placeholder="{{ __('Octifi public key') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Private key') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="octifi.privateKey" value="{{ paymentSetting('octifi.privateKey')??'' }}" placeholder="{{ __('Octifi private key') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Web url') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="octifi.web" value="{{ paymentSetting('octifi.web')??'' }}" placeholder="{{ __('Octifi web url') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('API url') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="octifi.api" value="{{ paymentSetting('octifi.api')??'' }}" placeholder="{{ __('Octifi api url') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Minimum order total') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="octifi.minimumOrderTotal" value="{{ paymentSetting('octifi.minimumOrderTotal')??'' }}" placeholder="{{ __('Minimum SGD for allow Octifi payment') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Product detail text') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="octifi.productDetailText" value="{{ paymentSetting('octifi.productDetailText')??'' }}" placeholder="{{ __('Display text at product detail page') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Cart summary text') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="octifi.cartSummaryText" value="{{ paymentSetting('octifi.cartSummaryText')??'' }}" placeholder="{{ __('Display text at product detail page') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-12 text-right">
                                    <button class="btn btn-purple" type="submit">{{__('Save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if(paymentSetting('cybersource.enable')??0)
            <div class="col-lg-6">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">{{ __('Cybersource Credential') }}</h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Aggregator Id') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="cybersource.aggregatorId" value="{{ paymentSetting('cybersource.aggregatorId')??'' }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Submerchant Id') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="cybersource.submerchantId" value="{{ paymentSetting('cybersource.submerchantId')??'' }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Profile Id') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="cybersource.profileId" value="{{ paymentSetting('cybersource.profileId')??'' }}" placeholder="{{ __('Cybersource Profile Id') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Access key') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="cybersource.accessKey" value="{{ paymentSetting('cybersource.accessKey')??'' }}" placeholder="{{ __('Cybersource Access key') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Secret Key') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="cybersource.secretKey" value="{{ paymentSetting('cybersource.secretKey')??'' }}" placeholder="{{ __('Cybersource Secret key') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Web url') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="cybersource.web" value="{{ paymentSetting('cybersource.web')??'' }}" placeholder="{{ __('Cybersource web url') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('API url') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="cybersource.api" value="{{ paymentSetting('cybersource.api')??'' }}" placeholder="{{ __('Cybersource api url') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-12 text-right">
                                    <button class="btn btn-purple" type="submit">{{__('Save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if(paymentSetting('nets.enable')??0)
            <div class="col-lg-6">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">{{ __('NETS Credential') }}</h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('UMID') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="nets.umid" value="{{ paymentSetting('nets.umid')??'' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Merchant Id') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="nets.mid" value="{{ paymentSetting('nets.mid')??'' }}" placeholder="{{ __('Merchant Id') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('tid') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="nets.tid" value="{{ paymentSetting('nets.tid')??'' }}" placeholder="{{ __('Maybe need this?') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('API Key') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="nets.apiKey" value="{{ paymentSetting('nets.apiKey')??'' }}" placeholder="{{ __('API Key') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('API Secret') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="nets.apiSecret" value="{{ paymentSetting('nets.apiSecret')??'' }}" placeholder="{{ __('API Secret') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3">
                                    <label class="control-label">{{__('Gateway url') }}</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" name="nets.url" value="{{ paymentSetting('nets.url')??'' }}" placeholder="{{ __('NET gateway url') }}" required>
                                    <small>{{ paymentSetting('nets.url')?(paymentSetting('nets.url').'GW2/TxnReqListenerToHost'):'' }}</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-12 text-right">
                                    <button class="btn btn-purple" type="submit">{{__('Save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if(0)
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Paypal Credential') }}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        <input type="hidden" name="payment_method" value="paypal">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="PAYPAL_CLIENT_ID">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('Paypal Client Id') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="PAYPAL_CLIENT_ID" value="{{  env('PAYPAL_CLIENT_ID') }}" placeholder="{{ __('Paypal Client ID') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="PAYPAL_CLIENT_SECRET">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('Paypal Client Secret') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="PAYPAL_CLIENT_SECRET" value="{{  env('PAYPAL_CLIENT_SECRET') }}" placeholder="{{ __('Paypal Client Secret') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('Paypal Sandbox Mode') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <label class="switch">
                                    <input value="1" name="paypal_sandbox" type="checkbox" @if (\App\BusinessSetting::where('type', 'paypal_sandbox')->first()->value == 1)
                                    checked
                                        @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!--
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Sslcommerz Credential') }}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="sslcommerz">
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="SSLCZ_STORE_ID">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('Sslcz Store Id') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="SSLCZ_STORE_ID" value="{{  env('SSLCZ_STORE_ID') }}" placeholder="{{ __('Sslcz Store Id') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="SSLCZ_STORE_PASSWD">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('Sslcz store password') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="SSLCZ_STORE_PASSWD" value="{{  env('SSLCZ_STORE_PASSWD') }}" placeholder="{{ __('Sslcz store password') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('Sslcommerz Sandbox Mode') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <label class="switch">
                                    <input value="1" name="sslcommerz_sandbox" type="checkbox" @if (\App\BusinessSetting::where('type', 'sslcommerz_sandbox')->first()->value == 1)
                                    checked
                                        @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        -->

        @if(0)
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Stripe Credential') }}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="stripe">
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="STRIPE_KEY">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('Stripe Key') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="STRIPE_KEY" value="{{  env('STRIPE_KEY') }}" placeholder="{{ __('STRIPE KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="STRIPE_SECRET">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('Stripe Secret') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="STRIPE_SECRET" value="{{  env('STRIPE_SECRET') }}" placeholder="{{ __('STRIPE SECRET') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

    <!--
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('RazorPay Credential') }}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="razorpay">
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="RAZOR_KEY">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('RAZOR KEY') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="RAZOR_KEY" value="{{  env('RAZOR_KEY') }}" placeholder="{{ __('RAZOR KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="RAZOR_SECRET">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('RAZOR SECRET') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="RAZOR_SECRET" value="{{  env('RAZOR_SECRET') }}" placeholder="{{ __('RAZOR SECRET') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        -->

    <!--
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Instamojo Credential') }}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="instamojo">
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="IM_API_KEY">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('API KEY') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="IM_API_KEY" value="{{  env('IM_API_KEY') }}" placeholder="{{ __('IM API KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="IM_AUTH_TOKEN">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('AUTH TOKEN') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="IM_AUTH_TOKEN" value="{{  env('IM_AUTH_TOKEN') }}" placeholder="{{ __('IM AUTH TOKEN') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('Instamojo Sandbox Mode') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <label class="switch">
                                    <input value="1" name="instamojo_sandbox" type="checkbox" @if (\App\BusinessSetting::where('type', 'instamojo_sandbox')->first()->value == 1)
                                    checked
                                        @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        -->

    <!--
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('PayStack Credential') }}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="paystack">
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="PAYSTACK_PUBLIC_KEY">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('PUBLIC KEY') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="PAYSTACK_PUBLIC_KEY" value="{{  env('PAYSTACK_PUBLIC_KEY') }}" placeholder="{{ __('PUBLIC KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="PAYSTACK_SECRET_KEY">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('SECRET KEY') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="PAYSTACK_SECRET_KEY" value="{{  env('PAYSTACK_SECRET_KEY') }}" placeholder="{{ __('SECRET KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="MERCHANT_EMAIL">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('MERCHANT EMAIL') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="MERCHANT_EMAIL" value="{{  env('MERCHANT_EMAIL') }}" placeholder="{{ __('MERCHANT EMAIL') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        -->

    <!--
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('VoguePay Credential') }}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="voguepay">
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="VOGUE_MERCHANT_ID">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('MERCHANT ID') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="VOGUE_MERCHANT_ID" value="{{  env('VOGUE_MERCHANT_ID') }}" placeholder="{{ __('MERCHANT ID') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('Sandbox Mode') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <label class="switch">
                                    <input value="1" name="voguepay_sandbox" type="checkbox" @if (\App\BusinessSetting::where('type', 'voguepay_sandbox')->first()->value == 1)
                                    checked
                                        @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        -->

    <!--
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __('Payhere Credential') }}</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="payhere">
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="PAYHERE_MERCHANT_ID">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('PAYHERE MERCHANT ID') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="PAYHERE_MERCHANT_ID" value="{{  env('PAYHERE_MERCHANT_ID') }}" placeholder="{{ __('PAYHERE MERCHANT ID') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="PAYHERE_SECRET">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('PAYHERE SECRET') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="PAYHERE_SECRET" value="{{  env('PAYHERE_SECRET') }}" placeholder="{{ __('PAYHERE SECRET') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="types[]" value="PAYHERE_CURRENCY">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('PAYHERE CURRENCY') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="PAYHERE_CURRENCY" value="{{  env('PAYHERE_CURRENCY') }}" placeholder="{{ __('PAYHERE CURRENCY') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-3">
                                <label class="control-label">{{ __('Payhere Sandbox Mode') }}</label>
                            </div>
                            <div class="col-lg-6">
                                <label class="switch">
                                    <input value="1" name="payhere_sandbox" type="checkbox" @if (\App\BusinessSetting::where('type', 'payhere_sandbox')->first()->value == 1)
                                    checked
                                        @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        -->

    </div>
@endsection
