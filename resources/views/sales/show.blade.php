@extends('layouts.app')

@section('content')

<div class="panel">
    <div class="panel-body">
        <div class="invoice-masthead">
            <div class="invoice-text">
                <h3 class="h1 text-thin mar-no text-primary">{{ __('Order Details') }}</h3>
            </div>
        </div>
        <hr>
        <div class="invoice-bill row">
            <div class="col-sm-6 text-xs-center">
                <address>
                    <strong class="text-main">{{ json_decode($order->shipping_address)->name }}</strong><br>
                    {{ json_decode($order->shipping_address)->email }}<br>
                    @if ($order->isHomeDelivery())
                        <span class="">{{ $order->user->fullPhone() }}</span><br>
                        {{ __('Delivery Address:') }} <br>
                        {{ $address->address }}, {{ $address->city }}, {{ $address->postal_code }}<br>
                        {{ $address->country }} <br>
                        {{ $address->phone }}
                    @else
                        {{ $order->user->fullPhone() }}<br>
                        {{ __('Delivery Address:') }} <br>
                        {{ __('N/A') }}
                    @endif
                </address>
                @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                <br>
                <strong class="text-main">{{ __('Payment Information') }}</strong><br>
                {{ __('Name') }}:
                {{ json_decode($order->manual_payment_data)->name }},
                {{ __('Amount') }}: {{ single_price(json_decode($order->manual_payment_data)->amount) }},
                {{ __('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                <br>
                <a href="{{ my_asset(json_decode($order->manual_payment_data)->photo) }}" target="_blank"><img src="{{ my_asset(json_decode($order->manual_payment_data)->photo) }}" alt="" height="100"></a>
                @endif
            </div>
            <div class="col-sm-6 text-xs-center">
                <table class="invoice-details">
                    <tbody>
                        <tr>
                            <td class="text-main text-bold">
                                {{ __('Order #') }}
                            </td>
                            <td class="text-right text-info text-bold">
                                {{ $order->code }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{ __('Delivery Status') }}
                            </td>
                            @php
                            $mainOrderStatus = ucfirst(str_replace('_', ' ', $order->delivery_status));
                            @endphp
                            <td class="text-right">
                                <span class="badge {{ ($mainOrderStatus == 'Delivered')?'badge-success':'badge-info' }}">
                                    {{ $mainOrderStatus }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{ __('Order Date') }}
                            </td>
                            <td class="text-right">
                                {{ date('d-m-Y h:i A', $order->date) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{ __('Total amount') }}
                            </td>
                            <td class="text-right">
                                {{ single_price($order->orderDetails->sum('price') + $order->orderDetails->sum('tax')) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{ __('Payment method') }}
                            </td>
                            <td class="text-right">
                                {{ __(ucfirst(str_replace('_', ' ', $order->payment_type))) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="new-section-sm bord-no">
        <div class="row">
            <div class="col-lg-12 table-responsive">
                <table class="table table-bordered invoice-summary">
                    <thead>
                        <tr class="bg-trans-dark">
                            <th width="2%">#</th>
                            <th width="10%">
                                {{ __('Photo') }}
                            </th>
                            <th class="text-uppercase">
                                {{ __('Description') }}
                            </th>
                            <th class="text-uppercase text-center">
                                {{ __('Variation')}}
                            </th>
                            <th class="text-uppercase text-center">
                                {{ __('Delivery Type') }}
                            </th>
                            <th class="min-col text-center text-uppercase">
                                {{ __('Qty') }}
                            </th>
                            <th class="min-col text-center text-uppercase">
                                {{ __('Price') }}
                            </th>
                            <th class="min-col text-center text-uppercase">
                                {{ __('Status') }}
                            </th>
                            <th class="min-col text-center text-uppercase">
                                {{ __('Total') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $key => $orderDetail)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>
                                @if ($orderDetail->product != null)
                                <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank"><img height="50" src="{{ my_asset($orderDetail->product->getThumbnailImageOrder($orderDetail->variation)) }}"></a>
                                @else
                                <strong>{{ __('N/A') }}</strong>
                                @endif
                            </td>
                            <td>
                                @if ($orderDetail->product != null)
                                <strong><a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">{{ $orderDetail->product->name }}</a></strong>
                                <small>{{ $orderDetail->variation }}</small>
                                @if ($orderDetail->shipping_type == 'self_collection')
                                    <div>
                                        <span>{{ __('Self-Collection Address: ') }} {{ $orderDetail->collection_address }}</span>
                                    </div>
                                @endif
                                @else
                                <strong>{{ __('Product Unavailable') }}</strong>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <ul class="mb-0 pl-4">
                                        @foreach ($orderDetail->getVariantDetails() as $variant)
                                        <li>
                                            {{ $variant }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                            <td>
                                @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                {{ __('Home Delivery') }}
                                @elseif ($orderDetail->shipping_type == 'pickup_point')
                                    @if ($orderDetail->pickup_point != null)
                                    {{ $orderDetail->pickup_point->name }} ({{ __('Pickup Point') }})
                                    @else
                                    {{ __('Pickup Point') }}
                                    @endif
                                @elseif($orderDetail->shipping_type == 'self_collection')
                                    {{ __('Self Collection') }}
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $orderDetail->quantity }}
                            </td>
                            <td class="text-center">
                                {{ single_price($orderDetail->price/$orderDetail->quantity) }}
                            </td>
                            <td class="text-center">
                                @if ($orderDetail->shipping_type == 'self_collection')
                                <select class="form-control demo-select2"  name="update_delivery_status_row[]" data-details="{{$orderDetail->id}}" data-minimum-results-for-search="Infinity" id="update_delivery_status_row">
                                    @foreach ($selfCollectionStatus as $key => $value)
                                    <option value="{{ $key }}" @if ($orderDetail->delivery_status == $key) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @else
                                <select class="form-control demo-select2"  name="update_delivery_status_row[]" data-details="{{$orderDetail->id}}" data-minimum-results-for-search="Infinity" id="update_delivery_status_row">
                                    @foreach ($status as $key => $value)
                                    <option value="{{ $key }}" @if ($orderDetail->delivery_status == $key) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ single_price($orderDetail->price) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="clearfix">
            <table class="table invoice-total">
    			<tbody>
                    <tr>
                        <td>
                            <strong>{{ __('Sub Total') }} :</strong>
                        </td>
                        <td>
                            {{ single_price($order->orderDetails->sum('price')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>{{ __('Tax') }} :</strong>
                        </td>
                        <td>
                            {{ single_price($order->orderDetails->sum('tax')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>{{ __('Shipping') }} :</strong>
                        </td>
                        <td>
                            {{ single_price($order->total_shipping_cost) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>{{ __('Discount') }} :</strong>
                        </td>
                        <td>
                            {{ single_price($order->coupon_discount) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>{{ __('TOTAL') }} :</strong>
                        </td>
                        <td class="text-bold h4">
                            {{ single_price($order->grand_total) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-right no-print">
            <a href="{{ route('customer.invoice.download', $order->id) }}" class="btn btn-default"><i class="demo-pli-printer icon-lg"></i></a>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">


    $('select').bind('change', function(){
        var orderdetail_id = $(this).data('details');
        var selecteddata = $(this).val();

        $.post('{{ route('orders.update_delivery_status_details') }}', {_token:'{{ @csrf_token() }}',order_id:orderdetail_id,status:selecteddata}, function(data){
            showAlert('success', 'Delivery status has been updated');
        });

    });


    $('#update_delivery_status').on('change', function(){
        var order_id = {{ $order->id }};
        var status = $('#update_delivery_status').val();
        $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
            showAlert('success', 'Delivery status has been updated');
        });
    });

    $('#update_payment_status').on('change', function(){
        var order_id = {{ $order->id }};
        var status = $('#update_payment_status').val();
        $.post('{{ route('orders.update_payment_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
            showAlert('success', 'Payment status has been updated');
        });
    });
</script>
@endsection
