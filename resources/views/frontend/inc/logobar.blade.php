@php $total=0; @endphp
<div class="sticky-top-item logo-bar-area">
    <div class="">
        <div class="container">
            <div class="row no-gutters align-items-center">
                <div class="col-lg-1 col-4">
                    <div class="d-flex">
                        <div class="d-block d-lg-none mobile-menu-icon-box">
                            <!-- Navbar toggler  -->
                            <a href="" onclick="sideMenuOpen(this)">
                                <div class="hamburger-icon">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                        </div>

                        <!-- Brand/Logo -->
                        <a class="navbar-brand w-100" href="{{ $logoLink }}">
                            <img src="{{ $logoUrl }}" alt="{{ $logoAlt }}">
                        </a>

                    </div>
                </div>
                <div class="col-lg-11 col-8 position-static">
                    <div class="d-flex w-100">
                        <div class="search-box flex-grow-1">
                            <form action="{{ route('search') }}" method="GET">
                                <div class="d-flex position-relative">
                                    <div class="d-lg-none search-box-back">
                                        <button class="" type="button"><i class="la la-long-arrow-left"></i>
                                        </button>
                                    </div>
                                    <div class="w-100">
                                        <input type="text" aria-label="Search" id="search" name="q" class="w-100"
                                            placeholder="{{__('Search for Products, Brands') }}"
                                            value="{{ Request::get('q') }}"
                                            autocomplete="off">
                                    </div>
                                    <button class="d-none d-lg-block" type="submit">
                                        <i class="la la-search la-flip-horizontal"></i>
                                    </button>
                                    <div class="typed-search-box d-none">
                                        <div class="search-preloader">
                                            <div class="loader">
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                            </div>
                                        </div>
                                        <div class="search-nothing d-none">

                                        </div>
                                        <div id="search-content">

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="logo-bar-icons d-inline-block ml-auto">
                            <div class="d-inline-block d-lg-none">
                                <div class="nav-search-box">
                                    <a href="#" class="nav-box-link">
                                        <i class="la la-search la-flip-horizontal d-inline-block nav-box-icon"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="d-none d-lg-inline-block">
                                <div class="nav-wishlist-box" id="wishlist">
                                    <a href="{{ route('wishlists.index') }}" class="nav-box-link">
                                        <i class="la la-heart-o d-inline-block nav-box-icon"></i>
                                        <span
                                            class="nav-box-text d-none d-xl-inline-block">{{__('Wishlist') }}</span>
                                        @if(Auth::check())
                                            <span class="nav-box-number">{{ count(Auth::user()->wishlists)}}</span>
                                        @else
                                            <span class="nav-box-number">0</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                            <div class="d-inline-block" data-hover="dropdown">
                                <div class="nav-cart-box dropdown" id="cart_items">
                                    @include('frontend.partials.cart')
                                </div>
                            </div>
                            <div class="d-none d-lg-inline-block">
                                <div class="nav-compare-box" id="account">
                                    <a href="{{ route('profile') }}" class="nav-box-link">
                                        <i class="la la-user d-inline-block nav-box-icon"></i>
                                        <span
                                            class="nav-box-text d-none d-xl-inline-block">{{__('Account') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hover-category-menu" id="hover-category-menu">
        <div class="container">
            <div class="row no-gutters position-relative">
                <div class="col-lg-3 position-static">
                    <div class="category-sidebar" id="category-sidebar">
                        <div class="all-category" @if(isMicrosite() && !empty(getMicrosite()->second_color)) style="background-color: {{ getMicrosite()->second_color }};" @endif>
                            <span>{{ __('Categories') }}</span>
                            <a href="{{ route('categories.all') }}"
                               class="d-inline-block">{{ __('See All') }} ></a>
                        </div>
                        <ul class="categories">
                            @foreach ($mainCategories as $key => $category)
                                <li class="category-nav-element" data-id="{{ $category->id }}">
                                    <a href="{{ route('category.view', $category->slug) }}">
                                        <span class="cat-name">{{$category->translation_name}}</span>
                                    </a>
                                    @if(count($category->subCategories)>0)
                                        <div class="sub-cat-menu c-scrollbar">
                                            <div class="c-preloader">
                                                <i class="fa fa-spin fa-spinner"></i>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Navbar -->
@section('script')
    <script>
        //$('#cart-total').on('input', function(){ // capturing every step
        var currency = '$';
        var zone1 = 80; // Free Standard shipping
        var zone2 = 160; // Free Express shipping
        var cart_total = $('#cart-total').val();

        function human_money(money, currency) {
            return currency + parseFloat(money).toFixed(2);
        }

        cart_total = {{ $total }}
        $('.total').html(human_money(cart_total, currency));
        if (cart_total <= zone1) {
            var new_width = ((cart_total / zone1) * 100).toFixed(0) + '%';
            $('.progress-bar').css('width', new_width).html(new_width);
            //} else if (cart_total <= zone2 ) {
            //  var new_width = ((cart_total / zone2) * 100).toFixed(0) + '%';
            //  $('.progress-bar').css('width', new_width).html(new_width);
        } else {
            $('.progress-bar').css('width', '100%').html(currency + zone1);
        }
        if (cart_total < zone1) {
            $('.progress-bar').css('background', 'red');
            // $('.notice').html('Add <b>' + human_money(zone1 - cart_total, currency) + '</b> more to get <b>Free Local shipping</b>.');
            //} else if (cart_total < zone2 ) {
            //  $('.progress-bar').css('background', 'orange');
            //  $('.notice').html('Your order is <b>eligable</b> for <b>free STANDARD shipping</b>. Add <b>' + human_money(zone2 - cart_total, currency) + '</b> more to get <b>free EXPRESS shipping</b>.');
        } else {
            //  $('.progress-bar').css('background', '#49ff78');
            // $('.progress-bar').html('Great! you eligable for Free Local shipping');
        }
    </script>
@endsection
