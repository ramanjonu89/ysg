<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YSG</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta charset="UTF-8">
    <style media="all">
        @font-face {
            font-family: 'Helvetica_Neue_LT_Pro';
            src: url("{{ my_asset('assets/fonts/helvetica/helvetica_neue.ttf') }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            line-height: 1.3;
            font-family: 'Helvetica_Neue_LT_Pro';
            color: black;
        }

        body {
            font-family: 'Helvetica_Neue_LT_Pro';
            font-size: .875rem;
        }

        .gry-color *,
        .gry-color {
            color: #878f9c;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: .5rem .7rem;
        }

        table.padding td {
            padding: .7rem;
        }

        table.sm-padding td {
            padding: .2rem .7rem;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .small {
            font-size: .85rem;
        }

        .currency {}

        .connect-link {
            float: right;
            position: relative;
            /* left: -50%; */
            /* or right 50% */
            /* text-align: left; */

            /* margin: 0;
            padding: 0; */
        }

        .connect-link .item-connect-link {
            /* position: relative; */
            /* left: 50%; */

            /* float: left;
            list-style-type: none;
            margin: .5em; */
        }

        .clear {
            clear: both;
        }

        .fw-65 {
            font-weight: 650 !important;
        }

        .fw-45 {
            font-weight: 450 !important;
            opacity: 0.5;
        }

        table td .variation {
			padding-left: 1rem;
		}
    </style>
</head>

<body style="font-family: Helvetica_Neue_LT_Pro">
    <div>
        <div style="background: #eceff4;padding: 1.5rem;">
            <table>
                <tr>
                    <td class="fw-65">
                        @include('invoices.imgs.logo')
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="small fw-65" style="font-size: 22px; margin-bottom:8px">{{ $generalSetting->site_name }}
                    </td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td class="small fw-65">{{ $generalSetting->address }}</td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td class="small fw-65">{{ __('Email') }}: <span
                            class="fw-45">{{ $generalSetting->email }}</span></td>
                    <td class="text-right small fw-65"><span class="small">{{ __('Order ID') }}:</span> <span
                            class="strong fw-45">{{ $order->code }}</span></td>
                </tr>
                <tr>
                    <td class="text-right small fw-65"><span class="small">{{ __('Order Date') }}:</span> <span
                            class="strong fw-45">{{ date('d-m-Y', $order->date) }}</span></td>
                </tr>
            </table>
        </div>
        <div style="padding: 1.5rem;padding-bottom: 0">
            <table>
                @php
                    $shipping_address = json_decode($order->shipping_address);
                @endphp
                <tr>
                    <td class="strong small fw-65">{{ $order->user->name }}</td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td class="strong small fw-65">{{ __('Email') }}: <span class="fw-45">{{ $order->user->email }}</span></td>
                    <td class="text-right">{{ __('Delivery Address') }}:</td>
                </tr>
                <tr>
                    <td class="strong small fw-65">{{ __('Phone') }}: <span class="fw-45">{{ $order->user->fullPhone() }}</span></td>
                    <td class="text-right small fw-45">
                        <span class="fw-45">
                            @if ($order->isHomeDelivery())
                                {{ $shipping_address->address }}, {{ $shipping_address->city }},
                                {{ $shipping_address->country }} {{ $shipping_address->postal_code }} <br>
                                {{ $shipping_address->phone }}
                            @else
                                {{ __('NA') }}
                            @endif
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <div style="padding: 1.5rem;">
            <table class="padding text-left small border-bottom">
                <thead>
                    <tr class="strong" style="background: #eceff4;">
                        <th width="35%" class="fw-65">{{ __('Product Name') }}</th>
                        <th width="15%" class="fw-65">{{ __('Variation') }}</th>
                        <th width="15%" class="fw-65">{{ __('Delivery Type') }}</th>
                        <th width="10%" class="fw-65">{{ __('Qty') }}</th>
                        <th width="15%" class="fw-65">{{ __('Unit Price') }}</th>
                        <th width="10%" class="fw-65">{{ __('Tax') }}</th>
                        <th width="15%" class="text-right fw-65">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @foreach ($order->orderDetails as $key => $orderDetail)
                        @if ($orderDetail->product != null)
                            <tr class="">
                                <td class="fw-65">
                                    {{ $orderDetail->product->name }} @if ($orderDetail->variation != null)
                                        ({{ $orderDetail->variation }})
                                    @endif
                                    @if ($orderDetail->shipping_type == 'self_collection')
                                    <div>
                                        <span>{{ __('Self-Collection Address: ') }} {{ $orderDetail->collection_address }}</span>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="variation">
                                        <ul class="item">
                                            @foreach ($orderDetail->getVariantDetails() as $variant)
                                            <li>
                                                {{ $variant }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                                <td width="18%" class="fw-65">
                                    @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                        {{ __('Home Delivery') }}
                                    @elseif ($orderDetail->shipping_type == 'pickup_point')
                                        @if ($orderDetail->pickup_point != null)
                                            {{ $orderDetail->pickup_point->name }} ({{ __('Pickip Point') }})
                                        @endif
                                    @elseif($orderDetail->shipping_type == 'self_collection')
                                        {{ __('Self Collection') }}
                                    @endif
                                </td>
                                <td class="text-center fw-65">{{ $orderDetail->quantity }}</td>
                                <td class="fw-65">
                                    {{ single_price($orderDetail->price / $orderDetail->quantity) }}</td>
                                <td class="fw-65">
                                    {{ single_price($orderDetail->tax / $orderDetail->quantity) }}</td>
                                <td class="fw-65 text-right">
                                    {{ single_price($orderDetail->price + $orderDetail->tax) }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:0 1.5rem; display:block;width:100%;position:relative;z-index:99999;">
            <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
                <tbody>
                    <tr>
                        <th class="text-left fw-65">{{ __('Sub Total') }}</th>
                        <td class="fw-65">{{ single_price($order->orderDetails->sum('price')) }}</td>
                    </tr>
                    <tr>
                        <th class="text-left fw-65">{{ __('Shipping Cost') }}</th>
                        <td class="fw-65">{{ single_price($order->total_shipping_cost) }}</td>
                    </tr>
                    <tr>
                        <th class="text-left fw-65">{{ __('Less Promotion Code') }}</th>
                        <td class="fw-65">(-{{ single_price($order->coupon_discount) }})</td>
                    </tr>
                    <tr class="border-bottom">
                        <th class="text-left fw-65">{{ __('Total Tax') }}</th>
                        <td class="fw-65">{{ single_price($order->orderDetails->sum('tax')) }}</td>
                    </tr>
                    <tr>
                        <th class="text-left strong fw-65">{{ __('Grand Total') }}</th>
                        <td class="fw-65">{{ single_price($order->grand_total) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="clear"></div>
    </div>
</body>

</html>
