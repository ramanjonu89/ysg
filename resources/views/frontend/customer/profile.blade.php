@extends('frontend.layouts.app')
@section('content')
    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @if (Auth::user()->user_type == 'seller')
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
                                        @include('frontend.inc.breadcrumb', [
                                            'breadcrumbs' => [
                                                [
                                                    'link' => route('dashboard'),
                                                    'text' => __('Dashboard'),
                                                ],
                                                [
                                                    'link' => route('profile'),
                                                    'text' => __('Manage Profile'),
                                                ],
                                            ],
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form class="" action="{{ route('customer.profile.update') }}" method="POST"
                            enctype="multipart/form-data">
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
                                        <div class="col-md-10 mb-3">
                                            <input type="text" class="form-control"
                                                placeholder="{{ __('Your Name') }}" name="name"
                                                value="{{ Auth::user()->name }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label>{{ __('Your Phone') }}</label>
                                        </div>
                                        <div class="col-md-10 phone-number">
                                            <input type="hidden" class="country_code" name="country_code" value="{{ Auth::user()->country_code }}">
                                            <input type="tel" id="phone-code-profile"
                                                    class="h-auto w-100 form-control @error('phone') is-invalid @enderror"
                                                    value="{{ Auth::user()->phone }}" placeholder="" name="phone" autocomplete="off"  required
                                                    >
                                            @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Your Password') }}</label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                                placeholder="{{ __('New Password') }}" name="new_password">
                                            @error('new_password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ __('Confirm Password') }}</label>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <input type="password" class="form-control @error('confirm_password') is-invalid @enderror"
                                                placeholder="{{ __('Confirm Password') }}" name="confirm_password"
                                                >
                                            @error('confirm_password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right mt-4">
                                <button type="submit"
                                    class="btn btn-styled btn-base-1">{{ __('Update Profile') }}</button>
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
                                        @if (Auth::user()->addresses->count() < 6)
                                            <div class="col-lg-6 mx-auto" id="add-address-box" onclick="add_new_address()">
                                                <div class="border p-3 rounded mb-3 c-pointer text-center bg-light">
                                                    <i class="la la-plus la-2x"></i>
                                                    <div class="alpha-7">{{ __('Add New Address') }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
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
                                                <input type="email" class="form-control"
                                                    placeholder="{{ __('Your Email') }}" name="email"
                                                    value="{{ Auth::user()->email }}" />
                                                <div class="input-group-append">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary new-email-verification">
                                                        <span class="d-none loading">
                                                            <span class="spinner-border spinner-border-sm" role="status"
                                                                aria-hidden="true"></span>
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
                        <div class="text-right mt-3">
                            <form action="{{ route('customer.profile.destroy', ['id' => Auth::user()->id]) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-styled btn-danger">{{ __('Delete Account') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="new-address-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GMAP_API_KEY') }}&callback=initAutocomplete&libraries=places&v=weekly"
                defer></script>
        <script>
            let placeSearch;
            let autocomplete;
            const componentForm = {
                postal_code: "short_name",
            };

            function initAutocomplete() {
                // Create the autocomplete object, restricting the search predictions to
                // geographical location types.
                autocomplete = new google.maps.places.Autocomplete(
                    document.getElementById("autocomplete"), {
                        types: ["geocode"]
                    }
                );
                // Avoid paying for data that you don't need by restricting the set of
                // place fields that are returned to just the address components.
                autocomplete.setFields(["address_component"]);
                // When the user selects an address from the drop-down, populate the
                // address fields in the form.
                autocomplete.addListener("place_changed", fillInAddress);
            }

            function fillInAddress() {
                // Get the place details from the autocomplete object.
                const place = autocomplete.getPlace();
                for (const component in componentForm) {
                    document.getElementById(component).value = "";
                    document.getElementById(component).disabled = false;
                }
                // Get each component of the address from the place details,
                // and then fill-in the corresponding field on the form.
                for (const component of place.address_components) {
                    const addressType = component.types[0];
                    if (componentForm[addressType]) {
                        const val = component[componentForm[addressType]];
                        document.getElementById(addressType).value = val;
                    }
                }
            }
            // Bias the autocomplete object to the user's geographical location,
            // as supplied by the browser's 'navigator.geolocation' object.
            function geolocate() {
                console.log("lol");
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        const geolocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        const circle = new google.maps.Circle({
                            center: geolocation,
                            radius: position.coords.accuracy,
                        });
                        autocomplete.setBounds(circle.getBounds());
                    });
                }
            }
        </script>
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ __('New Address') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="" role="form" method="POST" id="address-form">
                    @csrf
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="_method" value="">
                    <div class="modal-body">
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{ __('Title') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control mb-3"
                                                placeholder="{{ __('Title') }}" name="title"></input>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{ __('Shipping Address') }}</label>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <textarea class="form-control textarea-autogrow" placeholder="{{ __('Your Address') }}"
                                        rows="2" name="address" required></textarea>
                                    <small class="text-danger error-text address_error"></small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{ __('Unit No.') }}</label>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <input type="text" class="form-control" placeholder="{{ __('e.g. 09-11') }}"
                                        name="unitno" value="" required>
                                <small class="text-danger error-text unitno_error"></small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{ __('Country') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <select id="select-country" class="form-control mb-3 selectpicker"
                                            data-placeholder="{{ __('Select your country') }}" name="country_id"
                                            required>
                                            @foreach (\App\Models\Country::where('status', 1)->orderBy('name', 'asc')->get() as $key => $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger error-text country_id_error"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{ __('City') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                    <select id="select-city" class="form-control mb-3 selectpicker"
                                        data-placeholder="{{ __('Select Your City') }}" name="city_id" required>
                                        @if (isset($cities) && !empty($cities))
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <small class="text-danger error-text city_id_error"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{ __('Postal code') }}</label>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <input type="text" class="form-control"
                                        placeholder="{{ __('Your Postal Code') }}" id="postal_code" name="postal_code"
                                        value="{{ old('postal_code') }}" required>
                                    <small class="text-danger error-text postal_code_error"></small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{ __('Phone') }}</label>
                                </div>
                                <div class="col-md-8 mb-3 phone-number">
                                    <input type="hidden" class="country_code" name="country_code" value="">
                                    <input type="tel" id="phone-code" class="h-auto form-control phone-init"
                                            value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off" required>
                                    <small class="text-danger error-text phone_error"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
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
        function add_new_address(address = null) {
            if (address) {
                $('input[name=id]').val(address.id);
                $('input[name=title]').val(address.title);
                const fullAddress = address.address;
                const lastSpaceIndex = fullAddress.lastIndexOf(' ');
                const mainAddress = fullAddress.substring(0, lastSpaceIndex);
                const unitNo = fullAddress.substring(lastSpaceIndex + 1);

                $('textarea[name=address]').val(mainAddress);
                $('input[name=unitno]').val(unitNo);

                $('select[name=country_id]').val(address.country_id).trigger('change', address.city_id);

                $('input[name=postal_code]').val(address.postal_code);
                $('input[name=country_code]').val(address.country_code ?? '+65');
                $('input[name=phone]').val(address.phone);

                $('input[name=_method]').val('PATCH');
                const addressUpdateUrl = "{{ route('addresses.update', ':id') }}";
                $('#address-form').attr('action', addressUpdateUrl.replace(':id', address.id));
            } else {
                $('#address-form')[0].reset();
                $('input[name=country_code]').val('+65');
                $('input[name=_method]').val('');
                $('#address-form').attr('action', '{{ route('addresses.store') }}');
            }

            if (itiMain) {
                const dial = $('input[name=country_code]').val().replace('+', '');
                const matched = countryData.find(c => c.dialCode === dial);
                if (matched) {
                    itiMain.setCountry(matched.iso2);
                }
            }

            $('#new-address-modal').modal('show');
        }
        $('.new-email-verification').on('click', function() {
            $(this).find('.loading').removeClass('d-none');
            $(this).find('.default').addClass('d-none');
            var email = $("input[name=email]").val();
            $.post('{{ route('user.new.verify') }}', {
                _token: '{{ csrf_token() }}',
                email: email
            }, function(data) {
                data = JSON.parse(data);
                $('.default').removeClass('d-none');
                $('.loading').addClass('d-none');
                if (data.status == 2)
                    showFrontendAlert('warning', data.message);
                else if (data.status == 1)
                    showFrontendAlert('success', data.message);
                else
                    showFrontendAlert('danger', data.message);
            });
        });

        $(document).on('change', '#select-country', function (e, selectedCityId = null) {
            getCities($(this).val(), '#select-city', selectedCityId);
        });


        function getCities(countryId, citySelector, selectedCityId = null) {
            $.ajax({
                url: '/get-cities/' + countryId,
                success: function (res) {
                    if (res.status !== 200) return;

                    const $city = $(citySelector);

                    $city.html(res.html);

                    if (selectedCityId) {
                        $city.val(selectedCityId).trigger('change');
                    } else {
                        $city.val(null).trigger('change');
                    }
                }
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
                        if (res.address_id && $('#address-item-' + res.address_id).length) {
                            $('#address-item-' + res.address_id).replaceWith(res.html);
                            return;
                        }
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
<style>
    .pac-container {
        z-index: 10000 !important;
    }

    .phone-number .invalid-feedback {
        display: block !important;
    }
</style>
