@extends('frontend.layouts.app')
@section('content')
    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-6 mx-auto">
                    <div class="main-content">
                        <!-- Page title -->
                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-10">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                        {{ __('Become a Seller')}}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <img src="/assets/frontend/images/be_a_seller.png" style="width:100%">
                    </div>
                </div>
                <div class="col-lg-6 mx-auto">
                    <div class="main-content">
                        <!-- Page title -->
                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-12">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                        {{ __('I want to know more!')}}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <form id="shop" class="" action="{{ route('user_otps.create_user_otp', ['type' => $user_type]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (Auth::check())
                                <div class="form-box bg-white mt-4">
                                    <div class="form-box-title px-3 py-2">
                                        {{ __('Please logout and register with another email address.')}} <br>
                                        {{ __('You can also send a request to cs@ysg.com.sg.')}}
                                    </div>
                                </div>
                            @else

                                <div class="form-box bg-white mt-4">
                                    <div class="form-box-title px-3 py-2">
                                        {{ __('Contact info')}}
                                    </div>
                                    <div class="form-box-content p-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="input-group input-group--style-1">
                                                        <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" placeholder="{{  __('Name*') }}" name="name">
                                                        <span class="input-group-addon">
                                                    <i class="text-md la la-user"></i>
                                                </span>
                                                    </div>
                                                    @error('name')
                                                    <p class="text-danger">{{ __('Please input name') }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="input-group input-group--style-1">
                                                        <input type="text" class="form-control{{ $errors->has('company_name') ? ' is-invalid' : '' }}" value="{{ old('company_name') }}" placeholder="{{  __('Company Name*') }}" name="company_name">
                                                        <span class="input-group-addon">
                                                    <i class="text-md la la-user"></i>
                                                </span>
                                                    </div>
                                                    @error('company_name')
                                                    <p class="text-danger">{{ __('Please input company name') }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="input-group input-group--style-1">
                                                        <input type="text" class="form-control{{ $errors->has('website') ? ' is-invalid' : '' }}" value="{{ old('website') }}" placeholder="{{  __('Website URL') }}" name="website">
                                                        <span class="input-group-addon">
                                                    <i class="text-md la la-globe"></i>
                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="input-group input-group--style-1">
                                                        <input type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{ old('phone') }}" placeholder="{{  __('Phone Number*') }}" name="phone">
                                                        <span class="input-group-addon">
                                                    <i class="text-md la la-phone"></i>
                                                </span>
                                                    </div>
                                                    @error('phone')
                                                    <p class="text-danger">{{ __('Please input valid phone number') }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="input-group input-group--style-1">
                                                        <input type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  __('Corporate Email*') }}" name="email" id="email">
                                                        <span class="input-group-addon">
                                                    <i class="text-md la la-envelope"></i>
                                                </span>
                                                    </div>
                                                    @error('email')
                                                    <p class="text-danger">{{ __('Please input valid email') }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row d-none">
                                            <div class="col-12">
                                                <div class="form-group">
                                                <!-- <label>{{  __('Password') }}</label> -->
                                                    <div class="input-group input-group--style-1">
                                                        <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{  __('Password') }}" name="password" value="ysg@123">
                                                        <span class="input-group-addon">
                                                    <i class="text-md la la-lock"></i>
                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row d-none">
                                            <div class="col-12">
                                                <div class="form-group">
                                                <!-- <label>{{  __('Confirm Password') }}</label> -->
                                                    <div class="input-group input-group--style-1">
                                                        <input type="password" class="form-control" placeholder="{{  __('Confirm Password') }}" name="password_confirmation" value="ysg@123">
                                                        <span class="input-group-addon">
                                                    <i class="text-md la la-lock"></i>
                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--                        <div class="form-box bg-white mt-4">--}}
                                {{--                            <div class="form-box-title px-3 py-2">--}}
                                {{--                                {{ __('Basic Info')}}--}}
                                {{--                            </div>--}}
                                {{--                            <div class="form-box-content p-3">--}}
                                {{--                                <div class="row">--}}
                                {{--                                    <div class="col-md-2">--}}
                                {{--                                        <label>{{ __('Shop Name')}} <span class="required-star">*</span></label>--}}
                                {{--                                    </div>--}}
                                {{--                                    <div class="col-md-10">--}}
                                {{--                                        <input type="text" class="form-control mb-3" placeholder="{{ __('Shop Name')}}" name="name" required>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                                {{--                                <div class="row">--}}
                                {{--                                    <div class="col-md-2">--}}
                                {{--                                        <label>{{ __('Logo')}}</label>--}}
                                {{--                                    </div>--}}
                                {{--                                    <div class="col-md-10">--}}
                                {{--                                        <input type="file" name="logo" id="file-2" class="custom-input-file custom-input-file--4" data-multiple-caption="{count} files selected" accept="image/*" />--}}
                                {{--                                        <label for="file-2" class="mw-100 mb-3">--}}
                                {{--                                            <span></span>--}}
                                {{--                                            <strong>--}}
                                {{--                                                <i class="fa fa-upload"></i>--}}
                                {{--                                                {{ __('Choose image')}}--}}
                                {{--                                            </strong>--}}
                                {{--                                        </label>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                                {{--                                <div class="row">--}}
                                {{--                                    <div class="col-md-2">--}}
                                {{--                                        <label>{{ __('Address')}} <span class="required-star">*</span></label>--}}
                                {{--                                    </div>--}}
                                {{--                                    <div class="col-md-10">--}}
                                {{--                                        <input type="text" class="form-control mb-3" placeholder="{{ __('Company Address')}}" name="address" required>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                                {{--                            </div>--}}
                                {{--                        </div>--}}
                                @if($isCaptchaEnabled)
                                    <div class="form-group mt-2 mx-auto row">
                                        <div class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                                    </div>
                                @endif
                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-styled btn-base-1">{{ __('Submit Your interest')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script type="text/javascript">
      // making the CAPTCHA  a required field for form submission
      $(document).ready(function(){
        // alert('helloman');
        $("#shop").on("submit", function(evt)
        {
          var response = grecaptcha.getResponse();
          if(response.length == 0)
          {
            //reCaptcha not verified
            alert("please verify you are humann!");
            evt.preventDefault();
            return false;
          }
          //captcha verified
          //do the rest of your validations here
          $("#reg-form").submit();
        });

        $('#email').on('input', function() {
                const value = $(this).val();
            if (value.length) {
                $("#btn_send_otp").prop('disabled', false);
            } else {
                $("#btn_send_otp").prop('disabled', true);
            }
        
        });
      });
    </script>
@endsection
