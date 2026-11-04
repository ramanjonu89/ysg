<!DOCTYPE html>
@if(\App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
<html dir="rtl" lang="en">
@else
<html lang="en">
@endif
<head>
    @php
        $seosetting = \App\SeoSetting::first();
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <title>@yield('meta_title', config('app.name', 'Laravel'))</title>
    <meta name="description" content="@yield('meta_description', $seosetting->description)"/>
    <meta name="keywords" content="@yield('meta_keywords', $seosetting->keyword)">
    <meta name="author" content="{{ $seosetting->author }}">
    <meta name="sitemap_link" content="{{ $seosetting->sitemap_link }}">
    <meta name="google-site-verification" content="ez7SKDGw2B0Cks9bSNgJcHfd-MfyQmMbo5xq6TkNWGU"/>
    @yield('meta')
    @if(!isset($detailedProduct) && !isset($shop) && !isset($page))
    <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="{{ config('app.name', 'Laravel') }}">
        <meta itemprop="description" content="{{ $seosetting->description }}">
        <meta itemprop="image" content="https://ysg.com.sg/uploads/logo/logo_white.png">
        <!-- Twitter Card data -->
        <meta name="twitter:card" content="product">
        <meta name="twitter:site" content="@publisher_handle">
        <meta name="twitter:title" content="{{ config('app.name', 'Laravel') }}">
        <meta name="twitter:description" content="{{ $seosetting->description }}">
        <meta name="twitter:creator" content="@author_handle">
        <meta name="twitter:image" content="https://ysg.com.sg/uploads/logo/logo_white.png">
        <!-- Open Graph data -->
        <meta property="og:title" content="{{ config('app.name', 'Laravel') }}"/>
        <meta property="og:type" content="website"/>
        <meta property="og:image" content="https://ysg.com.sg/uploads/logo/logo_white.png"/>
        <meta property="og:description" content="{{ $seosetting->description }}"/>
        <meta property="og:site_name" content="{{ env('APP_NAME') }}"/>
    @endif
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="Y.SG Online Shopping"/>
    <meta property="og:description" content="Private Sales . Lowest Price . Best Products"/>
    <meta property="og:image" content="https://ysg.com.sg/uploads/logo/logo_white.png"/>
    <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
    <!-- Favicon -->
    <link type="image/x-icon" href="{{ my_asset(\App\Models\GeneralSetting::first()->favicon) }}"
          rel="shortcut icon"/>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i"
          rel="stylesheet" media="none" onload="if(media!='all')media='all'">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/bootstrap.min.css') }}" type="text/css"
          media="all">
    <!-- Icons -->
    <link rel="stylesheet" href="{{ static_asset('frontend/css/font-awesome.min.css') }}" type="text/css"
          media="none" onload="if(media!='all')media='all'">
    <link rel="stylesheet" href="{{ static_asset('frontend/css/line-awesome.min.css') }}" type="text/css"
          media="none" onload="if(media!='all')media='all'">
    <link type="text/css" href="{{ static_asset('frontend/css/bootstrap-tagsinput.css') }}" rel="stylesheet"
          media="none" onload="if(media!='all')media='all'">
    <link type="text/css" href="{{ static_asset('frontend/css/jodit.min.css') }}" rel="stylesheet" media="none"
          onload="if(media!='all')media='all'">
    <link type="text/css" href="{{ static_asset('frontend/css/sweetalert2.min.css') }}" rel="stylesheet"
          media="none" onload="if(media!='all')media='all'">
    <link type="text/css" href="{{ static_asset('frontend/css/slick.css') }}" rel="stylesheet" media="all">
    <link type="text/css" href="{{ static_asset('frontend/css/xzoom.css') }}" rel="stylesheet" media="none"
          onload="if(media!='all')media='all'">
    <link type="text/css" href="{{ static_asset('frontend/css/jssocials.css') }}" rel="stylesheet" media="none"
          onload="if(media!='all')media='all'">
    <link type="text/css" href="{{ static_asset('frontend/css/jssocials-theme-flat.css') }}" rel="stylesheet"
          media="none" onload="if(media!='all')media='all'">
    <link type="text/css" href="{{ static_asset('frontend/css/intlTelInput.min.css') }}" rel="stylesheet"
          media="none" onload="if(media!='all')media='all'">
    <link type="text/css" href="{{ static_asset('css/spectrum.css') }}" rel="stylesheet" media="none"
          onload="if(media!='all')media='all'">
    <!-- Global style (main) -->
    <link type="text/css" href="{{ static_asset('frontend/css/active-shop.css') }}" rel="stylesheet"
          media="all">
    <link type="text/css" href="{{ static_asset('frontend/css/main.css') . '?v=' . time() }}" rel="stylesheet"
          media="all">
@if(\App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
    <!-- RTL -->
        <link type="text/css" href="{{ static_asset('frontend/css/active.rtl.css') }}" rel="stylesheet"
              media="all">
@endif
<!-- color theme -->
    <link href="{{ static_asset('frontend/css/colors/'.\App\Models\GeneralSetting::first()->frontend_color.'.css') }}"
          rel="stylesheet" media="all">
    <!-- Custom style -->
    <link type="text/css" href="{{ static_asset('frontend/css/custom-style.css') }}" rel="stylesheet"
          media="all">
@yield('css')
<!-- jQuery -->
    <script src="{{ static_asset('frontend/js/vendor/jquery.min.js') }}"></script>
    <script src="//code.tidio.co/pnl6xsrplg28qgfngkwideyrrdwzooef.js" async></script>
@if (\App\BusinessSetting::where('type', 'google_analytics')->first()->value == 1)
    <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('TRACKING_ID') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', '{{ env('TRACKING_ID') }}');
        </script>
@endif
@if (\App\BusinessSetting::where('type', 'facebook_pixel')->first()->value == 1)
    <!-- Facebook Pixel Code -->
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', {{ env('FACEBOOK_PIXEL_ID') }});
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none"
                 src="https://www.facebook.com/tr?id={{ env('FACEBOOK_PIXEL_ID') }}/&ev=PageView&noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
@endif
<!--DataTables [ OPTIONAL ]-->
    <script src=" {{static_asset('js/jquery.min.js') }}"></script>
    <script src="{{ static_asset('plugins/datatables/media/js/jquery.dataTables.js') }}"></script>
    <script src="{{ static_asset('plugins/datatables/media/js/dataTables.bootstrap.js') }}"></script>
    <script
        src="{{ static_asset('plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.print.min.js"></script>
    <script src="{{ static_asset('js/loading-overlay.min.js') }}"></script>
    <!--DataTables [ OPTIONAL ]-->
    <link href="{{ static_asset('plugins/datatables/media/css/dataTables.bootstrap.css') }}" rel="stylesheet">
    <link
        href="{{ static_asset('plugins/datatables/extensions/Responsive/css/responsive.dataTables.min.css') }}"
        rel="stylesheet">
</head>
<body>
<!-- MAIN WRAPPER -->
<div class="shop-default shop-cards shop-tech gry-bg">
    <!-- Header -->
    @include('frontend.inc.nav')
    @yield('content')
    @include('frontend.inc.microsite_footer')
    @include('frontend.partials.modal')
    @if (\App\BusinessSetting::where('type', 'facebook_chat')->first()->value == 1)
        <div id="fb-root"></div>
        <!-- Your customer chat code -->
        <div class="fb-customerchat"
             attribution=setup_tool
             page_id="{{ env('FACEBOOK_PAGE_ID') }}">
        </div>
    @endif
    <div class="modal fade" id="addToCart">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size"
             role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>
                <button type="button" class="close absolute-close-btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div id="addToCart-modal-body">
                </div>
            </div>
        </div>
    </div>
</div><!-- END: body-wrap -->
<!-- Modal -->
<div class="modal fade" id="corporateAccessCountDownModal" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 rounded">
            <div class="modal-header bg-danger text-light border-0 rounded-top">
                <button type="button" class="close p-0 pt-1 pr-1" data-dismiss="modal" aria-label="Close">
                    <span class="font-size-1 text-light" aria-hidden="true">X</span>
                </button>
            </div>
            <div class="modal-body bg-danger text-light border-0 rounded-bottom">
                <p>Woah! You have been given a time-limited exclusive deals at the Corporate Sale Store. The
                    clock is counting down and while stocks last!</p>
                <h3 class="text-center activated-countdown" id="coprorate-access-countdown-lg"></h3>
            </div>
        </div>
    </div>
</div>
<span id="coprorate-access-countdown-sm"
      class="activated-countdown fixed-top float-right bg-danger font-weight-bold invisible"></span>
<!-- SCRIPTS -->
<!-- Modal -->
@php
    $corporateAccessService = new \App\Services\CorporateAccessService();
    $hasCorporateAccess = $corporateAccessService->hasCorporateAccess();
@endphp
@if ($hasCorporateAccess))
<div class="modal fade" id="corporateAccessNotifyModal" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 rounded">
            <div class="modal-header bg-success text-light border-0 rounded-top">
                <button type="button" class="close p-0 pt-1 pr-1" data-dismiss="modal" aria-label="Close">
                    <span class="font-size-1 text-light" aria-hidden="true">X</span>
                </button>
            </div>
            <div class="modal-body bg-success text-light border-0 rounded-bottom">
                <h5 class="text-center">{{ sprintf(__('You have %d unused Corporate Access Pass(es).'), $hasCorporateAccess) }}</h5>
                <div class="col-12 d-flex justify-content-center">
                    <button id="btn-manage-access"
                            class="btn btn-light">{{ __('View & Activate Access Pass') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- <a href="#" class="back-to-top btn-back-to-top"></a> -->
<!-- Core -->
<script src="{{ static_asset('frontend/js/vendor/popper.min.js') }}"></script>
<script src="{{ static_asset('frontend/js/vendor/bootstrap.min.js') }}"></script>
<!-- Plugins: Sorted A-Z -->
<script src="{{ static_asset('frontend/js/jquery.countdown.min.js') }}"></script>
<script src="{{ static_asset('frontend/js/select2.min.js') }}"></script>
<script src="{{ static_asset('frontend/js/nouislider.min.js') }}"></script>
<script src="{{ static_asset('frontend/js/sweetalert2.min.js') }}"></script>
<script src="{{ static_asset('frontend/js/slick.min.js') }}"></script>
<script src="{{ static_asset('frontend/js/jssocials.min.js') }}"></script>
<script src="{{ static_asset('frontend/js/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ static_asset('frontend/js/jodit.min.js') }}"></script>
<script src="{{ static_asset('frontend/js/xzoom.min.js') }}"></script>
<script src="{{ static_asset('frontend/js/fb-script.js') }}"></script>
<script src="{{ static_asset('frontend/js/lazysizes.min.js') }}"></script>
<script src="{{ static_asset('frontend/js/intlTelInput.min.js') }}"></script>
<!-- App JS -->
<script src="{{ static_asset('frontend/js/active-shop.js') }}"></script>
<script src="{{ static_asset('frontend/js/main.js') }}"></script>
<script>
    function showFrontendAlert(type, message) {
        if (type == 'danger') {
            type = 'error';
        }
        swal({
            position: 'top-end',
            type: type,
            title: message,
            showConfirmButton: false,
            timerProgressBar: true,
            timer: 5000,
        });
    }
</script>
@foreach (session('flash_notification', collect())->toArray() as $message)
    <script>
        showFrontendAlert('{{ $message['level'] }}', '{!! $message['message'] !!}');
    </script>
@endforeach
<script>
    $(document).ready(function () {
        $('.category-nav-element').each(function (i, el) {
            $(el).on('mouseover', function () {
                if (!$(el).find('.sub-cat-menu').hasClass('loaded')) {
                    $.post('{{ route('category.elements') }}', {
                        _token: '{{ csrf_token()}}',
                        id: $(el).data('id')
                    }, function (data) {
                        $(el).find('.sub-cat-menu').addClass('loaded').html(data);
                    });
                }
            });
        });
        if ($('#lang-change').length > 0) {
            $('#lang-change .dropdown-item a').each(function () {
                $(this).on('click', function (e) {
                    e.preventDefault();
                    var $this = $(this);
                    var locale = $this.data('flag');
                    $.post('{{ route('language.change') }}', {
                        _token: '{{ csrf_token() }}',
                        locale: locale
                    }, function (data) {
                        location.reload();
                    });
                });
            });
        }
        if ($('#currency-change').length > 0) {
            $('#currency-change .dropdown-item a').each(function () {
                $(this).on('click', function (e) {
                    e.preventDefault();
                    var $this = $(this);
                    var currency_code = $this.data('currency');
                    $.post('{{ route('currency.change') }}', {
                        _token: '{{ csrf_token() }}',
                        currency_code: currency_code
                    }, function (data) {
                        location.reload();
                    });
                });
            });
        }
    });
    $('#search').on('keyup', function () {
        search();
    });
    $('#search').on('focus', function () {
        search();
    });

    function search() {
        var search = $('#search').val();
        if (search.length > 0) {
            $('body').addClass("typed-search-box-shown");
            $('.typed-search-box').removeClass('d-none');
            $('.search-preloader').removeClass('d-none');
            $.post('{{ route('search.ajax') }}', {
                _token: '{{ @csrf_token() }}',
                search: search
            }, function (data) {
                if (data == 0) {
                    // $('.typed-search-box').addClass('d-none');
                    $('#search-content').html(null);
                    $('.typed-search-box .search-nothing').removeClass('d-none').html('Sorry, nothing found for <strong>"' + search + '"</strong>');
                    $('.search-preloader').addClass('d-none');
                } else {
                    $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                    $('#search-content').html(data);
                    $('.search-preloader').addClass('d-none');
                }
            });
        } else {
            $('.typed-search-box').addClass('d-none');
            $('body').removeClass("typed-search-box-shown");
        }
    }

    function updateNavCart() {
        $.post('{{ route('cart.nav_cart') }}', {_token: '{{ csrf_token() }}'}, function (data) {
            $('#cart_items').html(data);
        });
    }

    function removeFromCart(key) {
        $.post('{{ route('cart.removeFromCart') }}', {_token: '{{ csrf_token() }}', key: key}, function (data) {
            updateNavCart();
            $('#cart-summary').html(data);
            showFrontendAlert('success', 'Item has been removed from cart');
            $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) - 1);
        });
    }

    function addToCompare(id) {
        $.post('{{ route('compare.addToCompare') }}', {_token: '{{ csrf_token() }}', id: id}, function (data) {
            $('#compare').html(data);
            showFrontendAlert('success', 'Item has been added to compare list');
            $('#compare_items_sidenav').html(parseInt($('#compare_items_sidenav').html()) + 1);
        });
    }

    function addToWishList(id) {
        @if (Auth::check() && (Auth::user()->user_type == 'customer' || Auth::user()->user_type == 'seller'))
        $.post('{{ route('wishlists.store') }}', {_token: '{{ csrf_token() }}', id: id}, function (data) {
            if (data != 0) {
                $('#wishlist').html(data);
                showFrontendAlert('success', 'Item has been added to wishlist');
            } else {
                showFrontendAlert('warning', 'Please login first');
            }
        });
        @else
        showFrontendAlert('warning', 'Please login first');
        @endif
    }

    function showAddToCartModal(id) {
        if (!$('#modal-size').hasClass('modal-lg')) {
            $('#modal-size').addClass('modal-lg');
        }
        $('#addToCart-modal-body').html(null);
        $('#addToCart').modal();
        $('.c-preloader').show();
        $.post('{{ route('cart.showCartModal') }}', {_token: '{{ csrf_token() }}', id: id}, function (data) {
            $('.c-preloader').hide();
            $('#addToCart-modal-body').html(data);
            $('.xzoom, .xzoom-gallery').xzoom({
                Xoffset: 20,
                bg: true,
                tint: '#000',
                defaultScale: -1
            });
            getVariantPrice();
        });
    }

    $('#option-choice-form input').on('change', function () {
        getVariantPrice();
    });

    function getVariantPrice() {
        if ($('#option-choice-form input[name=quantity]').val() > 0 && checkAddToCartValidity()) {
            $.ajax({
                type: "POST",
                url: '{{ route('products.variant_price') }}',
                data: $('#option-choice-form').serializeArray(),
                success: function (data) {
                    $('#option-choice-form #chosen_price_div').removeClass('d-none');
                    $('#option-choice-form #chosen_price_div #chosen_price').html(data.price);
                    $('#available-quantity').html(data.quantity);
                    $('.input-number').prop('max', data.quantity);
                    //console.log(data.quantity);
                    if (parseInt(data.quantity) < 1 && data.digital != 1) {
                        // $('.buy-now').hide();
                        // $('.add-to-cart').hide();
                    } else {
                        $('.buy-now').show();
                        $('.add-to-cart').show();
                    }
                }
            });
        }
    }

    function checkAddToCartValidity() {
        var names = {};
        $('#option-choice-form input:radio').each(function () { // find unique names
            names[$(this).attr('name')] = true;
        });
        var count = 0;
        $.each(names, function () { // then count them
            count++;
        });
        if ($('#option-choice-form input:radio:checked').length == count) {
            return true;
        }
        return false;
    }

    function addToCart() {
        var is_age_restricted = $('#option-choice-form input[name=is_age_restricted]').val();
        if (is_age_restricted === '0') {
            doAddToCart();
        } else {
            if (sessionStorage.getItem('isConfirmedRestrictedAge') == 1) {
                doAddToCart();
            } else {
                swal.fire({
                    title: 'Confirm your age',
                    text: "You must be 18 or above to buy this Product!",
                    showCloseButton: true,
                    showCancelButton: true,
                    width: 600,
                    confirmButtonColor: '#ff825b',
                    cancelButtonColor: '#ED4C67',
                    confirmButtonText: 'Over 18',
                    cancelButtonText: 'Not 18 yet'
                }).then((result) => {
                    if (result.value) {
                        sessionStorage.setItem('isConfirmedRestrictedAge', 1)
                        doAddToCart();
                    }
                })
            }
        }
    }

    function doAddToCart() {
        if (checkAddToCartValidity()) {
            $('#addToCart').modal();
            $('.c-preloader').show();
            $.ajax({
                type: "POST",
                url: '{{ route('cart.addToCart') }}',
                data: $('#option-choice-form').serializeArray(),
                success: function (data) {
                    $('#addToCart-modal-body').html(null);
                    $('.c-preloader').hide();
                    $('#modal-size').removeClass('modal-lg');
                    $('#addToCart-modal-body').html(data);
                    updateNavCart();
                    $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) + 1);
                }
            });
        } else {
            showFrontendAlert('warning', 'Please choose all the options');
        }
    }

    function buyNow() {
        var is_age_restricted = $('#option-choice-form input[name=is_age_restricted]').val();
        if (is_age_restricted === '0') {
            doBuyNow();
        } else {
            if (sessionStorage.getItem('isConfirmedRestrictedAge') == 1) {
                doBuyNow();
            } else {
                swal.fire({
                    title: 'Confirm your age',
                    text: "You must be 18 or above to buy this Product!",
                    showCloseButton: true,
                    showCancelButton: true,
                    width: 600,
                    confirmButtonColor: '#ff825b',
                    cancelButtonColor: '#ED4C67',
                    confirmButtonText: 'Over 18',
                    cancelButtonText: 'Not 18 yet'
                }).then((result) => {
                    if (result.value) {
                        sessionStorage.setItem('isConfirmedRestrictedAge', 1)
                        doBuyNow();
                    }
                })
            }
        }
    }

    function doBuyNow() {
        if (checkAddToCartValidity()) {
            $('#addToCart').modal();
            $('.c-preloader').show();
            $.ajax({
                type: "POST",
                url: '{{ route('cart.addToCart') }}',
                data: $('#option-choice-form').serializeArray(),
                success: function (data) {
                    //$('#addToCart-modal-body').html(null);
                    //$('.c-preloader').hide();
                    //$('#modal-size').removeClass('modal-lg');
                    //$('#addToCart-modal-body').html(data);
                    updateNavCart();
                    $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) + 1);
                    window.location.replace("{{ route('cart') }}");
                }
            });
        } else {
            showFrontendAlert('warning', 'Please choose all the options');
        }
    }

    function show_purchase_history_details(order_id) {
        $('#order-details-modal-body').html(null);
        if (!$('#modal-size').hasClass('modal-lg')) {
            $('#modal-size').addClass('modal-lg');
        }
        $.post('{{ route('purchase_history.details') }}', {
            _token: '{{ @csrf_token() }}',
            order_id: order_id
        }, function (data) {
            $('#order-details-modal-body').html(data);
            $('#order_details').modal();
            $('.c-preloader').hide();
        });
    }

    function show_order_details(order_id) {
        $('#order-details-modal-body').html(null);
        if (!$('#modal-size').hasClass('modal-lg')) {
            $('#modal-size').addClass('modal-lg');
        }
        $.post('{{ route('orders.details') }}', {
            _token: '{{ @csrf_token() }}',
            order_id: order_id
        }, function (data) {
            $('#order-details-modal-body').html(data);
            $('#order_details').modal();
            $('.c-preloader').hide();
        });
    }

    function cartQuantityInitialize() {
        $('.btn-number').click(function (e) {
            e.preventDefault();
            fieldName = $(this).attr('data-field');
            type = $(this).attr('data-type');
            var input = $("input[name='" + fieldName + "']");
            var currentVal = parseInt(input.val());
            if (!isNaN(currentVal)) {
                if (type == 'minus') {
                    if (currentVal > input.attr('min')) {
                        input.val(currentVal - 1).change();
                    }
                    if (parseInt(input.val()) == input.attr('min')) {
                        $(this).attr('disabled', true);
                    }
                } else if (type == 'plus') {
                    if (currentVal < input.attr('max')) {
                        input.val(currentVal + 1).change();
                    }
                    if (parseInt(input.val()) == input.attr('max')) {
                        $(this).attr('disabled', true);
                    }
                }
            } else {
                input.val(0);
            }
        });
        $('.input-number').focusin(function () {
            $(this).data('oldValue', $(this).val());
        });
        $('.input-number').change(function () {
            minValue = parseInt($(this).attr('min'));
            maxValue = parseInt($(this).attr('max'));
            valueCurrent = parseInt($(this).val());
            name = $(this).attr('name');
            if (valueCurrent >= minValue) {
                $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
            } else {
                alert('Sorry, the minimum value was reached');
                $(this).val($(this).data('oldValue'));
            }
            if (valueCurrent <= maxValue) {
                $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
            }
        });
        $(".input-number").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    }

    function imageInputInitialize() {
        $('.custom-input-file').each(function () {
            var $input = $(this),
                $label = $input.next('label'),
                labelVal = $label.html();
            $input.on('change', function (e) {
                var fileName = '';
                if (this.files && this.files.length > 1)
                    fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                else if (e.target.value)
                    fileName = e.target.value.split('\\').pop();
                if (fileName)
                    $label.find('span').html(fileName);
                else
                    $label.html(labelVal);
            });
            // Firefox bug fix
            $input
                .on('focus', function () {
                    $input.addClass('has-focus');
                })
                .on('blur', function () {
                    $input.removeClass('has-focus');
                });
        });
    }
