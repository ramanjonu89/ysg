<div class="card sticky-top">
    <div class="card-title py-3">
        <div class="row align-items-center">
            <div class="col-6">
                <h3 class="heading heading-3 strong-400 mb-0">
                    <span>{{ __('Summary') }}</span>
                </h3>
            </div>
            <div class="col-6 text-right">
                @php
                    $count = 0 ;
                    foreach (Session::get('cart') as $key => $cartItem){
                    $count +=  $cartItem['quantity'];
                    }
                @endphp
                <span class="badge badge-md badge-success">{{ $count }} {{ __('Items') }}</span>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
            @php
                $total_point = 0;
            @endphp
            @foreach (Session::get('cart') as $key => $cartItem)
                @php
                    $product = \App\Product::find($cartItem['id']);
                    $total_point += $product->earn_point*$cartItem['quantity'];
                @endphp
            @endforeach
            <div class="club-point mb-3 bg-soft-base-1 border-light-base-1 border">
                {{ __("Total Club point") }}:
                <span class="strong-700 float-right">{{ $total_point }}</span>
            </div>
        @endif
        <table class="table-cart table-cart-review">
            <thead>
            <tr>
                <th class="product-name">{{ __('Product') }}</th>
                <th class="product-total text-right">{{ __('Total') }}</th>
            </tr>
            </thead>
            <tbody>
            @php
                $subtotal = 0;
                $tax = 0;
                $shipping = 0;
                $shipmentcartotal = 0;
                $Pshipmentcartotal = 0;
                $Pshipping = 0;
                 $seller = [];
            @endphp
            @foreach (Session::get('cart') as $key => $cartItem)
                @php
                    $product = \App\Product::find($cartItem['id']);
                    $seller[] =array(
                    \App\User::where('id', $product->user_id)->first()->id,
                    $cartItem['price']*$cartItem['quantity'],
                    );
                @endphp
            @endforeach
            @php
                $result = [];
                foreach ($seller as $node) {
                if (isset($result[$node[0]])) {
                $result[$node[0]] = ['id' => $node[0],'sum' => $result[$node[0]]['sum'] + $node[1], 'count' => $result[$node[0]]['count'] + 1];
                } else {
                $result[$node[0]] = ['id' => $node[0],'sum' => $node[1], 'count' => 1];
                }
                }
              $combinetogether = 0;
                $combinedbefore = 0;
                foreach ($result as $node => $myvalue) {
                        if (\App\User::where('id', $myvalue['id'])->first()->company_shipping == 2) {
                            if ((\App\Shop::where('user_id', $myvalue['id'])->first()->shipping_cost_above) <= $myvalue['sum']){
                            // echo "shipping - 0";
                            }else{
                            $combinetogether +=\App\Shop::where('user_id', $myvalue['id'])->first()->shipping_cost;
                             //echo "shipping - ". \App\Shop::where('user_id', $myvalue['id'])->first()->shipping_cost;
                            }
                        }else{
                           if ($combinedbefore == 0){
                           $combinedbefore = 1;
                           //echo "im here";
                            $combinetogether +=2;
                           }
                        }
                }
            @endphp
            @foreach (Session::get('cart') as $key => $cartItem)
                @php
                    $product = \App\Modeld\Product::find($cartItem['id']);
                    $subtotal += $cartItem['price']*$cartItem['quantity'];
                    if (array_key_exists($product->user_id, $result)) {
                    $subtotal1 =  $result[$product->user_id]['sum'];
                    }
                    if (\App\User::where('id', $product->user_id)->first()->company_shipping == 2) {
                    if ((\App\Shop::where('user_id', $product->user_id)->first()->shipping_cost_above) <= $subtotal1){
                    $Pshipping = 0;
                @endphp
                <script>
                    $('#directshippingcost-'+{{$product->user_id}}).html(' FREE');
                </script>
                @php
                    }else{
                        $Pshipping = \App\Shop::where('user_id', $product->user_id)->first()->shipping_cost;
                         //$combinetogether += $Pshipping;
                    }
                        }else{
                        $shipmentcartotal += $cartItem['price']*$cartItem['quantity'];
                        }
                        $product_name_with_choice = $product->translation_name;
                        if ($cartItem['variant'] != null) {
                        $product_name_with_choice = $product->translation_name.' - '.$cartItem['variant'];
                        }
                @endphp
                <tr class="cart_item">
                    <td class="product-name">
                        {{ $product_name_with_choice }}
                        <strong class="product-quantity">× {{ $cartItem['quantity'] }}</strong>
                    </td>
                    <td class="product-total text-right">
                        <span class="pl-4">{{ single_price($cartItem['price']*$cartItem['quantity']) }}</span>
                    </td>
                </tr>
            @endforeach
            @php
                $shipping += $Pshipping;
               if ($shipmentcartotal >= 60 && $shipmentcartotal>0 )
                    {
                    $combinetogether -= 2;
                }
                   $shipping =$combinetogether;
                     Session::put('shipping',$shipping);
            @endphp
            </tbody>
        </table>
        <div id="ysgcombineshippingcart" style="display:none">{{$shipmentcartotal}}</div>
        <div id="Pshipping" style="display:none">{{$Pshipmentcartotal}}</div>
        <table class="table-cart table-cart-review">
            <tfoot>
            <tr class="cart-subtotal">
                <th>{{ __('Subtotal') }}</th>
                <td class="text-right">
                    <span class="strong-600">{{ single_price($subtotal) }}</span>
                </td>
            </tr>
            <!-- <tr class="cart-shipping">
                            <th>{{ __('GST') }}</th>
                            <td class="text-right">
                            <span class="text-italic">{{ single_price($tax) }}</span>
                            </td>
                        </tr>-->
            <tr class="cart-shipping">
                <th>{{ __('Shipping Fee') }}</th>
                <td class="text-right">
                    <span class="text-italic">{{ single_price($shipping) }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="notice text-center" style="display:none"></span>
                </td>
            </tr>
            @if (Session::has('coupon_discount'))
                <tr class="cart-shipping">
                    <th>{{ __('Coupon Discount') }}</th>
                    <td class="text-right">
                        <span class="text-italic">{{ single_price(Session::get('coupon_discount')) }}</span>
                    </td>
                </tr>
            @endif
            @php
                $total = $subtotal+$tax+$shipping;
                if(Session::has('coupon_discount')){
                $total -= Session::get('coupon_discount');
                }
            @endphp
            <tr class="cart-total">
                <th><span class="strong-600">{{ __('Total') }}<br> (Inclusive of Taxes)</span></th>
                <td class="text-right">
                    <strong><span>{{ single_price($total) }}</span></strong>
                </td>
            </tr>
            @if(paymentSetting('octifi.enable') && $total >= paymentSetting('octifi.minimumOrderTotal', 0))
                @if($cartSummaryTexts)
                    <tr class="cart-summary-text">
                        <td colspan="2">
                            <div class="d-flex align-items-center">
                                <span class="mr-1">{{ sprintf(__($cartSummaryTexts[0]), round($total*0.33, 2) ) }}</span>
                                <a class="mr-1" href="{{ paymentSetting('octifi.web') }}" target="_blank">
                                    <img loading="lazy" class="" src="{{ static_asset('frontend/images/icons/cards/Latitude-Pay_Logo_Blue_RGB.png') }}" style="height: 15px;">
                                </a>
                                <span>{{ __($cartSummaryTexts[1]) }}</span>
                            </div>
                            <div class="d-flex align-items-center mt-1">
                                <span class="mr-1">{{ __('Visit') }}</span>
                                <a class="mr-1" href="http://support.octifi.com/support/solutions/81000010762" target="_blank">
                                    {{ __('here') }}
                                </a>
                                <span class="mr-1">{{ __('for more information on') }}</span>
                                <a href="{{ paymentSetting('octifi.web') }}" target="_blank">
                                    <img loading="lazy" class="" src="{{ static_asset('frontend/images/icons/cards/Latitude-Pay_Logo_Blue_RGB.png') }}" style="height: 15px;">
                                </a>
                            </div>
                        </td>
                    </tr>
                @endif
            @endif
            </tfoot>
        </table>
        @if (url()->current() != "https://ysg.com.sg/checkout/delivery_info")
            @if (Auth::check() && \App\BusinessSetting::where('type', 'coupon_system')->first()->value == 1)
                @if (Session::has('coupon_discount'))
                    <div class="mt-3">
                        <form class="form-inline" action="{{ route('checkout.remove_coupon_code') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group flex-grow-1">
                                <div class="form-control bg-gray w-100">{{ \App\Coupon::find(Session::get('coupon_id'))->code }}</div>
                            </div>
                            <button type="submit" class="btn btn-base-1">{{ __('Change Coupon') }}</button>
                        </form>
                    </div>
                @else
                    <div class="mt-3">
                        <form class="form-inline" action="{{ route('checkout.apply_coupon_code') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group flex-grow-1">
                                <input type="text" class="form-control w-100" name="code" placeholder="{{ __('Have coupon code? Enter here') }}" required>
                            </div>
                            <button type="submit" class="form-group btn btn-base-1">{{ __('Apply') }}</button>
                        </form>
                    </div>
                @endif
            @endif
        @endif
    </div>
</div>
<script>
    //$('#cart-total').on('input', function(){ // capturing every step
    var currency = '$';
    var zone1 = 60; // Free Standard shipping
    var zone2 = 160; // Free Express shipping
    var cart_total = $('#ysgcombineshippingcart').html();
    function human_money(money, currency) {
        return currency + parseFloat(money).toFixed(2);
    }
    cart_total = {{$shipmentcartotal}}
    $('.total').html(human_money(cart_total, currency));
    if(cart_total <= zone1) {
        var new_width = ((cart_total / zone1) * 100).toFixed(0) + '%';
        //  $('.progress-bar').css('width', new_width).html(new_width);
        //} else if (cart_total <= zone2 ) {
        //  var new_width = ((cart_total / zone2) * 100).toFixed(0) + '%';
        //  $('.progress-bar').css('width', new_width).html(new_width);
    } else {
        // $('.progress-bar').css('width', '100%').html(currency+zone1);
    }
    if(cart_total < zone1) {
        //$('.progress-bar').css('background', 'red');
        // $('.notice').html('You are <b>' + human_money(zone1 - cart_total, currency) + '</b> away from free local shipping!</b>');
        //} else if (cart_total < zone2 ) {
        //  $('.progress-bar').css('background', 'orange');
        //  $('.notice').html('Your order is <b>eligable</b> for <b>free STANDARD shipping</b>. Add <b>' + human_money(zone2 - cart_total, currency) + '</b> more to get <b>free EXPRESS shipping</b>.');
    } else {
        //$('.progress-bar').css('background', '#49ff78');
        // $('.notice').html('Awesome! Your purchase qualifies for free local shipping!');
        $('#localshippingcost').html(' FREE');
    }
    var Pshipping = $('#Pshipping').html();
    var directabove = $('#directabove').html();
    console.log(Pshipping);
    console.log(directabove);
    if(parseFloat(Pshipping) > parseFloat(directabove)) {
        console.log("inside free");
        $('#directshippingcost').html(' FREE');
    }
</script>
<style>
    .progress-bar{
        color: #000;
        font-weight: 400;
    }
    .progress{
        height:30px !important
    }
</style>
