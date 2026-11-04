@extends('frontend.layouts.app')
@section('content')
    <section class="gry-bg py-4">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            <div class="text-center px-35 pt-5">
                                <h1 class="heading heading-4 strong-500">
                                    {{ __('Create My YSG Account') }}
                                </h1>
                            </div>
                            @if($ref_error_message)
                                <div class="px-5 py-3 py-lg-4">
                                    <p class="pl-2 pr-2 text-danger text-center">
                                        {{ $ref_error_message }}
                                    </p>
                                </div>
                            @else
                                <div class="px-5 py-3 py-lg-4">
                                    <div class="">
                                        <form id="reg-form" class="form-default" role="form" action="{{ route('user_otps.create_user_otp', ['ref' => $ref, 'referral_by' => $referal_code]) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <input type="text" class="h-auto form-control-lg form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" placeholder="{{ __('Name') }}" name="name" required>
                                                @if ($errors->has('name'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                                <div class="form-group phone-form-group mb-1">
                                                    <input type="tel" id="phone-code" class="h-auto w-100 form-control-lg form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off">
                                                </div>
                                                <input type="hidden" name="country_code" value="">
                                                <div class="form-group email-form-group mb-1 d-none">
                                                    <input type="email" class="h-auto form-control-lg form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ __('Email') }}" name="email"  autocomplete="off">
                                                    @if ($errors->has('email'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('email') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-link p-0" type="button" onclick="toggleEmailPhone(this)">{{ __('Use Email Instead') }}</button>
                                                </div>
                                            @else
                                                <div class="form-group phone-number {{ $errors->has('phone') ? ' is-invalid' : '' }}">
                                                    <input type="hidden" class="country_code" name="country_code" value="">
                                                    <input type="tel" id="phone-code" class="h-auto form-control-lg form-control"
                                                            value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off" required>
                                                    @if ($errors->has('phone'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('phone') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="form-group">
                                                    <input type="email" class="h-auto form-control-lg form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ __('Email') }}" name="email" id="email"  required>
                                                    @if ($errors->has('email'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('email') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="form-group">
                                                <input type="password" class="h-auto form-control-lg form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" name="password"  required>
                                                @if ($errors->has('password'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <input type="password" class="h-auto form-control-lg form-control" placeholder="{{ __('Confirm Password') }}" name="password_confirmation"  required>
                                            </div>
                                            <div class="form-group">
                                                <input type="referral" class="h-auto form-control-lg form-control" placeholder="{{ __('Referral Code') }}" name="referral_by" @if(isset($referal_code)) value="{{ $referal_code }}" disabled @endif>
                                            </div>
                                            @if($isCaptchaEnabled)
                                                <div class="form-group">
                                                    <div class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                                                </div>
                                            @endif
                                            <div class="checkbox text-left">
                                                <div class="terms mb-0">
                                                    <input class="magic-checkbox" type="checkbox" name="terms" id="terms" required>
                                                    <label for="terms" class="text-sm">
                                                        {{ __('By creating a YSG account, I acknowledge that I have read and agreed to') }}
                                                        <span>
                                                            <a href="{{route('terms')}}" target="_blank">{{ __('YSG’s Customers Terms & Conditions') }}</a>
                                                            {{ __(' and its ')}}
                                                            <a href="{{route('privacypolicy')}}" target="_blank">{{ __('Personal Data Protection Policy.')}}</a>
                                                            {{ __(' I hereby consent to the collection, use and disclosure of my personal data by YSG in accordance with
                                                                    the Personal Data Protection Act 2012 (“PDPA”).') }}
                                                        </span>
                                                    </label>
                                                </div>
                                                <span class="invalid-feedback" role="alert" id="error_terms">
                                                    <strong>Please agree to the above to complete the registration</strong>
                                                </span>
                                            </div>
                                            <div class="checkbox text-left">
                                                <div class="terms">
                                                    <input value="1" id="receive_special_offers" name="receive_special_offers" type="checkbox">
                                                    <label for="receive_special_offers" class="text-sm">
                                                        <span>{{ __('I want to receive the latest promotions and discounts from YSG.
                                                                    By clicking this box, I agree to receive marketing materials from YSG.') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="text-right mt-3">
                                                <button type="button" class="submit-btn btn btn-styled btn-base-1 w-100 btn-md">{{ __('Create Account') }}</button>
                                            </div>
                                        </form>
                                        @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                            <div class="or or--1 mt-3 text-center">
                                                <span>or</span>
                                            </div>
                                            <div>
                                                @if (\App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                                                    <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="btn btn-styled btn-block btn-facebook btn-icon--2 btn-icon-left px-4 mb-3">
                                                        <i class="icon fa fa-facebook"></i> {{ __('Login with Facebook') }}
                                                    </a>
                                                @endif
                                                @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                                                    <a href="{{ route('social.login', ['provider' => 'google']) }}" class="btn btn-styled btn-block btn-google btn-icon--2 btn-icon-left px-4 mb-3">
                                                        <i class="icon fa fa-google"></i> {{ __('Login with Google') }}
                                                    </a>
                                                @endif
                                                @if (\App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                                    <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="btn btn-styled btn-block btn-twitter btn-icon--2 btn-icon-left px-4">
                                                        <i class="icon fa fa-twitter"></i> {{ __('Login with Twitter') }}
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-center px-35 pb-3">
                                    <p class="text-md">
                                        {{ __('Already have an account?') }}
                                        <a href="{{ route('user.login') }}" class="strong-600">{{ __('Log In') }}</a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .phone-number .iti {
                width: 100% !important;
            }

            .is-invalid .invalid-feedback {
                display: block;
            }
        </style>
    </section>
    @include('frontend.inc.js.phone-input')
@endsection
@section('script')
    @parent
    <script src="{{ static_asset('js/loading-overlay.min.js') }}"></script>
    @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    <script type="text/javascript">
        // making the CAPTCHA  a required field for form submission
        $(document).ready(function() {
            let submitting = false;
            $("#reg-form .submit-btn").on("click", function(evt)
            {
                @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
                    var response = grecaptcha.getResponse();
                    if(response.length == 0)
                    {
                        //reCaptcha not verified
                        showFrontendAlert('error','Please verify you are humann!');
                        evt.preventDefault();
                        return false;
                    }
                @endif

                const status = $("#terms").prop('checked');
                if (!status) {
                    $("#error_terms").show();
                    evt.preventDefault();
                    return false;
                }

                if (!submitting) {
                    submitting = true;
                    $.LoadingOverlay("show", {
                        size: 10,
                        image: "",
                        fontawesome: "fa fa-refresh fa-spin"
                    });
                    $("#reg-form").submit();
                } else {
                    evt.preventDefault();
                    return false;
                }
            });
        });

        function toggleEmailPhone(el) {
            if(isPhoneShown) {
                $('.phone-form-group').addClass('d-none');
                $('.email-form-group').removeClass('d-none');
                isPhoneShown = false;
                $(el).html('Use Phone Instead');
            } else {
                $('.phone-form-group').removeClass('d-none');
                $('.email-form-group').addClass('d-none');
                isPhoneShown = true;
                $(el).html('Use Email Instead');
            }
        }
    </script>
@endsection
