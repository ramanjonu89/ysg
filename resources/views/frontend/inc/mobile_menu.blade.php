<!-- mobile menu -->
<div class="mobile-side-menu d-lg-none">
    <div class="side-menu-overlay opacity-0" onclick="sideMenuClose()"></div>
    <div class="side-menu-wrap opacity-0">
        <div class="side-menu closed">
            <div class="side-menu-header ">
                <div class="side-menu-close" onclick="sideMenuClose()">
                    <i class="la la-close"></i>
                </div>

                @auth
                    <div class="widget-profile-box px-3 py-4 d-flex align-items-center">
                        @if (auth()->user()->avatar_original != null)
                            <div class="image "
                                 style="background-image:url('{{ my_asset(auth()->user()->avatar_original) }}')"></div>
                        @else
                            <div class="image "
                                 style="background-image:url('{{ my_asset('frontend/images/user.png') }}')"></div>
                        @endif

                        <div class="name">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="side-login px-3 pb-3">
                        <a href="{{ route('logout') }}" id="user-logout-mobile">{{ __('Sign Out') }}</a>
                    </div>
                @else
                    <div class="widget-profile-box px-3 py-4 d-flex align-items-center">
                        <div class="image "
                             style="background-image:url('{{ my_asset('frontend/images/icons/user-placeholder.jpg') }}')"></div>
                    </div>
                    <div class="side-login px-3 pb-3">
                        <a href="{{ route('user.login') }}">{{ __('Sign In') }}</a>
                        <a href="{{ route('user.registration') }}">{{ __('Registration') }}</a>
                    </div>
                @endauth
            </div>
            <div class="side-menu-list px-3">
                <ul class="side-user-menu">
                    <li>
                        <a href="{{ getAppHomepageLink() }}">
                            <i class="la la-home"></i>
                            <span>{{ __('Home') }}</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('dashboard') }}">
                            <i class="la la-dashboard"></i>
                            <span>{{ __('Dashboard') }}</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('purchase_history.index') }}">
                            <i class="la la-file-text"></i>
                            <span>{{ __('Purchase History') }}</span>
                        </a>
                    </li>
                    @auth
                        @php
                            $conversation = \App\Conversation::where('sender_id', auth()->user()->id)->where('sender_viewed', '1')->get();
                        @endphp
                        @if (checkNullThenCompareValue($businessSetting, 'conversation_system', 1))
                            <li>
                                <a href="{{ route('conversations.index') }}"
                                   class="{{ areActiveRoutesHome(['conversations.index', 'conversations.show'])}}">
                                    <i class="la la-comment"></i>
                                    <span class="category-name">
                                            {{ __('Conversations') }}
                                        @if (count($conversation) > 0)
                                            <span class="ml-2"
                                                  style="color:green"><strong>({{ count($conversation) }})</strong></span>
                                        @endif
                                        </span>
                                </a>
                            </li>
                        @endif
                    @endauth

                    <li>

                        <a href="{{ route('cart') }}">
                            <i class="la la-shopping-cart"></i>
                            <span>{{ __('Cart') }}</span>
                            @if(Session::has('cart'))
                                @php
                                    $count = 0 ;
                                    foreach (Session::get('cart') as $key => $cartItem){
                                        $count +=  $cartItem['quantity'];
                                    }
                                @endphp
                                <span class="badge" id="cart_items_sidenav">
                                    {{ $count}}</span>
                            @else
                                <span class="badge" id="cart_items_sidenav">0</span>
                            @endif
                        </a>

                    </li>
                    <li>
                        <a href="{{ route('wishlists.index') }}">
                            <i class="la la-heart-o"></i>
                            <span>{{ __('Wishlist') }}</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('categories.all') }}">
                            <i class="la la-headphones"></i>
                            <span>{{ __('Categories') }}</span>
                        </a>
                    </li>

                    @if(checkNullThenCompareValue($businessSetting, 'classified_product', 1))
                        <li>
                            <a href="{{ route('customer_products.index') }}">
                                <i class="la la-diamond"></i>
                                <span>{{ __('Classified Products') }}</span>
                            </a>
                        </li>
                    @endif

                    @if (checkNullThenCompareValue($businessSetting, 'wallet_system', 1))
                        <li>
                            <a href="{{ route('wallet.index') }}">
                                <i class="la la-dollar"></i>
                                <span>{{ __('My Wallet') }}</span>
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ route('profile') }}">
                            <i class="la la-user"></i>
                            <span>{{ __('Manage Profile') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('referral.access') }}">
                            <i class="la la-user"></i>
                            <span>{{ __('Corporate Access Pass') }}</span>
                        </a>
                    </li>

                    @php
                        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
                        $club_point_addon = \App\Addon::where('unique_identifier', 'club_point')->first();
                    @endphp
                    @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                        <li>
                            <a href="{{ route('customer_refund_request') }}"
                               class="{{ areActiveRoutesHome(['customer_refund_request'])}}">
                                <i class="la la-file-text"></i>
                                <span class="category-name">
                                        {{ __('Sent Refund Request') }}
                                    </span>
                            </a>
                        </li>
                    @endif

                    @if ($club_point_addon != null && $club_point_addon->activated == 1)
                        <li>
                            <a href="{{ route('earnng_point_for_user') }}"
                               class="{{ areActiveRoutesHome(['earnng_point_for_user'])}}">
                                <i class="la la-dollar"></i>
                                <span class="category-name">
                                        {{ __('Earning Points') }}
                                    </span>
                            </a>
                        </li>
                    @endif
                </ul>

                @if (auth()->check() && auth()->user()->user_type == 'seller')
                    <div class="sidebar-widget-title py-0">
                        <span>{{ __('Shop Options') }}</span>
                    </div>
                    <ul class="side-seller-menu">
                        <li>
                            <a href="{{ route('seller.products') }}">
                                <i class="la la-diamond"></i>
                                <span>{{ __('Products') }}</span>
                            </a>
                        </li>

                        @if (\App\Addon::where('unique_identifier', 'pos_system')->first() != null && \App\Addon::where('unique_identifier', 'pos_system')->first()->activated)
                            <li>
                                <a href="{{route('poin-of-sales.seller_index') }}">
                                    <i class="la la-fax"></i>
                                    <span>
                                            {{ __('POS Manager') }}
                                        </span>
                                </a>
                            </li>
                        @endif

                        <li>
                            <a href="{{ route('orders.index') }}">
                                <i class="la la-file-text"></i>
                                <span>{{ __('Orders') }}</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('shops.index') }}">
                                <i class="la la-cog"></i>
                                <span>{{ __('Shop Setting') }}</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('withdraw_requests.index') }}">
                                <i class="la la-money"></i>
                                <span>
                                                                    {{ __('Money Withdraw') }}
                                                                </span>
                            </a>
                        </li>

                        @php
                            $conversation = \App\Conversation::where('receiver_id', auth()->user()->id)->where('receiver_viewed', '1')->get();
                        @endphp
                        @if (checkNullThenCompareValue($businessSetting, 'conversation_system', 1))
                            <li>
                                <a href="{{ route('conversations.index') }}"
                                   class="{{ areActiveRoutesHome(['conversations.index', 'conversations.show'])}}">
                                    <i class="la la-comment"></i>
                                    <span class="category-name">
                                            {{ __('Conversations') }}
                                        @if (count($conversation) > 0)
                                            <span class="ml-2"
                                                  style="color:green"><strong>({{ count($conversation) }})</strong></span>
                                        @endif
                                        </span>
                                </a>
                            </li>
                        @endif

                        <li>
                            <a href="{{ route('payments.index') }}">
                                <i class="la la-cc-mastercard"></i>
                                <span>{{ __('Payment History') }}</span>
                            </a>
                        </li>
                    </ul>
                    <div class="sidebar-widget-title py-0">
                        <span>{{ __('Earnings') }}</span>
                    </div>
                    <div class="widget-balance py-3">
                        <div class="text-center">
                            <div class="heading-4 strong-700 mb-4">
                                <small
                                    class="d-block text-sm alpha-5 mb-2">{{ __('Your earnings (current month)') }}</small>
                                <span class="p-2 bg-base-1 rounded">{{ single_price($vc_revenueThisMonth) }}</span>
                            </div>
                            <table class="text-left mb-0 table w-75 m-auto">
                                <tbody>
                                <tr>
                                    <td class="p-1 text-sm">
                                        {{ __('Total earnings') }}:
                                    </td>
                                    <td class="p-1">
                                        {{ single_price($vc_revenue) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-1 text-sm">
                                        {{ __('Last Month earnings') }}:
                                    </td>
                                    <td class="p-1">
                                        {{ single_price($vc_revenueLastMonth) }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="widget-balance py-3">
                    <div class="heading-5 strong-700 mb-0">
                        <span class="text-uppercase">{{ __('Useful Links') }}</span>
                    </div>
                    <div class="text-left mb-0 m-auto">
                        <ul class="">
                            @foreach (\App\Models\Link::all() as $key => $link)
                                <li class="text-sm">
                                    <a href="{{ $link->url }}">
                                        <i class=""></i>
                                        <span>{{ $link->getTranslationNameAttribute() }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end mobile menu -->
