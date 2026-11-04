@section('script')
    @parent
    <script type="text/javascript">
        var allowedCountries = @json(
            \App\Models\Country::where('status', 1)
                ->get(['code', 'country_code'])
                ->mapWithKeys(function ($item) {
                    return [strtolower($item->code) => $item];
                })
        );

        var countryData = window.intlTelInputGlobals.getCountryData();

        for (var i = 0; i < countryData.length; i++) {
            var iso2 = countryData[i].iso2;
            if (allowedCountries[iso2]) {
                countryData[i].dialCode = allowedCountries[iso2].country_code.replace('+', '');
            }
        }

        function initIntlPhoneInput(selector) {
            var input = document.querySelector(selector);
            if (!input) return null;

            if (input._itiInstance) {
                input._itiInstance.destroy();
            }

            var itiInstance = intlTelInput(input, {
                separateDialCode: true,
                utilsScript: "{{ static_asset('frontend/js/intlTelutils.js') }}",
                onlyCountries: Object.keys(allowedCountries),
            });

            input._itiInstance = itiInstance;

            var hiddenInput = $(input).closest('form').find('input[name=country_code]');
            var defaultDial = '65';
            var dial = hiddenInput.val()
                ? hiddenInput.val().replace('+', '')
                : defaultDial;

            var matched = countryData.find(function (c) {
                return c.dialCode === dial;
            });

            if (matched) {
                hiddenInput.val('+' + matched.dialCode);
                itiInstance.setCountry(matched.iso2);
                hiddenInput.val('+' + matched.dialCode);
            }

            input.addEventListener("countrychange", function () {
                var country = itiInstance.getSelectedCountryData();
                hiddenInput.val('+' + country.dialCode);
            });

            return itiInstance;
        }


        var itiMain = initIntlPhoneInput("#phone-code");
        var itiProfile = initIntlPhoneInput("#phone-code-profile");
    </script>
@endsection


@section('css')
    @parent
    <style>
        .phone-number .iti {
            width: 100% !important;
        }

        .phone-number .phone-init {
            padding-left: 79px !important;
        }

        .is-invalid .invalid-feedback {
            display: block;
        }
    </style>
@endsection
