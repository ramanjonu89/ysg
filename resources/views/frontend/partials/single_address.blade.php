<div class="col-lg-6 address-item" id="address-item-{{ $address->id }}">
    <input type="hidden" name="id" value="{{ $address->id }}">
    <div class="border p-3 pr-5 rounded mb-3 position-relative">
        <div>
            <span class="alpha-6">{{ __('Title') }}:</span>
            <span class="strong-600 ml-2">{{ $address->title }}</span>
        </div>

        <div>
            <span class="alpha-6">{{ __('Address') }}:</span>
            <span class="strong-600 ml-2">{{ $address->address }}</span>
        </div>

        <div>
            <span class="alpha-6">{{ __('Postal Code') }}:</span>
            <span class="strong-600 ml-2">{{ $address->postal_code }}</span>
        </div>

        <div>
            <span class="alpha-6">{{ __('City') }}:</span>
            <span class="strong-600 ml-2">
                {{ optional($address->cityRelationship)->name }}
            </span>
        </div>

        <div>
            <span class="alpha-6">{{ __('Country') }}:</span>
            <span class="strong-600 ml-2">
                {{ optional($address->countryRelationship)->name }}
            </span>
        </div>

        <div>
            <span class="alpha-6">{{ __('Phone') }}:</span>
            <span class="strong-600 ml-2">{{ $address->fullPhone() }}</span>
        </div>

        @if ($address->set_default)
            <div class="position-absolute right-0 bottom-0 pr-2 pb-3">
                <span class="badge badge-primary bg-base-1">{{ __('Default') }}</span>
            </div>
        @endif

        <div class="dropdown position-absolute right-0 top-0">
            <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                <i class="la la-ellipsis-v"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-right">
                @if (!$address->set_default)
                    <a class="dropdown-item" href="{{ route('addresses.set_default', $address->id) }}">
                        {{ __('Make This Default') }}
                    </a>
                @endif
                <a class="dropdown-item d-block" href="#" data-toggle="modal" data-target="#editAddressModal" onclick="add_new_address({{ json_encode($address) }})">
                    {{ __('Edit') }}
                </a>
                <a class="dropdown-item" href="{{ route('addresses.destroy', $address->id) }}">
                    {{ __('Delete') }}
                </a>
            </div>
        </div>
    </div>
</div>
