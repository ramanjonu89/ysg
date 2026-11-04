<div class="modal fade delivery-popup" id="delivery-address-popup" tabindex="-1" aria-labelledby="shipping-popup-label"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shipping-popup-label">{{ __('Delivery Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                            size="25" color="#999999" style="color: rgb(153, 153, 153);" height="25"
                            width="25" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z">
                            </path>
                        </svg>
                    </span>
                </button>
            </div>
            <div id="delivery-address">
                <div class="modal-body border-bottom">
                    <form id="store-shipping" class="form-default">
                        <div class="row gutters-5 delivery-address-option">
                            @include('frontend.inc.delivery.option_delivery_address')
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-styled btn-default border rounded mr-1"
                        data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button class="btn btn-styled btn-base-1 border rounded confirm-delivery-address"
                        disabled>{{ __('Confirm') }}</button>
                </div>
            </div>
            <div class="d-none" id="add-delivery-address">
                <script
                    src="https://maps.googleapis.com/maps/api/js?key={{ env('GMAP_API_KEY') }}&callback=initAutocomplete&libraries=places&v=weekly"
                    defer>
                </script>
                <script>
                    var placeSearch;
                    var autocomplete;
                    var componentForm = {
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
                <form id="form_add_option_address" class="" role="form" action="" method="">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="_method" value="">
                    <div class="modal-body border-bottom">
                        <div class="row">
                            <div class="col-md-3">
                                <label>{{ __('Title') }}</label>
                            </div>
                            <div class="col-md-9 mb-3">
                                <input type="text" class="form-control"
                                            placeholder="{{ __('Title') }}" name="title" required></input>
                                <small class="text-danger error-text title_error"></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label>{{ __('Shipping Address') }}</label>
                            </div>
                            <div class="col-sm-9 mb-3">
                                <textarea class="form-control textarea-autogrow" placeholder="{{ __('Your Address') }}"
                                    rows="2" name="address" required></textarea>
                                <small class="text-danger error-text address_error"></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label>{{ __('Unit No.') }}</label>
                            </div>
                            <div class="col-sm-9 mb-3">
                                <input type="text" class="form-control" placeholder="{{ __('e.g. 09-11') }}"
                                    name="unitno" value="" required>
                                <small class="text-danger error-text unitno_error"></small>
                            </div>
                        </div>
                        <div class="row" id="address">
                            @php
                                $countries = Session::get('countries');
                            @endphp
                            <div class="col-sm-3">
                                <label>{{ __('Country') }}</label>
                            </div>
                            <div class="col-sm-9 mb-3">
                                <select id="select-country" class="form-control selectpicker"
                                    data-placeholder="{{ __('Select your country') }}" name="country_id" required>
                                    @if (!empty($countries))
                                        @foreach ($countries as $key => $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <small class="text-danger error-text country_id_error"></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label>{{ __('City') }}</label>
                            </div>
                            <div class="col-sm-9 mb-3">
                                <select id="select-city" class="form-control selectpicker"
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
                        <div class="row">
                            <div class="col-sm-3">
                                <label>{{ __('Postal code') }}</label>
                            </div>
                            <div class="col-sm-9 mb-3">
                                <input type="text" class="form-control"
                                    placeholder="{{ __('Your Postal Code') }}" id="postal_code" name="postal_code"
                                    value="" required>
                                <small class="text-danger error-text postal_code_error"></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label>{{ __('Phone') }}</label>
                            </div>
                            <div class="col-md-9 mb-3 phone-number">
                                <input type="hidden" class="country_code" name="country_code" value="">
                                <input type="tel" id="phone-code" class="h-auto form-control phone-init"
                                        value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off" required>
                                <small class="text-danger error-text phone_error"></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-styled btn-default border rounded mr-1 btn-cancel-add-address">{{ __('Cancel') }}</button>
                        <button type="button" id="btn_add_delivery_address"
                            class="btn btn-styled btn-base-1 border rounded">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
