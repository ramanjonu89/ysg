@extends('layouts.app')
@section('content')
@php
$refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
@endphp
<!-- Basic Data Tables -->
<!--===================================================-->
<div class="panel">
<div class="pad-all text-center">

    <form class=""  method="GET" action="{{ route('sales.index') }}" enctype="">
        @csrf
        <div class="box-inline mar-btm pad-rgt">
            {{ __('Start Date') }}:
            <input class="form-control datepicker input-sm" type="text" name="start_date" placeholder="{{ __('Start Date') }}" @isset($_GET['start_date']) value="{{$_GET['start_date']}}" @endisset autocomplete="off" required >
        </div>
        <div class="box-inline mar-btm pad-rgt">
            {{ __('End Date') }}:
            <input class="form-control datepicker input-sm" type="text" name="end_date" placeholder="{{ __('End Date') }}" @isset($_GET['end_date']) value="{{$_GET['end_date']}}" @endisset autocomplete="off" required>
        </div>
        <button class="btn btn-default" type="submit">{{ __('Search') }}</button>
    </form>
    </div>
<div class="panel-heading">
    <h3 class="panel-title">{{ __('Total Sales') }} </h3>
</div>
@if (isset($_GET['start_date']))
    <div class="panel-body">
        <table class="table table-striped res-table mar-no dataTable" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Order Date') }}</th>
                    <th>{{ __('Order Code') }}</th>
                    <th>{{ __('Num. of Products') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Shipping Fee') }}</th>
                    <th>{{ __('Coupon Discount') }}</th>
                    <th>{{ __('Refunded Amount (Excl. Shipping)') }}</th>
                    <th>{{ __('Cart Value') }}</th>
                    <th>{{ __('Amount Paid') }}</th>
                    <th>
                        {{ __('Delivery Status') }}
                        <span class="delivery-info">
                            <span class="info-icon">?</span>
                            <div class="delivery-popup">
                                <h4 class="title-delivery">Delivery Status For Multiple Merchants Order</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Merchant 1</th>
                                            <th>Merchant 2</th>
                                            <th width="20%">Delivery Status</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Delivered</td>
                                            <td>Delivered</td>
                                            <td>Delivered</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Cancelled</td>
                                            <td>Cancelled</td>
                                            <td>Cancelled</td>
                                            <td>Fully Order Cancelled show as Cancelled</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Delivered</td>
                                            <td>Cancelled</td>
                                            <td>Partial Delivered</td>
                                            <td>Take earliest delivery status</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Before Delivered (In Transit) </td>
                                            <td>Delivered</td>
                                            <td>In Transit</td>
                                            <td>Take the earliest delivery status from the multiple merchant </td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>Before Delivered (Ready To Ship)</td>
                                            <td>Cancelled</td>
                                            <td>Ready To Ship</td>
                                            <td>Take the earliest delivery status from the multiple merchant</td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td>Before Delivered (Pending)</td>
                                            <td>Before Deliver (Ready to Ship) </td>
                                            <td>Pending</td>
                                            <td>Take the earliest delivery status from the multiple product/merchant</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </span>
                    </th>
                    <th>{{ __('Payment Status') }}</th>
                    <th>{{ __('Payment Type') }}</th>
                    <th>{{ __('Restock Status') }}</th>
                    <th width="10%">{{ __('Ref.no') }}</th>
                    @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                    <th>{{ __('Refund') }}</th>
                    @endif
                    <th>{{ __('Used Coupons') }}</th>
                    <th width="10%">{{ __('options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $key => $order)
                    <tr>
                        <td>
                            {{ $order->id }}
                        </td>
                        <td>
                            {{ $order->created_at }}
                        </td>
                        <td>
                            {{ $order->code }}
                        </td>
                        <td>
                            {{ count($order->orderDetails) }}
                        </td>
                        <td>
                            @if ($order->user != null)
                                {{ $order->user->name }}
                            @else
                                Guest ({{ $order->guest_id }})
                            @endif
                        </td>
                        <td>
                            @if ($order->user != null)
                                {{ $order->user->email }}
                            @else
                                Guest ({{ $order->guest_id }})
                            @endif
                        </td>
                        <td>
                            {{ $order->total_shipping_cost }}
                        </td>
                        <td>
                            {{ $order->coupon_discount }}
                        </td>
                        <td>
                            @php
                                $refund_amount = 0;
                                foreach ($order->orderDetails as $key => $orderDetail) {
                                    if ($orderDetail->delivery_status == "cancelled"){
                                        $refund_amount +=  $orderDetail->price;
                                    }
                                }
                            @endphp

                            @if ($refund_amount > 0)
                                -{{$refund_amount}}
                            @else
                                0
                            @endif
                        </td>
                        <td>
                            {{ single_price(\App\Models\OrderDetail::where('order_id', $order->id)->where('payment_status','paid')->sum('price')) }}
                        </td>
                        <td>
                            {{ single_price($order->grand_total) }}
                        </td>
                        <td>
                            {{ __($order->delivery_status) }}
                        </td>
                        <td>
                            <span class="badge badge--2 mr-4">
                                @if ($order->payment_status == 'paid')
                                    <i class="bg-green"></i> {{ __('Paid') }}
                                @else
                                    <i class="bg-red"></i> {{ __('Unpaid') }}
                                @endif
                            </span>
                        </td>
                        <td>
                            {{ __($order->payment_type) }}
                        </td>

                        <td>
                            @if ($order->payment_status == 'unpaid')
                                @if ($order->restock_status == 1)
                                    <span class="badge badge-success">Restocked</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            @else
                                <span class="badge badge-secondary">N/A</span>
                            @endif
                        </td>

                        <td>
                            @if ($order->payment_status == 'paid')
                                {{ $order->paymentReferentId }}
                            @endif
                        </td>

                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                            <td>
                                @if (count($order->refund_requests) > 0)
                                    {{ count($order->refund_requests) }} {{ __('Refund') }}
                                @else
                                    {{ __('No Refund') }}
                                @endif
                            </td>
                        @endif
                        <td>
                            {{ $order->list_coupon_name }}
                        </td>
                        <td>
                            <div class="btn-group dropdown">
                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                    {{ __('Actions') }} <i class="dropdown-caret"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a href="{{route('sales.show', encrypt($order->id))}}">{{ __('View') }}</a></li>
                                    <li><a href="{{ route('orders.resync', $order->id) }}">{{ __('Resync OMS') }}</a></li>
                                    <li><a href="{{ route('customer.invoice.download', $order->id) }}">{{ __('Download Invoice') }}</a></li>
                                    @if ($order->payment_status == 'unpaid' && $order->restock_status == 0)
                                    <li><a href="#" onclick="restockOrder({{ $order->id }}, this)" class="restock-btn">{{ __('Restock') }}</a></li>
                                    @endif
                                <!-- <li><a onclick="confirm_modal('{{route('orders.destroy', $order->id)}}');">{{ __('Delete') }}</a></li>-->
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">

            </div>
        </div>
    </div>
@else
    <div class="panel-body">
        Please select the date range above to start
    </div>
@endif
</div>
@endsection
@section('script')
<script type="text/javascript">
function restockOrder(orderId, element) {
    if ($(element).hasClass('processing')) {
        return false;
    }

    if (confirm('Are you sure you want to restock this order?')) {
        $(element).addClass('processing').text('Processing...');

        $.ajax({
            url: '{{ route("orders.restock", ":id") }}'.replace(':id', orderId),
            type: 'POST',
            data: {
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'Failed to restock order');
                }
            },
            error: function(xhr) {
                var errorMessage = 'Failed to restock order';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            },
            complete: function() {
                $(element).removeClass('processing').text('{{ __("Restock") }}');
            }
        });
    }
}
</script>
    <style>
        #DataTables_Table_0_paginate{

        }

        .delivery-info {
            position: relative;
            display: inline-block;
            margin-left: 4px;
        }

        .info-icon {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #f1f1f1;
            border: 1px solid #ccc;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            color: #555;
        }

        .delivery-popup {
            display: none;
            position: absolute;
            top: 20px;
            background: #fff;
            padding: 10px;
            border: 1px solid #ddd;
            box-shadow: 0px 2px 10px rgba(0,0,0,0.15);
            z-index: 99999;
            width: 700px;
            border-radius: 6px;
        }

        .delivery-info:hover .delivery-popup {
            display: block;
        }

        .delivery-popup table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .delivery-popup th,
        .delivery-popup td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        .delivery-popup th {
            background: #f8be9a;
            font-weight: bold;
        }

        .title-delivery {
            color: #f8be9a;
            text-decoration: underline;
        }

        @media (min-width: 1500px) {
            .delivery-popup {
                left: 0;
                transform: translateX(-100%);
                right: auto;
            }
        }

        @media (max-width: 1499px) {
            .delivery-popup {
                right: auto;
                left: 2rem;
                transform: translateY(-20%);
            }
        }

    </style>
    <script>
        $(function(){
            $( ".datepicker" ).datepicker({
                format: 'mm/dd/yyyy',
            });
        });
    </script>

@endsection
