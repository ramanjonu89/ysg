@extends('frontend.layouts.app')
@section('content')
    <section class="gry-bg py-5">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-5 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            <div class="text-center px-5 pt-5 ">
                                <h1 class="heading heading-4 strong-500">
                                    {{ __('Confirming the OTP') }}
                                </h1>
                            </div>
                            <div class="px-5 py-3 py-lg-4">
                                <div class="">
                                    <form class="form-default" role="form" action="@if($userOtp->isSellers()){{ route('shops.store') }}@else{{ route('register') }}@endif " method="POST">
                                        @csrf
                                        <input id="email" type="hidden" name="email" value="{{ $email }}">
                                        <div class="form-group">
                                            <input type="text" class="form-control h-auto form-control-lg"
                                                   placeholder="{{ __('OTP') }}" name="otp" id="otp" required>
                                        </div>
                                        <div class="form-group">
                                            @if ($countSend === 1)
                                                <span class="" id="message_resend_after_one_minute">{{ __('Resend OTP after 1 minute') }}</span>
                                            @endif
                                            <span class="d-none" id="message_resend_after_three_minute">{{ __('Resend OTP after 3 minute') }}</span>
                                            <span class="d-none" id="message_contact_us_via_email">{{ __('If you don\'t receive the OTP, please contact us via email.') }}</span>
                                            <a class="d-none resend-otp-button" id="resend_otp"  href="#">
                                                <span class="text-button">{{ __('Resend OTP') }}</span>
                                            </a>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-styled btn-base-1 btn-md w-100">{{  __('OTP Authentication For Registration') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            const email = $('#email').val();
            $.post('{{ route('user_otps.count_send_otp') }}', {
                _token:'{{ csrf_token() }}',
                email:email
            }, function(data) {
                if (data.count_send_otp === 2) {
                    $('#message_resend_after_three_minute').removeClass('d-none');
                    function setTimeEnableBtn() {
                        $('#message_resend_after_three_minute').addClass('d-none');
                        $('#resend_otp').removeClass('d-none');
                    }
                    setTimeout(setTimeEnableBtn, 18000);
                } else if (data.count_send_otp === 3 ) {
                    $('#message_contact_us_via_email').removeClass('d-none');
                } else {
                    function setTimeEnableBtn() {
                        $('#message_resend_after_one_minute').addClass('d-none');
                        $('#resend_otp').removeClass('d-none');
                    }
                    setTimeout(setTimeEnableBtn, 6000); 
                }
            });
        });

        $('#resend_otp').on('click', function() {
            const email = $('#email').val();
            $.ajax({
                url: "{{ route('user_otps.resend_otp') }}",
                method: 'POST',
                data: { 
                    _token: '{{ csrf_token() }}',
                    email: email 
                },
                beforeSend: function () {
                    $.LoadingOverlay("show", {
                        size: 10,
                        image: "",
                        fontawesome: "fa fa-refresh fa-spin"
                    });
                },
                complete: function () {
                    $.LoadingOverlay('hide');
                    $('#resend_otp').addClass('d-none');
                },
                success: function (response) {
                    data = JSON.parse(response);
                    if (data.count === 2) {
                        $('#message_resend_after_three_minute').removeClass('d-none');
                        function setTimeEnableBtn() {
                            $('#message_resend_after_three_minute').addClass('d-none');
                            $('#resend_otp').removeClass('d-none');
                        }
                        setTimeout(setTimeEnableBtn, 18000);
                    } else {
                        $('#message_contact_us_via_email').removeClass('d-none');
                    }
                    showFrontendAlert('success', data.message);
                }
            });
        });


    </script>
@endsection
<style>
  .resend-otp-button:hover {
    text-decoration: underline;
    color: blue;
  }

  .text-button {
    font-weight: bold;
  }
</style>
