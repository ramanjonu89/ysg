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
                                    {{ __('Change Password') }}
                                </h1>
                            </div>
                            <div class="px-5 py-3 py-lg-4">
                                <div class="">
                                    <form class="form-default" role="form" action="{{ route('user.update.password') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <input type="password" class="form-control h-auto form-control-lg @error('current_password') is-invalid @enderror"
                                                    placeholder="{{ __('Current Password') }}" name="current_password" required>
                                            @error('current_password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control h-auto form-control-lg @error('new_password') is-invalid @enderror"
                                                    placeholder="{{ __('New Password') }}" name="new_password" required>
                                            @error('new_password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control h-auto form-control-lg"
                                                    placeholder="{{ __('Comfirm Password') }}" name="new_password_confirmation" required>
                                        </div>
                                        <div class="text-center">
                                            <button class="btn btn-styled btn-base-1 btn-md w-100" type="submit">{{ __('Submit') }}</button>
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
