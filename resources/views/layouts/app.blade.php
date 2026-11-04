<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link name="favicon" type="image/x-icon" href="{{ static_asset(\App\Models\GeneralSetting::first()->favicon) }}"
          rel="shortcut icon"/>
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!--Bootstrap Stylesheet [ REQUIRED ]-->
    <link href="{{ static_asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!--active-shop Stylesheet [ REQUIRED ]-->
    <link href="{{ static_asset('css/active-shop.min.css') }}" rel="stylesheet">
    <link href="{{ static_asset('css/demo/active-shop-demo-icons.min.css') }}" rel="stylesheet">

    <!--Font Awesome [ OPTIONAL ]-->
    <link href="{{ static_asset('plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">

    <!--Switchery [ OPTIONAL ]-->
    <link href="{{ static_asset('plugins/switchery/switchery.min.css') }}" rel="stylesheet">

    <!--DataTables [ OPTIONAL ]-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/autofill/2.4.0/css/autoFill.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.6/css/colReorder.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.2/css/dataTables.dateTime.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.1.0/css/fixedColumns.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.2.4/css/fixedHeader.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/keytable/2.7.0/css/keyTable.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.2.0/css/rowGroup.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.7/css/scroller.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.3.4/css/searchBuilder.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchpanes/2.0.2/css/searchPanes.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.4.0/css/select.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/staterestore/1.1.1/css/stateRestore.dataTables.css"/>

    <!--Select2 [ OPTIONAL ]-->
    <link href="{{ static_asset('plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ static_asset('css/bootstrap-select.min.css') }}" rel="stylesheet">


    <!--Bootstrap Tags Input [ OPTIONAL ]-->
    <link href="{{ static_asset('plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.css') }}" rel="stylesheet">

    <!--Summernote [ OPTIONAL ]-->
    <link href="{{ static_asset('css/jodit.min.css') }}" rel="stylesheet">

    <!--Theme [ DEMONSTRATION ]-->
    <link href="{{ static_asset('css/themes/type-c/theme-navy.min.css') }}" rel="stylesheet">

    <!--Spectrum Stylesheet [ REQUIRED ]-->
    <link href="{{ static_asset('css/spectrum.min.css') }}" rel="stylesheet">

    <!--Jquery-ui Stylesheet [ REQUIRED ]-->
    <link href="{{ static_asset('css/jquery-ui.min.css') }}" rel="stylesheet">

    <!--Custom Stylesheet [ REQUIRED ]-->
    <link href="{{ static_asset('css/custom.css') }}" rel="stylesheet">
{{--    <link href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css" rel="stylesheet">--}}

    <link type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="{{ static_asset('frontend/css/line-awesome.min.css') }}" type="text/css" media="none" onload="if(media!='all')media='all'">

    <!-- Toast css-->
    <link href="{{ static_asset('plugins/toastr/css/toastr.min.css') }}" rel="stylesheet">


    @yield('css')
    @stack('css')

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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/autofill/2.4.0/js/dataTables.autoFill.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.colVis.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/colreorder/1.5.6/js/dataTables.colReorder.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/4.1.0/js/dataTables.fixedColumns.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/keytable/2.7.0/js/dataTables.keyTable.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.2.0/js/dataTables.rowGroup.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/scroller/2.0.7/js/dataTables.scroller.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.3.4/js/dataTables.searchBuilder.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchpanes/2.0.2/js/dataTables.searchPanes.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.4.0/js/dataTables.select.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/staterestore/1.1.1/js/dataTables.stateRestore.js"></script>


    <!-- Jquery Validator [ OPTINAL ]-->
    <script src="{{ static_asset('plugins/jquery-validator/jqueryValidate.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!--DataTables Sample [ SAMPLE ]-->
    <script src="{{ static_asset('js/demo/tables-datatables.js') }}"></script>

    <!--Select2 [ OPTIONAL ]-->
    <script src="{{ static_asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ static_asset('js/bootstrap-select.min.js') }}"></script>

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
    <script src="{{ static_asset('js/spectrum.min.js') }}"></script>

    <!--Spartan Image JavaScript [ REQUIRED ]-->
    <script src="{{ static_asset('js/spartan-multi-image-picker-min.js') }}"></script>

    <!--Jquery-ui JavaScript [ REQUIRED ]-->
    <script src="{{ static_asset('js/jquery-ui.min.js') }}"></script>

    <!-- Toast JS-->

    <script src="{{ static_asset('js/custom.js') }}"></script>

    <!--Custom JavaScript [ REQUIRED ]-->
    <script src="{{ static_asset('plugins/toastr/js/toastr.min.js') }}"></script>

    <script type="text/javascript">

        $(document).ready(function () {
            // $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
            if($('.active-link').parent().parent().parent().is('ul')){
                $('.active-link').parent().parent().addClass('in');
                $('.active-link').parent().parent().parent().addClass('in');
            }
            if($('.active-link').parent().parent().is('li')){
                $('.active-link').parent().parent().addClass('active-sub');
            }
            if($('.active-link').parent().is('ul')){
                $('.active-link').parent().addClass('in');
            }

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

        });

    </script>
    <!-- Hotjar Tracking Code for www.ysg.com.sg -->
    <script>
        (function (h, o, t, j, a, r) {
            h.hj = h.hj || function () {
                (h.hj.q = h.hj.q || []).push(arguments)
            };
            h._hjSettings = {hjid: 2152246, hjsv: 6};
            a = o.getElementsByTagName('head')[0];
            r = o.createElement('script');
            r.async = 1;
            r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
            a.appendChild(r);
        })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
    </script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
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


</head>
<body>

@foreach (session('flash_notification', collect())->toArray() as $message)
    <script type="text/javascript">
        $(document).on('nifty.ready', function () {
            showAlert('{{ $message['level'] }}', '{{ $message['message'] }}');
        });
    </script>
@endforeach


<div id="container" class="effect aside-float aside-bright mainnav-lg">

    @include('inc.admin_nav')

    <div class="boxed">

        <!--CONTENT CONTAINER-->
        <!--===================================================-->
        <div id="content-container">
            <div id="page-content">

                @yield('content')

            </div>
        </div>
    </div>

    @include('inc.admin_sidenav')

    @include('inc.admin_footer')

    @include('partials.modal')

    @yield('modal')

</div>


<script src="{{ static_asset('js/bootbox.min.js') }}"></script>

@yield('script')
@stack('script')

</body>
</html>