</script>
<script>
    $(".res-table").DataTable({
        responsive: false,
        paging: true,
        searching: true,
        ordering: true,
        bInfo: true,
    });
</script>
<script>
    var corporateAccessExpire;
    if (sessionStorage.getItem('corporateAccessExpire')) {
        corporateAccessExpire = sessionStorage.getItem('corporateAccessExpire');
    } else {
        corporateAccessExpire = '{{ session()->get('corporate_access_expire') ? session()->get('corporate_access_expire')->format('Y/m/d H:i:s') : '' }}';
        sessionStorage.setItem('corporateAccessExpire', corporateAccessExpire);
    }
    var hasUnusedCorporateAccess = {{ session()->has('has_unused_corporate_access') ? session()->get('has_unused_corporate_access') : 0 }};
    if (hasUnusedCorporateAccess === 1 && sessionStorage.getItem('hasUnusedCorporateAccess') !== '0') {
        sessionStorage.setItem('hasUnusedCorporateAccess', hasUnusedCorporateAccess)
    }
    if (corporateAccessExpire) {
        sessionStorage.setItem('hasUnusedCorporateAccess', '0');
    }
    if (corporateAccessExpire) {
        $('.activated-countdown').countdown(corporateAccessExpire)
            .on('update.countdown', function (event) {
                var format = '%Hh %Mm %Ss';
                format = '%-dd%!d ' + format;
                if (event.offset.weeks > 0) {
                    format = '%-w w%!w ' + format;
                }
                $(this).html(event.strftime(format));
            })
            .on('finish.countdown', function (event) {
                $(this).hide();
                swal({
                    text: '{{ __('Your Time-Limited Corporate Sales Store Access Pass has expired') }}',
                    type: 'info'
                }).then(function () {
                    sessionStorage.clear();
                    window.location = '{{ route('system.logout') }}';
                });
            });
        if (!sessionStorage.getItem('closedCorporateModal')) {
            $('#corporateAccessCountDownModal').modal();
        } else {
            $('#coprorate-access-countdown-sm').toggleClass('invisible');
        }
        $('#corporateAccessCountDownModal').on('hidden.bs.modal', function () {
            $('#coprorate-access-countdown-sm').toggleClass('invisible');
            sessionStorage.setItem('closedCorporateModal', true);
        });
    } else {
        $('#corporateAccessNotifyModal').on('hidden.bs.modal', function () {
            sessionStorage.setItem('hasUnusedCorporateAccess', '0');
        });
        $('#btn-manage-access').click(function () {
            sessionStorage.setItem('hasUnusedCorporateAccess', '0');
            window.location.href = '{{ route('referral.access') }}'
        });
        if (sessionStorage.getItem('hasUnusedCorporateAccess') === '1') {
            $('#corporateAccessNotifyModal').modal();
        }
    }
    // Event on logout on web
    $('#user-logout').click(function (e) {
        e.preventDefault();
        sessionStorage.clear();
        window.location.href = '{{ route('system.logout') }}';
    })
    // Event on logout on mobile
    $('#user-logout-mobile').click(function (e) {
        e.preventDefault();
        sessionStorage.clear();
        window.location.href = '{{ route('system.logout') }}';
    })
</script>
@yield('script')
<style>
    .swal-overlay {
        backdrop-filter: blur(10px);
    }
</style>
</body>
</html>
