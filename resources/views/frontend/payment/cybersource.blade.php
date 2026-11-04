@extends('frontend.layouts.app')
@section('content')
    <section class="gry-bg py-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-2">
                    <i class="fa fa-cog" aria-hidden="true"></i>
                    <div class="fa fa-cog fa-spin"></div>
                    <form id="cybersource_form" action="{{ paymentSetting('cybersource.api').'/pay' }}" method="post">
                        @foreach($formData as $key => $value)
                            <input type="hidden" id="{{ $key }}" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="loadingoverlay"
             style="box-sizing: border-box; position: fixed; display: flex; flex-flow: column nowrap; align-items: center; justify-content: space-around; background: rgba(255, 255, 255, 0.8); top: 0px; left: 0px; width: 100%; height: 100%; z-index: 2147483647; opacity: 1;">
            <div class="loadingoverlay_element loadingoverlay_fa"
                 style="order: 2; box-sizing: border-box; overflow: visible; flex: 0 0 auto; display: flex; justify-content: center; align-items: center; animation-name: loadingoverlay_animation__undefined; animation-timing-function: linear; animation-iteration-count: infinite; color: rgb(32, 32, 32); font-size: 93.7px;">
                <div class="fa fa-cog fa-spin"></div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script type="text/javascript">
        $(function () {
            $('#cybersource_form').submit();
            // $.LoadingOverlay("show", {
            //     size: 10,
            //     image: "",
            //     fontawesome: "fa fa-cog fa-spin"
            // });
        });
    </script>
@endsection
