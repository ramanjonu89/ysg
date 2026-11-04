<div class="footer-top">
    <div class="container">
        <div class="row cols-xs-space cols-sm-space cols-md-space">
            <div class="col-lg-5 col-xl-4 text-center text-md-left">
                <div class="col">
                <!--<a href="{{ route('home') }}" class="d-block">
                    @if($generalSetting->footer_logo != null)
                    <img loading="lazy"  src="{{ my_asset($generalSetting->footer_logo) }}" alt="{{ env('APP_NAME') }}" height="44">
                    @elseif($generalSetting->logo != null)
                    <img loading="lazy"  src="{{ my_asset($generalSetting->logo) }}" alt="{{ env('APP_NAME') }}" height="44">
                    @else
                    <img loading="lazy"  src="{{ my_asset('frontend/images/logo/logo.png') }}" alt="{{ env('APP_NAME') }}" height="44">
                    @endif
                    </a>-->
                    <p class="">{{ $generalSetting->getTranslationDescriptionAttribute() }}</p>
                    {{-- <div class="d-inline-block d-md-block">
                        <p class="mb-1 text-uppercase text-bold">{{__('Join our mailing list') }}</p>
                        <form class="form-inline" method="POST" action="{{ route('subscribers.store') }}">
                            @csrf
                            <div class="form-group mb-0">
                                <input type="email" class="form-control"
                                       placeholder="{{ __('Your Email Address') }}" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-base-1 btn-icon-left">
                                {{ __('Subscribe') }}
                            </button>
                        </form>
                    </div> --}}
                </div>
            </div>
            <div class="col-lg-3 offset-xl-1 col-md-4">
                <div class="col text-center text-md-left">
                    <h4 class="heading heading-xs strong-600 text-uppercase mb-2">
                        {{ __('Multiple Payment Options') }}
                    </h4>
                    <ul class="footer-links contact-widget">
                        <li>
                            <div class="d-block" style="width: 80%">
                                <img style="width:100%"
                                     src=" {{ my_asset('assets/frontend/images/icons/cards/visa_master.png') }}">
                            </div>
                        </li>
                        @if(paymentSetting('octifi.enable'))
                        <li>
                            <div class="d-block" style="width: 80%">
                                <a href="{{ paymentSetting('octifi.web') }}" target="_blank">
                                    <img style="width:100%; margin-left: 3%"
                                     src=" {{ static_asset('frontend/images/icons/cards/Latitude-Pay_Logo_Blue_RGB.png') }}">
                                </a>
                            </div>
                        </li>
                        @endif
                        @if(paymentSetting('nets.enable'))
                        <li>
                            <div class="d-block" style="width: 80%">
                                <a href="#" target="_blank">
                                    <img style="width:100%; margin-left: 3%"
                                     src=" {{ static_asset('frontend/images/icons/cards/Nets_Payment_Logo.png') }}">
                                </a>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="col text-center text-md-left">
                    <h4 class="heading heading-xs strong-600 text-uppercase mb-2">
                        {{ __('Useful Links') }}
                    </h4>
                    <ul class="footer-links">
                        @foreach (\App\Models\Link::all() as $key => $link)
                            <li>
                                <a href="{{ $link->url }}" title="">
                                    {{ $link->getTranslationNameAttribute() }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="col text-center text-md-left">
                    <h4 class="heading heading-xs strong-600 text-uppercase mb-2">
                        {{ __('My Account') }}
                    </h4>
                    <ul class="footer-links">
                        @if (Auth::check())
                            <li>
                                <a href="{{ route('logout') }}">
                                    {{ __('Logout') }}
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('user.login') }}">
                                    {{ __('Login') }}
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('purchase_history.index') }}">
                                {{ __('Order History') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('wishlists.index') }}">
                                {{ __('My Wishlist') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('orders.track') }}">
                                {{ __('Track Order') }}
                            </a>
                        </li>
                        @if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated)
                            <li>
                                <a href="{{ route('affiliate.apply') }}">{{ __('Be an affiliate partner') }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
                @if (checkNullThenCompareValue($businessSetting, 'vendor_system_activation', 1))
                    <div class="col text-center text-md-left">
                        <div class="mt-4">
                            <h4 class="heading heading-xs strong-600 text-uppercase mb-2">
                                {{ __('Be a Seller') }}
                            </h4>
                            <a href="{{ route('be.a.seller') }}" class="btn btn-base-1 btn-icon-left">
                                {{ __('Apply Now') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="footer-bottom py-3 sct-color-3">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <ul class="my-3 my-md-0 social-nav model-2">
                    @if ($generalSetting->facebook != null)
                        <li>
                            <a href="{{ $generalSetting->facebook }}" class="facebook" target="_blank"
                               data-toggle="tooltip" data-original-title="Facebook">
                                <i class="fa fa-facebook" style="    margin-top: 10px;"></i>
                            </a>
                        </li>
                    @endif
                    @if ($generalSetting->instagram != null)
                        <li>
                            <a href="{{ $generalSetting->instagram }}" class="instagram" target="_blank"
                               data-toggle="tooltip" data-original-title="Instagram">
                                <i class="fa fa-instagram" style="    margin-top: 10px;"></i>
                            </a>
                        </li>
                    @endif
                    @if ($generalSetting->twitter != null)
                        <li>
                            <a href="{{ $generalSetting->twitter }}" class="twitter" target="_blank"
                               data-toggle="tooltip" data-original-title="Twitter">
                                <i class="fa fa-twitter" style="    margin-top: 10px;"></i>
                            </a>
                        </li>
                    @endif
                    @if ($generalSetting->youtube != null)
                        <li>
                            <a href="{{ $generalSetting->youtube }}" class="youtube" target="_blank"
                               data-toggle="tooltip" data-original-title="Youtube">
                                <i class="fa fa-youtube" style="    margin-top: 10px;"></i>
                            </a>
                        </li>
                    @endif
                    @if ($generalSetting->google_plus != null)
                        <li>
                            <a href="{{ $generalSetting->google_plus }}" class="google-plus" target="_blank"
                               data-toggle="tooltip" data-original-title="Google Plus">
                                <i class="fa fa-google-plus" style="    margin-top: 10px;"></i>
                            </a>
                        </li>
                    @endif
                    @if ($generalSetting->telegram != null)
                        <li>
                            <a href="{{ $generalSetting->telegram }}" class="telegram" target="_blank"
                               data-toggle="tooltip" data-original-title="Telegram">
                                <i class="fa fa-telegram" style="margin-top: 10px;" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="col-md-8">
                <div class="text-center text-md-right">
                    <ul class="inline-links">
                        @if (checkNullThenCompareValue($businessSetting, 'paypal_payment', 1))
                            <li>
                                <img loading="lazy" alt="paypal"
                                     src="{{ my_asset('frontend/images/icons/cards/paypal.png') }}" height="30">
                            </li>
                        @endif
                        @if (checkNullThenCompareValue($businessSetting, 'stripe_payment', 1))
                            <li>
                                <img loading="lazy" alt="stripe"
                                     src="{{ my_asset('frontend/images/stripepayment.jpg') }}" height="50">
                            </li>
                        @endif
                        @if (checkNullThenCompareValue($businessSetting, 'sslcommerz_payment', 1))
                            <li>
                                <img loading="lazy" alt="sslcommerz"
                                     src="{{ my_asset('frontend/images/icons/cards/sslcommerz-foo.png') }}"
                                     height="50">
                            </li>
                        @endif
                        @if (checkNullThenCompareValue($businessSetting, 'instamojo_payment', 1))
                            <li>
                                <img loading="lazy" alt="instamojo"
                                     src="{{ my_asset('frontend/images/icons/cards/instamojo.png') }}" height="30">
                            </li>
                        @endif
                        @if (checkNullThenCompareValue($businessSetting, 'razorpay', 1))
                            <li>
                                <img loading="lazy" alt="razorpay"
                                     src="{{ my_asset('frontend/images/icons/cards/rozarpay.png') }}" height="30">
                            </li>
                        @endif
                        @if (checkNullThenCompareValue($businessSetting, 'voguepay', 1))
                            <li>
                                <img loading="lazy" alt="voguepay"
                                     src="{{ my_asset('frontend/images/icons/cards/voguepay.png') }}" height="30">
                            </li>
                        @endif
                        @if (checkNullThenCompareValue($businessSetting, 'paystack', 1))
                            <li>
                                <img loading="lazy" alt="paystack"
                                     src="{{ my_asset('frontend/images/icons/cards/paystack.png') }}" height="30">
                            </li>
                        @endif
                        @if (checkNullThenCompareValue($businessSetting, 'payhere', 1))
                            <li>
                                <img loading="lazy" alt="payhere"
                                     src="{{ my_asset('frontend/images/icons/cards/payhere.png') }}" height="30">
                            </li>
                        @endif
                        @if (checkNullThenCompareValue($businessSetting, 'cash_payment', 1))
                            <li>
                                <img loading="lazy" alt="cash on delivery"
                                     src="{{ my_asset('frontend/images/icons/cards/cod.png') }}" height="30">
                            </li>
                        @endif
                        @if (\App\Addon::where('unique_identifier', 'offline_payment')->first() != null && \App\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                            @foreach(\App\ManualPaymentMethod::all() as $method)
                                <li>
                                    <img loading="lazy" alt="{{ $method->heading }}"
                                         src="{{ my_asset($method->photo)}}" height="30">
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-md-12">
                <div class="copyright text-center text-md-right">
                    <ul class="copy-links no-margin">
                        <li>
                            © {{ date('Y') }} {{ $generalSetting->site_name }}
                        </li>
                        <li>
                            <a href="/termsofuse">{{ __('Terms of Use') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('privacypolicy') }}">{{ __('Privacy Policy') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
