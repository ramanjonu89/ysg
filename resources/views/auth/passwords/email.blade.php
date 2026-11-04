@extends('layouts.blank')



@section('content')



<div class="cls-content-sm panel">

    <div class="panel-body">

        <h1 class="h3">{{ __('Reset Password') }}</h1>

        <p class="pad-btm">{{ __('Enter your email address to recover your password.') }} </p>

            <div class="form-group">

                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)

                    <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required placeholder="{{ __('Email or Phone') }}">

                @else

                    <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ __('Email') }}" name="email">

                @endif



                @if ($errors->has('email'))

                    <span class="invalid-feedback" role="alert">

                        <strong>{{ $errors->first('email') }}</strong>

                    </span>

                @endif

            </div>

            <div class="form-group text-right">
                <button class="btn btn-danger btn-block" id="btn_send_password_link" type="button">
                    <span class="d-none loading d-flex justify-content-center align-items-center">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            {{ __('Send Password Reset Link') }}
                    </span>
                    <span class="default">{{ __('Send Password Reset Link') }}</span>
                </button>
            </div>

        <div class="pad-top">

            <a href="{{route('user.login') }}" class="btn-link text-bold text-main">{{ __('Back to Login') }}</a>

        </div>

    </div>

</div>

@endsection
@section('script')
<script type="text/javascript">

    $(function() {
        $('#email').keyup(function(){
            if (event.keyCode === 13) {
                $("#btn_send_password_link").click();
            }
        });

        $("#btn_send_password_link").click(function(){
            const email = $("#email").val();
            $(this).find('.loading').removeClass('d-none');
            $(this).find('.default').addClass('d-none');
            $("#btn_send_password_link").prop('disabled', true);
            $.post('{{ route('password.email') }}', {
                _token:'{{ csrf_token() }}',  
                email: email
            }, function(data) {
                data = JSON.parse(data);
                $('.default').removeClass('d-none');
                $('.loading').addClass('d-none');
                if (data.status == 1) {
                    function setTimeDisableBtnSubmit() {
                        $("#btn_send_password_link").prop('disabled', false);
                    }
                    setTimeout(setTimeDisableBtnSubmit, 60000);
                } else {
                    $("#btn_send_password_link").prop('disabled', false);
                }
                if (data.status == 2)
                    showFrontendAlert('warning', data.message);
                else if (data.status == 1)
                    showFrontendAlert('success', data.message);
                else
                    showFrontendAlert('danger', data.message);
            });
        }); 
    });

</script>
@endsection

