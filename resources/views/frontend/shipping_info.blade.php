<div class="card-body pb-0 border-bottom shipping-info-container">
    <form id="" class="form-default" data-toggle="validator" action="" role="form" method="POST">
        <div class="address-cart-details">
            <table class="table-cart">
                <tbody>
                    <tr class="border-bottom-0">
                        <td class="delivery-address">
                            <div class="d-flex align-items-center justify-content-between">
                                <strong class="strong-600 heading-6">{{ __('Delivery Address:') }}</strong>
                                <span class="btn-change change-delivery-address"
                                    role="button">{{ __('Change') }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="">
                        <td class="pt-0 pb-0">
                            <span id="delivery_address_default">
                                @include('frontend.inc.delivery.address_selected')
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>
@include('frontend.inc.js.delivery_address')
@include('frontend.inc.js.phone-input')
