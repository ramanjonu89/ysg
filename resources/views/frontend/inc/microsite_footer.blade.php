<footer id="footer-mobile" class="footer-mobile" style="display:none">
    <div class="container">
        <div class="row" style="border-top:5px solid #4F136D;">
            <div class="col-lg-3 col text-center mt-2">
                <a href="{{ getAppHomepageLink() }}">
                    <i class="la la-home" style="font-size: 22px;"></i>
                    <div style="font-weight:800">Shop</div>
                </a>
            </div>
            <div class="col-lg-3 col text-center mt-2">
                <a href="{{ route('wishlists.index') }}">
                    <i class="la la-heart-o" style="font-size: 22px;"></i>
                    <div style="font-weight:800">Wishlist</div>
                </a>
            </div>
            <div class="col-lg-3 col text-center mt-2">
                <a href="{{ route('cart') }}">
                    <i class="la la-shopping-cart" style="font-size: 24px;"></i>
                    <div style="font-weight:800">Cart</div>
                </a>
            </div>
            <div class="col-lg-3 col text-center mt-2">
                <a href="{{ route('dashboard') }}"> <i class="la la-user" style="font-size: 22px;"></i>
                    <div style="font-weight:800">Account</div>
                </a>
            </div>
        </div>
    </div>
</footer>
<footer id="footer-bar-top" class="footer-bar-top" style="display:none">
</footer>
<!-- FOOTER -->
<footer id="footer" class="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-xl-4 col-lg-3">
                </div>
                <div class="col-xl-3 col-lg-3 col-6">
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
                                            <img style="width:40%; margin-left: 7%"
                                                 src=" {{ static_asset('frontend/images/icons/cards/Latitude-Pay_Logo_Blue_RGB.png') }}">
                                        </a>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-6">
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
                <div class="col-xl-4 col-lg-3">
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom py-3 sct-color-3">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="copyright text-center">
                        © {{ date('Y') }} {{ __('Powered by').' '.$generalSetting->site_name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<div id="light" class="white_content">
    @php
        $newsletter = DB::table('newsletter_config') ->where('id', 1)->first();
    @endphp
    <p><a href="javascript:void(0)" style="float:right" onclick="closepop()">Close</a></p>
    <p><a href="{{$newsletter->url}}"><img style="width:100%" class="image100"
                                           src="{{my_asset($newsletter->photo)}}"></a></p>
    <p style="text-align: center;">{{$newsletter->description}}</p>
    @if ($newsletter->show_subbox == 1)
        <div class="d-inline-block d-md-block" style="width:100%">
            <form method="POST" action="{{ route('subscribers.store') }}">
                @csrf
                <div class="form-group mb-0">
                    <input type="email" class="form-control" placeholder="{{ __('Your Email Address') }}"
                           name="email" required>
                </div>
                <button type="submit" class="btn btn-base-1 btn-icon-left top-10" style="margin-top:10px;width:100%"
                        onclick="closepop()">
                    {{ __('Subscribe') }}
                </button>
            </form>
        </div>
        <p style="text-align: center;"><span class="fine-print">We do not sell or rent your personal information, and any such disclosure shall comply and be in accordance with the Personal Data Protection Act, 2012.</span>
        </p>
    @endif
</div>
<div id="fade" class="black_overlay"></div>
<style>
    .stop-scrolling {
        height: 100%;
        overflow: hidden;
    }
    .black_overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: black;
        z-index: 1001;
        -moz-opacity: 0.8;
        opacity: .90;
        filter: alpha(opacity=90);
    }
    .white_content {
        border-radius: 20px;
        display: none;
        position: absolute;
        top: 25%;
        left: 50%;
        width: 600px;
        margin-left: -300px;
        padding: 16px;
        background-color: white;
        z-index: 1002;
        overflow: auto;
        box-sizing: border-box;
        filter: Alpha(opacity=100);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
    }
    .white_content h1 {
        border-bottom: 1px #eee solid;
        margin: 0;
        padding: 5px 0;
        color: #000000;
        font-size: 23px;
        line-height: 1.3;
    }
    .white_content p {
        color: #333333;
        font-size: 16px;
        text-align: left;
        line-height: 1.5;
    }
    .fine-print {
        color: #aaaaaa;
        font-size: 13px;
        text-align: center;
        line-height: 1.7;
    }
    .image100 {
        width: 100% !important;
    }
    input[type=text] {
        width: 100%;
        padding: 10px;
        box-sizing: border-box;
        font-size: 17px;
    }
    input[type=submit] {
        display: block;
        vertical-align: middle !important;
        padding: 9px 32px !important;
        border: 0 !important;
        margin: 5px 0;
        line-height: 1.5;
        font-size: 15px;
        text-decoration: none;
        color: #FFFFFF;
        text-align: center !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25) !important;
        background: #4F136D;
        border-bottom: 2px solid #4F136D;
        cursor: pointer !important;
        -webkit-box-shadow: inset 0 -2px #4F136D;
        box-shadow: inset 0 -2px #4F136D;
        -webkit-border-radius: 0px !important;
        -moz-border-radius: 0px !important;
        border-radius: 20px !important;
        width: 100%;
    }
    input[type=submit]:hover {
        background: #4F136D;
    }
    @media (max-width: 600px) {
        .white_content {
            width: 300px;
            margin-left: -150px;
        }
    }
    @media (max-width: 400px) {
        .white_content {
            width: 280px;
            margin-left: -140px;
        }
    }
</style>
@if ($newsletter->status == 1)
    <script>
        $(document).ready(function () {
            //localStorage.setItem('isshow', 0);
            var isshow = sessionStorage.getItem('isshow');
            console.log(isshow);
            if (isshow == null) {
                sessionStorage.setItem('isshow', 1);
                window.newsletterPopupShown = true;
                openpop();
            } else {
                window.newsletterPopupShown = false;
            }
        });
        function openpop() {
            document.getElementById('light').style.display = 'block';
            document.getElementById('fade').style.display = 'block';
            var d = document.getElementById("section_best_sellers");
            $(window).unbind('scroll');
        }
        function closepop() {
            document.getElementById('light').style.display = 'none';
            document.getElementById('fade').style.display = 'none';
            
            if (window.newsletterPopupShown) {
                $('.slick-carousel').each(function() {
                    if ($(this).hasClass('slick-initialized')) {
                        $(this).slick('slickPlay');
                    }
                });
            }
        }
    </script>
@endif
