@extends('frontend.layouts.app')
@section('content')
    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @if(Auth::user()->user_type == 'seller')
                        @include('frontend.inc.seller_side_nav')
                    @elseif(Auth::user()->user_type == 'customer')
                        @include('frontend.inc.customer_side_nav')
                    @endif
                </div>
                <div class="col-lg-9">
                    <div class="main-content">
                        <!-- Page title -->
                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-6 col-12">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                        {{ __('Manage Profile') }}
                                    </h2>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="float-md-right">
                                        @include('frontend.inc.breadcrumb', ['breadcrumbs' => [
                                            [
                                                'link' => route('dashboard'),
                                                'text' => __('Dashboard')
                                            ],
                                            [
                                                'link' => route('profile'),
                                                'text' => __('Manage Profile')
                                            ],
                                        ]])
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form class="" action="{{ route('seller.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-box bg-white mt-4">
                                <div class="form-box-title px-3 py-2">
                                    {{ __('Basic info') }}
                                </div>
                                <div class="form-box-content p-3">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Your Name') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control mb-3" placeholder="{{ __('Your Name') }}" name="name" value="{{ Auth::user()->name }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label>{{ __('Your Phone') }}</label>
                                        </div>
                                        <div class="col-md-10 phone-number">
                                            <input type="hidden" class="country_code" name="country_code" value="{{ Auth::user()->country_code }}">
                                            <input type="tel" id="phone-code-profile" class="h-auto w-100 form-control"
                                                    value="{{ Auth::user()->phone }}" placeholder="" name="phone" autocomplete="off"  required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Photo') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="file" name="photo" id="file-3" class="custom-input-file custom-input-file--4" data-multiple-caption="{count} files selected" accept="image/*" />
                                            <label for="file-3" class="mw-100 mb-3">
                                                <span></span>
                                                <strong>
                                                    <i class="fa fa-upload"></i>
                                                    {{ __('Choose image') }}
                                                </strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Your Password') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="password" class="form-control mb-3" placeholder="{{ __('New Password') }}" name="new_password">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Confirm Password') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="password" class="form-control mb-3" placeholder="{{ __('Confirm Password') }}" name="confirm_password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-box bg-white mt-4">
                                <div class="form-box-title px-3 py-2">
                                    {{ __('Addresses') }}
                                </div>
                                <div class="form-box-content p-3">
                                    <div class="row gutters-10" id="address-list">
                                        @foreach (Auth::user()->addresses as $address)
                                            @include('frontend.partials.single_address', ['address' => $address])
                                        @endforeach
                                        <div class="col-lg-6 mx-auto" id="add-address-box" onclick="add_new_address()">
                                            <div class="border p-3 rounded mb-3 c-pointer text-center bg-light">
                                                <i class="la la-plus la-2x"></i>
                                                <div class="alpha-7">{{ __('Add New Address') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--
                            <div class="form-box bg-white mt-4">
                                <div class="form-box-title px-3 py-2">
                                    {{ __('Payment Setting') }}
                                </div>
                                <div class="form-box-content p-3">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Cash Payment') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <label class="switch mb-3">
                                                <input value="1" name="cash_on_delivery_status" type="checkbox" @if (Auth::user()->seller->cash_on_delivery_status == 1) checked @endif>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Bank Payment') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <label class="switch mb-3">
                                                <input value="1" name="bank_payment_status" type="checkbox" @if (Auth::user()->seller->bank_payment_status == 1) checked @endif>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Bank Name') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control mb-3" placeholder="{{ __('Bank Name') }}" value="{{ Auth::user()->seller->bank_name }}" name="bank_name">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Bank Account Name') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control mb-3" placeholder="{{ __('Bank Account Name') }}" value="{{ Auth::user()->seller->bank_acc_name }}" name="bank_acc_name">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Bank Account Number') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control mb-3" placeholder="{{ __('Bank Account Number') }}" value="{{ Auth::user()->seller->bank_acc_no }}" name="bank_acc_no">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Bank Routing Number') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="number" class="form-control mb-3" placeholder="{{ __('Bank Routing Number') }}" value="{{ Auth::user()->seller->bank_routing_no }}" name="bank_routing_no">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            -->
                            <div class="text-right mt-4">
                                <button type="submit" class="btn btn-styled btn-base-1">{{ __('Update Profile') }}</button>
                            </div>
                        </form>
                        <form action="{{ route('user.change.email') }}" method="POST">
                            @csrf
                            <div class="form-box bg-white mt-4">
                                <div class="form-box-title px-3 py-2">
                                    {{ __('Change your email') }}
                                </div>
                                <div class="form-box-content p-3">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Your Email') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="input-group mb-3">
                                                <input
                                                    type="email"
                                                    class="form-control"
                                                    placeholder="{{ __('Your Email') }}"
                                                    name="email"
                                                    value="{{ Auth::user()->email }}"
                                                />
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary new-email-verification">
                                                        <span class="d-none loading">
                                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                            Verifying...
                                                        </span>
                                                        <span class="default">Verify</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <button class="btn btn-styled btn-base-1" type="submit">Update Email</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="new-address-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ __('New Address') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST" id="address-form">
                    @csrf
                    <div class="modal-body">
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('Title') }}</label>
                                </div>
                                <div class="col-md-10 mb-3">
                                    <input type="text" class="form-control"
                                                placeholder="{{ __('Title') }}" name="title" required></input>
                                    <small class="text-danger error-text title_error"></small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('Address') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <textarea class="form-control textarea-autogrow mb-3" placeholder="{{ __('Your Address') }}" rows="1" name="address" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>{{ __('Unit No.') }}</label>
                                </div>
                                <div class="col-sm-10 mb-3">
                                    <input type="text" class="form-control" placeholder="{{ __('e.g. 09-11') }}"
                                        name="unitno" value="" required>
                                    <small class="text-danger error-text unitno_error"></small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('Country') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <select id="select-country" class="form-control mb-3 selectpicker" data-placeholder="{{ __('Select your country') }}" name="country_id" required>
                                            @foreach (\App\Models\Country::where('status', 1)->orderBy('name','asc')->get() as $key => $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('City') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <select id="select-city" class="form-control mb-3 selectpicker" data-placeholder="{{ __('Select Your City') }}" name="city_id" required>
                                            @if(isset($cities) && !empty($cities))
                                                @foreach($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    {{-- <input type="text" class="form-control mb-3" placeholder="{{ __('Your City') }}" name="city" value="" required> --}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('Postal code') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="{{ __('Your Postal Code') }}" name="postal_code" value="" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('Phone') }}</label>
                                </div>
                                <div class="col-md-10 mb-3 phone-number">
                                    <input type="hidden" class="country_code" name="country_code" value="">
                                    <input type="tel" id="phone-code" class="h-auto form-control phone-init"
                                            value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off" required>
                                    <small class="text-danger error-text phone_error"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-base-1">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('frontend.inc.js.phone-input')
@endsection
@section('script')
    <script type="text/javascript">
        function add_new_address(){
            $('#new-address-modal').modal('show');
        }
        $('.new-email-verification').on('click', function() {
            $(this).find('.loading').removeClass('d-none');
            $(this).find('.default').addClass('d-none');
            var email = $("input[name=email]").val();
            $.post('{{ route('user.new.verify') }}', {_token:'{{ csrf_token() }}', email: email}, function(data){
                data = JSON.parse(data);
                $('.default').removeClass('d-none');
                $('.loading').addClass('d-none');
                if(data.status == 2)
                    showFrontendAlert('warning', data.message);
                else if(data.status == 1)
                    showFrontendAlert('success', data.message);
                else
                    showFrontendAlert('danger', data.message);
            });
        });

        $(document).on('change','#select-country', function(){
            getCities($(this).val(), '#select-city');
        });

        function getCities(country_id, id) {
            $.get("/get-cities/" + country_id, function(data) {
                data.html == 0 ? $(id).html("<option value=''>-- No City --</option>") : $(id).html(data.html);
            });
        }

        $('#address-form').on('submit', function(e) {
            e.preventDefault();

            let form = this;
            let formData = new FormData(form);

            $('.error-text').text('');
            $('.form-control').removeClass('is-invalid');

            $.ajax({
                url: form.action,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status) {
                        $('#new-address-modal').modal('hide');
                        showFrontendAlert('success', res.message);
                        form.reset();
                        if ($('#add-address-box').length) {
                            $(res.html).insertBefore('#add-address-box');
                        } else {
                            $('#address-list').append(res.html);
                        }
                        if (res.count >= 6) {
                            $('#add-address-box').remove();
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('.' + key + '_error').text(value[0]);
                            $('[name="' + key + '"]').addClass('is-invalid');
                        });
                    }
                }
            });
        });
    </script>
@endsection
