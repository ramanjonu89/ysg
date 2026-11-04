<div class="form-default cart-details" id="cart-details-container">
    @if ($cart->getTotalItems() > 0)
        <div class="bg-white select-all-items">
            <table class="table-cart border-0">
                <thead>
                    <tr>
                        <th>
                            <div class="d-flex">
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" value="0" name="select-all" @if ($cart->getTotalItems() === $tempCart->getTotalItems()) checked @endif>
                                </div>
                            </div>
                        </th>
                        <th class="product-name" style="width:50%">{{ __('Product')}}</th>
                        <th class="product-price d-none d-lg-table-cell">{{ __('Price')}}</th>
                        <th class="product-quantity d-none d-md-table-cell">{{ __('Quantity')}}</th>
                        <th class="product-total">{{ __('Total')}}</th>
                        <th class="action">{{ __('Action')}}</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endif
    <div class="bg-white p-4">
        @foreach ($cart->getItemsBySellerGroup() as $sellerId => $seller)
            <div class="seller-group">
                <table class="table-cart border-bottom mb-2 seller-{{$sellerId}}" data-sellerID="{{ $sellerId}}">
                    <input type="hidden" id="seller-{{ $sellerId }}" class="order-type" name="order_type_{{ $sellerId }}" value="{{ $seller['info']['orderType'] }}">
                    <tbody>
                        <tr>
                            <td class="shop-cart-detail pt-0 select-item">
                                <div class="d-flex">
                                    <div class="form-check select-all-items-seller">
                                        <input class="form-check-input item" type="checkbox" data-offset-key="{{ $sellerId }}" value="" @if ($cart->getTotalItemsBySeller($sellerId) === $tempCart->getTotalItemsBySeller($sellerId)) checked @endif>
                                    </div>
                                    <a class="mr-1 mt-1" href="{{ route('shop.visit', $seller['info']['shopSlug'])}}">{{ $seller['info']['name'] }}</a>
                                </div>
                            </td>
                            @if ($seller['info']['orderType'] == \App\Models\Shop::ORDER_TYPE_BOTH)
                            <td>
                                <div class="form-group">
                                    <div>
                                        <label class="radio-inline">
                                            <input class="order_type" type="radio" name="order_type_{{$sellerId}}" value="{{ \App\Models\Shop::ORDER_TYPE_DELIVERY }}" @if (!$tempCart->isSelfCollectionShop($seller['info']['shopId'])) checked @endif > {{ __('Delivery') }}
                                        </label>
                                    </div>
                                    <div>
                                        <label class="radio-inline">
                                            <input class="order_type" type="radio" name="order_type_{{$sellerId}}" value="{{ \App\Models\Shop::ORDER_TYPE_SELF_COLLECTION }}" @if ($tempCart->isSelfCollectionShop($seller['info']['shopId'])) checked @endif> {{ __('Self Collection') }}
                                        </label>
                                    </div>
                                </div>
                            </td>
                            @endif
                            <td class="pt-0" width="50%">
                                <div class="text-right">
                                    <div class="select-address" @if (!$tempCart->isSelfCollectionShop($seller['info']['shopId']) && !$cart->isSelfCollectionShop($seller['info']['shopId'])) style="display: none" @endif>
                                        <div>
                                            <span class="badge bg-gray-light bg-base-6 span-order-type">
                                                {{ __('Self collection address:') }}
                                            </span>
                                        </div>
                                        <div>
                                            <select class="form-control mb-3 mt-3 shipping_address" data-placeholder="{{ __('Select an address') }}" id="addreses" name="shipping_address_{{$sellerId}}">
                                                @foreach ($seller['info']['addresses'] as $key => $address)
                                                <option value="{{ $key }}"
                                                    @if (!empty($tempCart->getShopAddressId($seller['info']['shopId'])) && $tempCart->getShopAddressId($seller['info']['shopId']) == $address->id)
                                                        selected
                                                    @elseif (empty($tempCart->getShopAddressId($seller['info']['shopId'])) && $address->set_default)
                                                        selected
                                                    @endif>
                                                    @if ($address->title)
                                                        [{{ $address->title }}] -
                                                    @endif
                                                    {{ $address->fullAddress() }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="shipping-fee" @if ($tempCart->isSelfCollectionShop($seller['info']['shopId']) || $cart->isSelfCollectionShop($seller['info']['shopId'])) style="display: none" @endif>
                                        <span
                                            class="badge bg-black bg-base-6 span-shippingfee span-order-type">
                                            Shipping Fee :<span class="ml-1"
                                                id="directshippingcost-{{$sellerId}}">{{ single_price($tempCart->getShippingFeeByShopId($seller['info']['shopId'])) }}</span>
                                        </span>
                                        @if ($tempCart->getShippingFeeByShopId($seller['info']['shopId']))
                                            <span class="message-shipping">{{ __('shipping.subtotal_remains_for_free_shipping', ['value' => single_price($tempCart->getSubTotalRemainsForFreeShippingByShopId($seller['info']['shopId']))]) }}</span>
                                        @endif
                                    </div>
                                    @foreach($cart->getCoupons() as $key => $coupon)
                                        @if ($coupon->getShopId() === $seller['info']['shopId'])
                                            <span class="voucher-of-store badge bg-orange">{{ $coupon->getCode() }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table-cart border-bottom mb-3">
                    <tbody>
                    @foreach ($seller['items'] as $key => $item)
                        @php
                            $currentStock = $item->getCurrentStock();
                            $isTempCartItem = $tempCart->hasItem($item->getItemAction());
                        @endphp
                        <tr class="cart-item @if(!$cart->isValidCartQuantity($item)) in-valid @endif">
                            <td class="select-item">
                                <div class="form-check">
                                    <input class="form-check-input select-item"
                                            name="{{ $item->getItemAction() }}" type="checkbox"
                                            data-offset-key="{{ $item->getItemAction() }}"
                                            value="{{ $item->getQuantity() }}"
                                            {{ $isTempCartItem ? 'checked' : '' }}
                                            {{ $cart->isValidCartQuantity($item) ? '' : 'disabled' }}>
                                </div>
                            </td>
                            <td class="product-image d-none d-md-table-cell">
                                <a href="{{ route('product', $item->getSlug()) }}" class="mr-3">
                                    <img loading="lazy"  src="{{ my_asset($item->getThumbnailImg()) }}">
                                </a>
                            </td>
                            <td class="product-name" style="width:45%">
                                <span class="pr-4 d-block ">{{ getProductTranslationNameAttributeWithId($item->getId()) }}
                                    @if (!empty($item->getVariation()))
                                        ({{ $item->getVariation() }})
                                    @endif
                                </span>
                                <div class="order-type-badge d-md-none mt-2">
                                    <div class="self-collection" style="width: 115px; display:none">
                                        <span class="self_collection_label order-type-mobile">
                                            {{ __('Self-Collection') }}
                                        </span>
                                    </div>
                                    <div class="home-delivery" style="width: 115px; display:none">
                                        <span class="home_delivery_label">
                                            {{ __('Delivery') }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="product-price d-none d-lg-table-cell">
                                <span class="pr-3 d-block">{{ single_price($item->getPrice()) }}</span>
                            </td>
                            <td class="product-quantity d-none d-md-table-cell">
                                <div class="input-group input-group--style-2 pr-4" style="width: 130px;">
                                    <span class="input-group-btn">
                                        <button id="btn-minus-{{$item->getItemAction()}}"
                                                data-qty="{{ $item->getQuantity() }}" type="button"
                                                class="btn btn-number {{ $item->validateProductAvailability() ? 'bg-white' : '' }}"
                                                data-type="minus" data-field="quantity['{{ $item->getItemAction() }}']"
                                                onclick="confirmToDetele(event, '{{ getProductTranslationNameAttributeWithId($item->getId()) }}', this)"
                                                {{ $item->validateProductAvailability() ? '' : 'disabled' }}
                                        >
                                            <i class="la la-minus"></i>
                                        </button>
                                    </span>
                                    <input readonly type="text" name="quantity['{{ $item->getItemAction() }}']" class="form-control h-auto input-number" placeholder="1"
                                            value="{{  $item->getQuantity() }}" min="1" max="{{ $currentStock }}" onchange="updateQuantity('{{ $item->getItemAction() }}', this)">
                                    <span class="input-group-btn">
                                        <button id="btn-plus-{{$item->getItemAction()}}"
                                            data-qty="{{ $item->getQuantity() }}" type="button"
                                            class="btn btn-number {{ $item->validateProductAvailability() ? 'bg-white' : '' }}"
                                            data-type="plus" data-field="quantity['{{ $item->getItemAction() }}']"
                                            {{ $item->validateProductAvailability() ? '' : 'disabled' }}
                                        >
                                            <i class="la la-plus"></i>
                                        </button>
                                    </span>
                                </div>
                                <div class="mt-1 float-left">
                                    @if(!$tempCart->validatePurchaseRules($item)['valid'] && $isTempCartItem)
                                        <span class="message-purchase">{{ $tempCart->validatePurchaseRules($item)['message'] }}</span>
                                    @endif
                                </div>
                                @if (!$item->validateProductAvailability())
                                    <small id="emailHelp" class="text-danger "> {{ __('Product Not Available') }}</small>
                                @elseif($currentStock < 10)
                                    <small id="emailHelp" class="text-danger ">{{ trans_choice('Item Remaining text', $currentStock, ['value' => $currentStock]) }}</small>
                                @endif
                            </td>
                            <td class="product-total-action">
                                <div class="d-flex justify-content-between">
                                    <span>{{ single_price($item->getTotalPrice()) }}</span>
                                    <div class="product-remove">
                                        <a href="#" onclick="removeFromCartView(event, '{{ $item->getItemAction() }}')" class="text-right pr-4">
                                            <i class="la la-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="order-type-label d-none d-md-table-cell">
                                <div class="self-collection" style="width: 115px; display:none">
                                    <span class="self_collection_label">
                                        {{ __('Self-Collection') }}
                                    </span>
                                </div>
                                <div class="home-delivery" style="width: 115px; display:none">
                                    <span class="home_delivery_label">
                                        {{ __('Delivery') }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="d-flex mb-3 border-bottom-voucher-store pb-3">
                    @if (!empty($coupons[$seller['info']['shopId']]))
                        <strong class="mr-1">{{ __('Store Voucher')}} :</strong>
                        <div>
                            @foreach(array_slice($coupons[$seller['info']['shopId']], 0, 1) as $coupon)
                                <span
                                    class="badge bg-orange">{{ $coupon->discount_label }} OFF</span>
                            @endforeach
                            <span data-toggle="modal" data-target="#{{$sellerId}}" role="button" class="badge bg-orange">
                                {{ __('More') }}
                                    <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i>
                                </span>
                        </div>
                        @include('frontend.inc.coupon.popup', ['modalId' => $sellerId, 'coupons' => $coupons[$seller['info']['shopId']], 'canApply' => true])
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div class="row align-items-center p-3">
        <div class="col-md-12 text-right">
            <a href="{{ getAppHomePageLink() }}" class="link link--style-3">
                <i class="la la-mail-reply"></i>
                <strong>{{ __('Return to Home') }}</strong>
            </a>
        </div>
    </div>
</div>
<!-- </form> -->
<div id="section_best_selling" style="padding-top:20px;">
</div>
@include('frontend.inc.delivery.popup')
@include('frontend.inc.coupon.popup_by_admin')
<script type="text/javascript">
    $(function() {
        $('#next-step-button').click(function () {
            $.LoadingOverlay("show", {
                size: '75px',
                image: "",
                fontawesome: "fa fa-refresh fa-spin"
            });

            setTimeout(function() {
                $.LoadingOverlay("hide");
            }, 700);
        });

        cartQuantityInitialize();

        $('input.select-item').change(function() {
            loadingOverlayShow()
            const orderType = $(this).closest('.seller-group').find('.order_type:checked').val();
            const offsetKey = $(this).data('offset-key');
            const shippingAddress = $(this).closest('.seller-group').find('.shipping_address').val();
            if ($(this).is(':checked')) {
                $.post('{{ route('cart.pickFromCart') }}', {
                    _token: '{{ csrf_token() }}',
                    item_offset: offsetKey,
                    order_type: orderType,
                    shipping_address:shippingAddress
                }, function (data) {
                    updateViewCart();
                });
            } else {
                $.post('{{ route('cart.unPickFromCart') }}', {_token: '{{ csrf_token() }}', item_offset: offsetKey}, function (data) {
                    updateViewCart();
                });
            }
        });

        $('.select-all-items input').change(function() {
            loadingOverlayShow()
            const status = (this.checked) ? 1 : 0;
            const $cart = $(this).closest('.cart-details');
            $cart.find('.select-item input').prop('checked', status);
            const orderTypeSeller = {};

            $cart.find('.seller-group').each(function() {
                const sellerId = $(this).find('table').data('sellerid');
                let orderType = $(this).find('.order_type:checked').val() || $(this).find('.order-type').val();

                orderTypeSeller[sellerId] = orderType;
            });

            $.post('{{ route('cart.select_all_from_cart') }}', {
                _token: '{{ csrf_token() }}',
                status: status,
                order_type_seller: orderTypeSeller
            }, function(data) {
                updateViewCart();
            });

        });

        $('.select-all-items-seller input').change(function() {
            const status = (this.checked) ? 1 : 0;
            const $cart = $(this).closest('.seller-group');
            const orderType = $cart.find('.order_type:checked').val();
            const shippingAddress = $cart.find('.shipping_address').val();

            $cart.find('.cart-item .select-item input').prop('checked', status);

            loadingOverlayShow()

            const offsetKey = $(this).data('offset-key');
            const data = {
                _token: '{{ csrf_token() }}',
                status: status,
                seller_id: offsetKey,
                order_type_seller: {},
                shipping_address: {}
            };

            data['order_type_seller'][offsetKey] = orderType;
            data['shipping_address'][offsetKey] = shippingAddress;

            $.post('{{ route('cart.select_all_from_cart') }}', data , function (data) {
                updateViewCart();
            });
        });
    });

    function loadingOverlayShow(){
        $.LoadingOverlay("show", {
            size: '75px',
            image: "",
            fontawesome: "fa fa-refresh fa-spin"
        });
    }

    $(document).ready(function() {
        $('.seller-group').each(function() {

            let orderType = $(this).find('.order_type:checked').val() || $(this).find('.order-type').val();

            if (orderType == '{{ \App\Models\Shop::ORDER_TYPE_SELF_COLLECTION }}') {
                $(this).find('.self-collection').show();
                $(this).find('.home-delivery').hide();
            } else {
                $(this).find('.home-delivery').show();
                $(this).find('.self-collection').hide();
            }
        });
    });

    function showCheckoutModal(){
        $('#GuestCheckout').modal();
    }

    function updateDeliveryInfo(element) {
        const loadingHtml = $('#cart-summary-loading')[0].outerHTML;
        $('#cart-summary-loading').removeClass('d-none');
        // get checked radio button value
        var orderType = element.closest('table').find('.order_type:checked').val();
        if (orderType == undefined) {
            orderType = 2;
        }
        var sellerId = element.closest('table').data('sellerid');
        if (orderType == '{{ \App\Models\Shop::ORDER_TYPE_SELF_COLLECTION }}') {
            element.closest('table').find('.select-address').show();
            element.closest('table').find('.shipping-fee').hide();
            element.closest('table').next('table').find('.self-collection').show();
            element.closest('table').next('table').find('.home-delivery').hide();
        } else {
            element.closest('table').find('.select-address').hide();
            element.closest('table').find('.shipping-fee').show();
            element.closest('table').next('table').find('.home-delivery').show();
            element.closest('table').next('table').find('.self-collection').hide();
        }
        document.cookie = `order_type_${sellerId}=${orderType}`;

        if (orderType == '{{ \App\Models\Shop::ORDER_TYPE_SELF_COLLECTION }}') {
            // radio changed to selectbox, update the finding address
            var address = element.closest('table').find('.select-address').find('select').val();
            document.cookie = `address_${sellerId}=${address}`;
        }

        const items = element.closest('.seller-group').find('.cart-item .select-item input');
        const hasCheckedItem = items.filter(':checked').length;

        if (hasCheckedItem) {
            $.ajax({
                url: '/cart/updateDeliveryInfo',
                type: 'GET',
                success: function(response) {
                    // reloadCartSummary();
                    updateViewCart();
                },
                error: function(xhr) {
                    console.log('Error updating delivery info:', xhr);
                },
                complete: function() {
                    // $('#cart-summary-loading').addClass('d-none');
                }
            });
        } else {
            $('#cart-summary-loading').addClass('d-none');
        }
    }

    $('input.order_type').change(function() {
        updateDeliveryInfo($(this));
    });

    $('.shipping_address').change(function() {
        updateDeliveryInfo($(this));
    });

    function reloadCartSummary() {
        const loadingHtml = $('#cart-summary-loading')[0].outerHTML;

        $.ajax({
            url: '/cart/summary',
            type: 'GET',
            success: function(response) {
                $('#cart-summary-container').html(loadingHtml + response);
            },
            error: function(xhr) {
                console.log('Error reloading cart summary:', xhr);
            },
            complete: function() {
                $('#cart-summary-loading').addClass('d-none');
            }
        });
    }
</script>
@foreach (session('flash_notification', collect())->toArray() as $message)
    <script>
        showFrontendAlert('{{ $message['level'] }}', '{!! $message['message'] !!}');
    </script>
@endforeach

<style>
    .self_collection_label,
    .home_delivery_label {
        color: #fff;
        background-color: #fab315;
        border-radius: 10px;
        padding: 10px;
    }

    .self-collection {
    }

    .span-order-type {
        font-size: 0.85rem;
    }

    td.select-item .form-check {
        width: 1.5rem;
        height: 1.5rem;
        margin-right: 1.5rem;
    }

    td.select-item input,
    .select-all-items .form-check-input {
        width: 1.5rem;
        height: 1.5rem;
    }

    .form-check-input {
        margin-top: 0;
    }

    .select-all-items {
        padding: 1rem 1rem 1rem 1.5rem;
        margin-bottom: 0.5rem;
    }

    .select-all-items thead > tr > th {
        border: 0;
        padding: 0;
    }
    .in-valid {
        background: rgba(0, 0, 0, .02);
    }
    .in-valid td {
        color:#787887 !important;
    }

    .in-valid {
        background: rgba(0, 0, 0, .02);
    }
    .in-valid td {
        color:#787887 !important;
    }

    .order-type-badge span {
        padding: 5px !important;
        font-size: smaller;
    }
</style>
