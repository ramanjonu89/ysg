@extends('frontend.layouts.app')
@section('content')

    <form id="eNETSRedirectForm" name="eNETSRedirectForm" action="{{$formData['action']}}" method="POST">
        @foreach($formData['inputs'] as $key => $value)
            <input type="hidden" id="{{ $key }}" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>


    <div class="container">
        <div class="row gutters-5">
            <div class="col-lg-12">
                <h3>
                    {{ __('eNETS QR') }}
                </h3>
            </div>
        </div>
        <div class="row gutters-5">
            <div class="col-lg-8" style="padding-top:5px;padding-bottom:5px;">
                <div class="text-center text-box mt-0">
                    <p>{{ __('Please stay on this page while your payment is being verified.')}}</p>
                    <p>{{ __('It may take up to a minute after the completion of the payment.')}}</p>
                    <p>{{ __('During this time, please do not refresh or leave the page.')}}</p>
                </div>

                <div id="anotherSection" class="row" style="margin-left: 5px">
                    <fieldset>
                        <div id="ajaxResponse"></div>
                    </fieldset>
                </div>
                <div class="text-center mt-3">
                    <a href="/checkout/payment_select" class="btn btn-base-1 btn-icon-left">
                        {{ __('Back') }}
                    </a>
                </div>
                <div class="text-center mt-3">
                    <p><i>{{ __('If the QR Code expires, please use the "Back" button to complete your payment process again.') }}</i></p>
                </div>
            </div>
            <div class="col-lg-4 ml-lg-auto">
                @include('frontend.payment.order_summary', [$tempCart])
            </div>
        </div>
    </div>

    <style>
        html {
            font-size: 16px;
            -ms-overflow-style: scrollbar;
            box-sizing: border-box;
            font-family: sans-serif;
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
        }
        body {
            margin: 0;
            font-family: "sen","Open Sans", sans-serif;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.5;
            color: #525f7f;
            text-align: left;
            background-color: #fff;
        }
        .badge-success {
            background-color: #28a745 !important;
        }
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }

        .text-box {
            border: 1px solid #f01a1a;
            padding: 10px 20px;
            margin: 20px 10px;
            background: #fff;
        }

        .text-box p {
            margin: 5px 0;
            color: #f01a1a;
            font-weight: bold;
            line-height: 12px;
        }

        .self-collection-address {
            font-weight: 300;
        }
    </style>


@endsection

@section('script')
    @if(env('APP_ENV') === 'production')
        <script src="https://www2.enets.sg/GW2/pluginpages/env.jsp"></script>
        <script type="text/javascript" src="https://www2.enets.sg/GW2/js/apps.js"></script>
    @else
        <script src="https://uat2.enets.sg/GW2/pluginpages/env.jsp"></script>
        <script type="text/javascript" src="https://uat2.enets.sg/GW2/js/apps.js"></script>
    @endif

    <script type="text/javascript">
        $(function () {

            const txnReq = $('#payload').val();
            const keyId = $('#apiKey').val();
            const hmac = $('#hmac').val();

            console.log(txnReq);
            console.log(keyId);
            console.log(hmac);

            sendPayLoad(txnReq, hmac, keyId);

            // const form = $('#eNETSRedirectForm');
            // form.submit();

        });
    </script>
@endsection
