<div class="sidebar sidebar--style-3 no-border stickyfill p-0">

    <div class="widget mb-0">

        <div class="widget-profile-box text-center p-3">

            @if (Auth::user()->avatar_original != null)

                <div class="image" style="background-image:url('{{ my_asset(Auth::user()->avatar_original) }}')"></div>

            @else

                <img src="{{ my_asset('frontend/images/user.png') }}" class="image rounded-circle">

            @endif

            @if(Auth::user()->seller->verification_status == 1)

                <div class="name mb-0">{{ Auth::user()->name }} <span class="ml-2"><i class="fa fa-check-circle" style="color:green"></i></span></div>

            @else

                <div class="name mb-0">{{ Auth::user()->name }} <span class="ml-2"><i class="fa fa-times-circle" style="color:red"></i></span></div>

            @endif

        </div>

        <div class="sidebar-widget-title py-3">

            <span>{{ __('Menu') }}</span>

        </div>

        <div class="widget-profile-menu py-3">

            <ul class="categories categories--style-3">

                <li>

                    <a href="{{ route('dashboard') }}" class="{{ areActiveRoutesHome(['dashboard'])}}">

                        <i class="la la-dashboard"></i>

                        <span class="category-name">

                            {{ __('Dashboard') }}

                        </span>

                    </a>

                </li>

               

               
               <!-- <li>

                    <a href="{{ route('digital_purchase_history.index') }}" class="{{ areActiveRoutesHome(['digital_purchase_history.index'])}}">

                        <i class="la la-download"></i>

                        <span class="category-name">

                            {{ __('Downloads') }}

                        </span>

                    </a>

                </li> -->

                <li>

                    <a href="{{ route('wishlists.index') }}" class="{{ areActiveRoutesHome(['wishlists.index'])}}">

                        <i class="la la-heart-o"></i>

                        <span class="category-name">

                            {{ __('Wishlist') }}

                        </span>

                    </a>

                </li>

                <li>

                    <a href="{{ route('seller.products') }}" class="{{ areActiveRoutesHome(['seller.products', 'seller.products.upload', 'seller.products.edit'])}}">

                        <i class="la la-diamond"></i>

                        <span class="category-name">

                            {{ __('Products') }}

                        </span>

                    </a>

                </li>

               
                <li>

                    <a href="{{ route('orders.index') }}" class="{{ areActiveRoutesHome(['orders.index'])}}">

                        <i class="la la-file-text"></i>

                        <span class="category-name">

                            {{ __('Orders') }}

                        </span>

                    </a>

                </li>




                @php

                    $review_count = DB::table('reviews')

                                ->orderBy('code', 'desc')

                                ->join('products', 'products.id', '=', 'reviews.product_id')

                                ->where('products.user_id', Auth::user()->id)

                                ->where('reviews.viewed', 0)

                                ->select('reviews.id')

                                ->distinct()

                                ->count();

                @endphp

                <li>

                    <a href="{{ route('reviews.seller') }}" class="{{ areActiveRoutesHome(['reviews.seller'])}}">

                        <i class="la la-star-o"></i>

                        <span class="category-name">

                            {{ __('Product Reviews') }}@if($review_count > 0)<span class="ml-2" style="color:green"><strong>
                                ({{ $review_count }} {{ __('New') }})</strong></span>@endif

                        </span>

                    </a>

                </li>

                @if (\App\BusinessSetting::where('type', 'conversation_system')->first()->value == 1)

                    @php

                        $conversation_sent = \App\Conversation::where('sender_id', Auth::user()->id)->where('sender_viewed', 0)->get();

                        $conversation_recieved = \App\Conversation::where('receiver_id', Auth::user()->id)->where('receiver_viewed', 0)->get();

                    @endphp

                    <li>

                        <a href="{{ route('conversations.index') }}" class="{{ areActiveRoutesHome(['conversations.index', 'conversations.show'])}}">

                            <i class="la la-comment"></i>

                            <span class="category-name">

                                {{ __('Conversations') }}

                                @if (count($conversation_sent)+count($conversation_recieved) > 0)

                                    <span class="ml-2" style="color:green"><strong>({{ count($conversation_sent)+count($conversation_recieved) }})</strong></span>

                                @endif

                            </span>

                        </a>

                    </li>

                @endif

                <li>

                    <a href="{{ route('shops.index') }}" class="{{ areActiveRoutesHome(['shops.index'])}}">

                        <i class="la la-cog"></i>

                        <span class="category-name">

                            {{ __('Shop Setting') }}

                        </span>

                    </a>

                </li>

          

                <li>

                    <a href="{{ route('profile') }}" class="{{ areActiveRoutesHome(['profile'])}}">

                        <i class="la la-user"></i>

                        <span class="category-name">

                            {{ __('Manage Profile') }}

                        </span>

                    </a>

                </li>


                @if (\App\BusinessSetting::where('type', 'wallet_system')->first()->value == 1)

                    <li>

                        <a href="{{ route('wallet.index') }}" class="{{ areActiveRoutesHome(['wallet.index'])}}">

                            <i class="la la-dollar"></i>

                            <span class="category-name">

                                {{ __('My Wallet') }}

                            </span>

                        </a>

                    </li>

                @endif

                @if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated && Auth::user()->affiliate_user != null && Auth::user()->affiliate_user->status)

                    <li>

                        <a href="{{ route('affiliate.user.index') }}" class="{{ areActiveRoutesHome(['affiliate.user.index', 'affiliate.payment_settings'])}}">

                            <i class="la la-dollar"></i>

                            <span class="category-name">

                                {{ __('Affiliate System') }}

                            </span>

                        </a>

                    </li>

                @endif

                

               

            </ul>

        </div>



       

    </div>

</div>

