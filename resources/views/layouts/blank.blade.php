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

    <link type="text/css" href="{{ static_asset('frontend/css/sweetalert2.min.css') }}" rel="stylesheet" media="none" onload="if(media!='all')media='all'">


    <!--Demo [ DEMONSTRATION ]-->

    <link href="{{ static_asset('css/demo/active-shop-demo.min.css') }}" rel="stylesheet">



    <!--Theme [ DEMONSTRATION ]-->

    <link href="{{ static_asset('css/themes/type-c/theme-navy.min.css') }}" rel="stylesheet">



    <link href="{{ static_asset('css/custom.css') }}" rel="stylesheet">



    <!--JAVASCRIPT-->

    <!--=================================================-->



    <!--jQuery [ REQUIRED ]-->

    <script src=" {{static_asset('js/jquery.min.js') }}"></script>





    <!--BootstrapJS [ RECOMMENDED ]-->

    <script src="{{ static_asset('js/bootstrap.min.js') }}"></script>





    <!--active-shop [ RECOMMENDED ]-->

    <script src="{{ static_asset('js/active-shop.min.js') }}"></script>



    <!--Alerts [ SAMPLE ]-->

    <script src="{{ static_asset('js/demo/ui-alerts.js') }}"></script>



    <!--Switchery [ OPTIONAL ]-->

    <script src="{{ static_asset('plugins/switchery/switchery.min.js') }}"></script>



    <!--DataTables [ OPTIONAL ]-->

    <script src="{{ static_asset('plugins/datatables/media/js/jquery.dataTables.js') }}"></script>

    <script src="{{ static_asset('plugins/datatables/media/js/dataTables.bootstrap.js') }}"></script>

    <script src="{{ static_asset('plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js') }}"></script>



    <!--DataTables Sample [ SAMPLE ]-->

    <script src="{{ static_asset('js/demo/tables-datatables.js') }}"></script>



    <!--Select2 [ OPTIONAL ]-->

    <script src="{{ static_asset('plugins/select2/js/select2.min.js') }}"></script>



    <!--Summernote [ OPTIONAL ]-->

    <script src="{{ static_asset('js/jodit.min.js') }}"></script>



    <!--Bootstrap Tags Input [ OPTIONAL ]-->

    <script src="{{ static_asset('plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>



    <!--Bootstrap Validator [ OPTIONAL ]-->

    <script src="{{ static_asset('plugins/bootstrap-validator/bootstrapValidator.min.js') }}"></script>



    <!--Bootstrap Wizard [ OPTIONAL ]-->

    <script src="{{ static_asset('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>



    <!--Bootstrap Datepicker [ OPTIONAL ]-->

    <script src="{{ static_asset('plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>



    <!--Form Component [ SAMPLE ]-->

    <script src="{{static_asset('js/demo/form-wizard.js') }}"></script>



    <!--Spectrum JavaScript [ REQUIRED ]-->

    <script src="{{ static_asset('js/spectrum.js') }}"></script>



    <!--Spartan Image JavaScript [ REQUIRED ]-->

    <script src="{{ static_asset('js/spartan-multi-image-picker-min.js') }}"></script>



    <!--Custom JavaScript [ REQUIRED ]-->

    <script src="{{ static_asset('js/custom.js') }}"></script>



</head>

<body>

    <div id="container" class="">

        <div class="cls-content">

            <div class="container">

                <div class="row">

                    <div class="col-md-8 col-md-offset-2">

                        <div class="panel">

                            <div class="panel-body">

                                @yield('content')

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    @yield('script')
    
    <script src="{{ static_asset('frontend/js/sweetalert2.min.js') }}"></script>

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
            showFrontendAlert('{{ $message['level'] }}', "{!! $message['message'] !!}");
        </script>
    @endforeach



</body>

</html>

