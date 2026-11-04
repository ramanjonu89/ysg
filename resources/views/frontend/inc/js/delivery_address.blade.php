@section('script')
    @parent
    <script type="text/javascript">
        $(document).on('change', '#select-country', function (e, selectedCityId = null) {
            getCities($(this).val(), '#select-city', selectedCityId);
        });

        function getCities(country_id, citySelector, selectedCityId = null) {
            $.ajax({
                url: '/get-cities/' + country_id,
                success: function (res) {
                    if (res.status !== 200) return;

                    const $city = $(citySelector);

                    $city.html(res.html);
                    if (selectedCityId) {
                        $city.val(selectedCityId).trigger('change');
                    }
                }
            });
        }

        function getDeliveryAddresses(addressId = null) {
            $.post('{{ route('address.list_delivery_address') }}', {
                _token: '{{ csrf_token() }}',
                address_id: addressId
            }, function (data) {
                $('#delivery-address').show();
                $('#add-delivery-address').addClass('d-none');
                $('#delivery-address .delivery-address-option').html(data);

                toggleConfirmDeliveryButton();
            });
        }

        $(document).on('click', '.btn-cancel-add-address', function () {
            const address_id = $('input[name="address_id"]:checked').val();
            $('#delivery-address').show();
            $('#add-delivery-address').addClass('d-none');
            getDeliveryAddresses(address_id);
        });

        $(document).on('click', '#btn_add_delivery_address', function() {
            const address_id = $('input[name="address_id"]:checked').val();
            if ($("#form_add_option_address")[0].reportValidity()) {
                let form  = $('#form_add_option_address');
                let data = form.serialize();

                $.post(form.attr('action'), data)

                .done(function(response) {
                    if (response.status === 1 || response.status === true) {
                        $('#delivery-address').show();
                        $('#add-delivery-address').addClass('d-none');
                        $('#form_add_option_address .error-text').text('');
                        $('#form_add_option_address .form-control').removeClass('is-invalid');
                        $('#form_add_option_address')[0].reset();
                        getDeliveryAddresses(address_id);
                        if (response.delivery_address) {
                            updateCurrentDeliveryAddress(response.delivery_address.id)
                                .done(function (html) {
                                    $('#delivery_address_default').html(html);
                                });
                        }
                    }
                })

                .fail(function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        $('.is-invalid').removeClass('is-invalid');
                        $('.text-danger').text('');

                        $.each(errors, function(key, value) {
                            $('.' + key + '_error').text(value[0]);
                            $('#form_add_option_address [name="' + key + '"]').addClass('is-invalid');
                        });
                    }
                });
            }
        });

        $(document).on('click', '.btn-edit-address', function (e) {
            e.preventDefault();
            const address = $(this).data('address');
            addNewOptionAddress(address);
            setTimeout(function () {
                itiMain = initIntlPhoneInput("#phone-code");
            }, 50);
        });

        $(document).on('click', '.add-delivery-address', function (e) {
            e.preventDefault();
            addNewOptionAddress();
            setTimeout(function () {
                itiMain = initIntlPhoneInput("#phone-code");
            }, 50);
        });

        function addNewOptionAddress(address = null) {
            $('#delivery-address').hide();
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
                $('input[name=id]').val(address.id);

                const addressUpdateUrl = "{{ route('addresses.update', ':id') }}";
                $('#form_add_option_address').attr('action', addressUpdateUrl.replace(':id', address.id));
            } else {
                $('input[name=_method]').val('');
                $('input[name=country_code]').val('+65');
                $('#form_add_option_address')[0].reset();
                $('#form_add_option_address').attr('action', '{{ route('address.add_delivery_address') }}');
            }

            if (itiMain) {
                const dial = $('input[name=country_code]').val().replace('+', '');
                const matched = countryData.find(c => c.dialCode === dial);
                if (matched) {
                    itiMain.setCountry(matched.iso2);
                }
            }

            $('#add-delivery-address').removeClass('d-none');
        }

        $(document).on('change', 'input[name="address_id"]', function () {
            toggleConfirmDeliveryButton();
        });

        function toggleConfirmDeliveryButton() {
            const isChecked = $('input[name="address_id"]:checked').length > 0;
            $('.confirm-delivery-address').prop('disabled', !isChecked);
        }

        $(document).on('click', '.add-new-delivery-address', function() {
            $('#delivery-address-popup').modal('show');
            getDeliveryAddresses([])
        });

        $(document).on('click', '.confirm-delivery-address', function () {
            const address_id = $('input[name="address_id"]:checked').val();
            updateCurrentDeliveryAddress(address_id).done(function (data) {
                $('#delivery_address_default').html(data);
                $('#delivery-address-popup').modal('hide');
                $('#complete_order').removeClass('btn-disabled-next-step');
                $('.validation-address').addClass('d-none');
                showFrontendAlert('success', 'Update Address Delivery Successfully!');
                updateViewCart();
            });
        });

        function updateCurrentDeliveryAddress(address_id) {
            return $.post('{{ route('address.select_delivery_address') }}', {
                _token: '{{ csrf_token() }}',
                address_id: address_id
            });
        }

        $(document).on('click', '.change-delivery-address', function() {
            $('#delivery-address-popup').modal('show');
            const address_checked = $('#delivery_address_default #address_id').val();
            getDeliveryAddresses(address_checked);
        })

    </script>
@endsection

@section('css')
    @parent
    <style>
        .pac-container {
            z-index: 10000 !important;
            border-top: 0 !important;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
@endsection
