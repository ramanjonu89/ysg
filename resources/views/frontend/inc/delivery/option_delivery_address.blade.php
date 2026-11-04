@if (!empty($addresses))
    @foreach ($addresses as $key => $address)
        <div class="col-md-6">
            <label class="aiz-megabox d-block bg-white">
                <input type="radio" name="address_id" value="{{ $address->id }}"
                    @if ($address->id == $selectedAddress) checked @endif required>
                <span class="d-flex p-3 aiz-megabox-elem">
                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                    <span class="flex-grow-1 pl-3">
                        <div>
                            <span class="alpha-6">{{ __('Title') }}:</span>
                            <span class="strong-600 ml-2">{{ $address->title }}</span>
                        </div>
                        <div>
                            <span class="alpha-6">{{ __('Street Name') }}:</span>
                            <span class="strong-600 ml-2">{{ $address->address }}</span>
                        </div>
                        <div>
                            <span class="alpha-6">{{ __('Postal Code') }}:</span>
                            <span class="strong-600 ml-2">{{ $address->postal_code }}</span>
                        </div>
                        <div>
                            <span class="alpha-6">{{ __('City') }}:</span>
                            <span
                                class="strong-600 ml-2">{{ isset($address->cityRelationship) && isset($address->cityRelationship->name) ? $address->cityRelationship->name : '' }}</span>
                        </div>
                        <div>
                            <span class="alpha-6">{{ __('Country') }}:</span>
                            <span
                                class="strong-600 ml-2">{{ isset($address->countryRelationship) && isset($address->countryRelationship->name) ? $address->countryRelationship->name : '' }}</span>
                        </div>
                        <div>
                            <span class="alpha-6">{{ __('Phone') }}:</span>
                            <span class="strong-600 ml-2">{{ $address->fullPhone() }}</span>
                        </div>
                    </span>
                    <span>
                        <a href="#" class="btn btn-change strong-600 pt-0 btn-edit-address" data-address="{{ $address }}">Edit</a>
                    </span>
                </span>
            </label>
        </div>
    @endforeach
    @if($addresses->count() < 6)
        <div class="col-md-6 mx-auto add-delivery-address">
            <div class="border p-3 rounded mb-3 c-pointer text-center bg-white">
                <i class="la la-plus la-2x"></i>
                <div class="alpha-7">{{ __('Add New Address') }}</div>
            </div>
        </div>
    @endif
@endif
