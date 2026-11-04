<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">



    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">



    <link name="favicon" type="image/x-icon" href="{{static_asset('img/favicon.png') }}" rel="shortcut icon" />



    <title>{{ config('app.name', 'Laravel') }}</title>



    <!-- Fonts -->

    <link rel="dns-prefetch" href="https://fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">



    <!--Bootstrap Stylesheet [ REQUIRED ]-->

    <link href="{{ static_asset('css/bootstrap.min.css') }}" rel="stylesheet">



    <!--Font Awesome [ OPTIONAL ]-->

    <link href="{{ static_asset('plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">



    <!--active-shop Stylesheet [ REQUIRED ]-->

    <link href="{{ static_asset('css/active-shop.min.css') }}" rel="stylesheet">



    <!--active-shop Premium Icon [ DEMONSTRATION ]-->

    <link href="{{ static_asset('css/demo/active-shop-demo-icons.min.css') }}" rel="stylesheet">



    <!--Demo [ DEMONSTRATION ]-->

    <link href="{{ static_asset('css/demo/active-shop-demo.min.css') }}" rel="stylesheet">



    <!--Theme [ DEMONSTRATION ]-->

    <link href="{{ static_asset('css/themes/type-c/theme-navy.min.css') }}" rel="stylesheet">



    <link href="{{ static_asset('css/custom.css') }}" rel="stylesheet">

    <link type="text/css" href="{{ static_asset('frontend/css/sweetalert2.min.css') }}" rel="stylesheet"
              media="none" onload="if(media!='all')media='all'">



</head>

<body>

    <div id="container" class="blank-index"

        @if ($generalSetting->admin_login_background != null)

            style="background-image:url('{{ my_asset($generalSetting->admin_login_background) }}');"

        @else

            style="background-image:url('{{ my_asset('img/bg-img/login-bg.jpg') }}');"

        @endif>

        <div class="cls-content">

            <div class="container">

                <div class="row">

                    <div class="col-md-8 col-md-offset-2">

                        <div class="panel">

                            <div class="panel-body pad-no">

                                @yield('content')

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <!--JAVASCRIPT-->

    <!--=================================================-->



    <!--jQuery [ REQUIRED ]-->

    <script src="{{ static_asset('js/jquery.min.js') }}"></script>

    <script src="{{ static_asset('frontend/js/sweetalert2.min.js') }}"></script>
    <script>
        function showFrontendAlert(type, message) {
            if (type == 'danger') {
                type = 'error';
            }
            swal.fire({
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
            showFrontendAlert('{{ $message['level'] }}', "{!! $message['message'] !!}");
        </script>
    @endforeach

    <!--BootstrapJS [ RECOMMENDED ]-->

    <script src="{{ static_asset('js/bootstrap.min.js') }}"></script>





    <!--active-shop [ RECOMMENDED ]-->

    <script src="{{ static_asset('js/active-shop.min.js') }}"></script>



    <!--Alerts [ SAMPLE ]-->

    <script src="{{static_asset('js/demo/ui-alerts.js') }}"></script>



    @yield('script')



</body>

</html>

