@extends('frontend.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="container">
            <h1 class="hidden-xs">eNETS QR Test</h1>
            <div>

                <div class="data-input">
                    <table class="table table-condensed" align="center">
                        <thead>
                        <tr>
                            <th>Fields</th>
                            <th>Input</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><label>Amount (Cents) </label></td>
                            <td><input class="form-control" type="text" size="40" id="amount" value="1000"></td>
                        </tr>
                        <tr>
                            <td><label>UMID </label></td>
                            <td><input class="form-control" type="text" id="umid" value="UMID_877772003"></td>
                        </tr>
                        <tr>
                            <td><label>API Key </label></td>
                            <td><input class="form-control" type="text" id="apiKey"
                                       value="154eb31c-0f72-45bb-9249-84a1036fd1ca"></td>
                        </tr>
                        <tr>
                            <td><label>Secret Key </label></td>
                            <td><input class="form-control" type="text" id="secretKey"
                                       value="38a4b473-0295-439d-92e1-ad26a8c60279"></td>
                        </tr>
                        </tbody>
                    </table>

                    <input type="hidden" name="open-with" id="open-with" value="Hosted">
                    <input type="hidden" name="mode" id="mode" value="UAT">

                </div>
            </div>
            <div class="container container-result">

                <div class="hidden-md hidden-lg" id="netspay_image"></div>

                <!-- <div>
                  <div id="radioBtn" class="btn-group">
                    <a class="btn btn-primary btn-sm active" data-toggle="mode" data-title="UAT">UAT</a>
                    <a class="btn btn-primary btn-sm notActive" data-toggle="mode" data-title="Production">Production</a>
                  </div>
                  <input type="hidden" name="mode" id="mode">
                </div> -->
                <button class="btn btn-primary" align="center">Send Payload</button>
                <div class="payment-section">
                    <fieldset>
                        <div id="ajaxResponse"></div>
                    </fieldset>
                </div>
            </div>


            <script type="text/javascript">
                $('#radioBtn a').on('click', function () {
                    var sel = $(this).data('title');
                    var tog = $(this).data('toggle');
                    $('#' + tog).prop('value', sel);

                    $('a[data-toggle="' + tog + '"]').not('[data-title="' + sel + '"]').removeClass('active').addClass('notActive');
                    $('a[data-toggle="' + tog + '"][data-title="' + sel + '"]').removeClass('notActive').addClass('active');
                })
            </script>
        </div>
        <div id="soapi-time" class="container">
            <h6><p>UAT simulater (Credit &amp; Debit) is available daily from 0900H to 1600H (GMT+8)</p></h6>
        </div>
    </div>
@endsection
@section('script')
    <script src='https://uat2.enets.sg/GW2/js/jquery-3.1.1.min.js' type='text/javascript'>
    </script>
    <script src='https://uat2.enets.sg/GW2/pluginpages/env.jsp'>
    </script>
    <script type='text/javascript' src='https://uat2.enets.sg/GW2/js/apps.js'>
    </script>

    <script src="{{ static_asset('js/soapi.js') }}"></script>
    <script src="{{ static_asset('js/moment.js') }}"></script>
    <script src="{{ static_asset('js/sha256.js') }}"></script>

    <script type="text/javascript">
        $(function () {

            const txnReq = $('#txnReq').val();
            const keyId = $('#keyId').val();
            const hmac = $('#hmac').val();

            console.log(txnReq);
            console.log(keyId);
            console.log(hmac);

            sendPayLoad(txnReq, hmac, keyId);
        });
    </script>
@endsection
