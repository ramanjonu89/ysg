<div class="footer-top">
    <div class="container">
        <div class="row cols-xs-space cols-sm-space cols-md-space">
            <div class="col-lg-5 col-xl-4 text-center text-md-left">
                <div class="col">
                    <p class="">{!! getMicrosite()->description !!}</p>
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
                                     src=" {{ my_asset('frontend/images/accpted_v3.jpg') }}">
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
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="col text-center text-md-left">
                    <h4 class="heading heading-xs strong-600 text-uppercase mb-2">
                        {{ __('Useful Links') }}
                    </h4>
                    <ul class="footer-links">
                        @if (!empty(getMicrosite()->terms_and_conditions))
                            <li><a href="{{ route('microsite.page', ['slug' => getMicrositeShop()->slug, 'page' => 'terms-and-conditions']) }}">{{ __('Terms and Conditions') }}</a></li>
                        @endif
                        @if (!empty(getMicrosite()->policy))
                            <li><a href="{{ route('microsite.page', ['slug' => getMicrositeShop()->slug, 'page' => 'privacy-policy']) }}">{{ __('Privacy Policy') }}</a></li>
                        @endif
                        @if (!empty(getMicrosite()->return_policy))
                            <li><a href="{{ route('microsite.page', ['slug' => getMicrositeShop()->slug, 'page' => 'return-policy']) }}">{{ __('Cancellation & Returns Policy') }}</a></li>
                        @endif
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
            </div>
        </div>
    </div>
</div>
