@php
    $address = Session::get('address');
@endphp
@if (!empty($address))
    <span>
        <span class="d-flex p-3">
            <input type="hidden" id="address_id" name="address_id" value="{{ $address->id }}">
            <span class="flex-grow-1">
                <span class="strong-500">{{ $address->title }}</span><br>
                <span class="strong-500">{{ $address->address }}</span><br>
                <span class="strong-500">{{ $address->postal_code }}</span><br>
                <span
                    class="strong-500">{{ isset($address->cityRelationship) && isset($address->cityRelationship->name) ? $address->cityRelationship->name : '' }}</span><br>
                <span
                    class="strong-500">{{ isset($address->countryRelationship) && isset($address->countryRelationship->name) ? $address->countryRelationship->name : '' }}</span><br>
                <span class="strong-500">{{ $address->fullPhone() }}</span><br>
            </span>
        </span>
    </span>
@else
    <span class="">
        <div class="add-new-delivery-address">
            <i class="la la-plus la-2x"></i>
            <div class="alpha-7">{{ __('Add New Address') }}</div>
        </div>
    </span>
@endif
