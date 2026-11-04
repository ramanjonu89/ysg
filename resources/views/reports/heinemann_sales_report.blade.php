@extends('layouts.app')
@section('content')
<!--
    <div class="pad-all text-center">
    <form class="" action="{{ route('seller_sale_report.index') }}" method="GET">
    <div class="box-inline mar-btm pad-rgt">
    {{ __('Sort by verificarion status') }}:
    <div class="select">
    <select class="demo-select2" name="verification_status" required>
    <option value="1">{{ __('Approved') }}</option>
    <option value="0">{{ __('Non Approved') }}</option>
    </select>
    </div>
    </div>
    <button class="btn btn-default" type="submit">{{ __('Filter') }}</button>
    </form>
    </div>
    <div class="col-md-offset-2 col-md-8">
    <div class="panel">
    <div class="panel-heading">
    <h3 class="panel-title">{{ __('Seller Based Selling Report Summary') }}</h3>
    </div>
    <div class="panel-body">
    <div class="table-responsive">
    <table class="table table-striped mar-no demo-dt-basic">
    <thead>
    <tr>
    <th>{{ __('Seller Name') }}</th>
    <th>{{ __('Shop Name') }}</th>
    <th>{{ __('Number of Product Sale') }}</th>
    <th>{{ __('Order Amount') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($sellers as $key => $seller)
    @if($seller->user != null)
    <tr>
    <td>{{ $seller->user->name }}</td>
    <td>{{ $seller->user->shop->name }}</td>
    <td>
    @php
    $num_of_sale = 0;
    $number_of_sales = count(\App\OrderDetail::where('seller_id', $seller->user->id)->where('payment_status','paid')->get());
    foreach ($seller->user->products as $key => $product) {
    $num_of_sale += $product->num_of_sale;
    }
    @endphp
    {{ $number_of_sales }}
    </td>
    <td>
    {{ single_price(\App\OrderDetail::where('seller_id', $seller->user->id)->where('payment_status','paid')->sum('price')) }}
    </td>
    </tr>
    @endif
    @endforeach
    </tbody>
    </table>
    </div>
    </div>
    </div>
</div>-->
<!-- lets have a table list -->
<div class=" col-md-12">
    <div class="panel">
        <!--Panel heading-->
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Seller Based Selling List Report') }} </h3>
        </div>
        <div class="panel-heading">
            <h3 class="panel-title"> <input id="daterange"></h3>
        </div>
        <!--Panel body-->
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped mar-no demo-dt-basic">
                    <thead>
                        <tr>
                         <th>No.</th>
                            <th>{{ __('Order Date') }}</th>
                            <th>{{ __('Order Number') }}</th>
                            <th>{{ __('Shop Name') }}</th>
                             <th>{{ __('Brand') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Item Name') }}</th>
                             <th>{{ __('Payment Status') }}</th>
                            <th>{{ __('Fulfilment Status') }}</th>
                            <th>{{ __('Qty') }}</th>
                            <th>{{ __('SKU/Unit') }}</th>
                            <th>{{ __('RSP') }}</th>
                             <th>{{ __('Discount') }}</th>
                              <th>{{ __('Discount Type') }}</th>
                            <th>{{ __('Unit Selling Price') }}</th>
                             <th>{{ __('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $orders = \App\OrderDetail::where('payment_status','paid')->where('seller_id','203')->get();
                        @endphp
                        @foreach ($orders as $key => $order)
                        @php
                        // var_dump($order);
                        $orderx = \App\Order::where('id',$order->order_id)->first();
                        $seller =  \App\User::where('id', $order->seller_id)->first();
                        @endphp
                        @if (isset($order->product))
                            <tr>
                            <td>{{ $key+1 }}</td>
                            <td>
								@php
								 $datecreated =  date("Y-m-d H:i:s", substr( $orderx->date, 0, 10));
								@endphp
								{{  $datecreated }}</td>
                            <td>
                            @if( $orderx)
                                {{ $orderx->code }}
                               @endif
                            </td>
                            <td>{{ $seller->name }}</td>
                             <td>
                             @php
                              $brand_id = \App\Brand::where('id',$order->product->brand_id)->first();
                            @endphp
                             @if (isset($brand_id))
                            {{$brand_id->name}}
                        @endif
                                </td>
                            <th>
                            @php
                              $category_id = \App\Models\Category::where('id',$order->product->category_id)->first();
                              $subcategory_id = \App\Models\SubCategory::where('id',$order->product->subcategory_id)->first();
                                $subsubcategory_id = \App\Models\SubSubCategory::where('id',$order->product->subsubcategory_id)->first();
                            @endphp
                           @if (isset($category_id))
                             {{$category_id->name}}
                            @endif
                             @if (isset($subcategory_id))
                            > {{$subcategory_id->name}}
                            @endif
                            @if (isset($subsubcategory_id))
                            > {{$subsubcategory_id->name}}
                            @endif
                            </th>
                            <td width="20%">{{ $order->product->name }}</td>
                            <td>{{ $order->payment_status }}</td>
                            <td>
                            @php
                            $deliverystatus= $order->delivery_status;
                            if ($deliverystatus == "pending") {
                            echo "Order Placed";
                            }else if ($deliverystatus == "on_review") {
                            echo "Ready to Ship";
                            }else if ($deliverystatus == "on_delivery") {
                            echo "In-transit";
                             }else if ($deliverystatus == "delivered") {
                            echo "Delivered";
                             } else if ($deliverystatus == "cancelled") {
                            echo "Cancelled";
                             }else{
                             }
                          @endphp
                                </td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ $order->product->unit }}</td>
                            <td>{{ $order->product->unit_price }}</td>
                            <td>{{ $order->product->discount }} </td>
                            <td> {{ $order->product->discount_type }}</td>
                            <td>
                                @if ($order->product->discount_type=="percent")
                                   @php
                               $foo = (($order->product->unit_price)-(($order->product->unit_price)*($order->product->discount)/100));
                               @endphp
                                {{ number_format((float)$foo, 2, '.', '') }}
                                @else
                                    @php
                                $foo = (($order->product->unit_price)-($order->product->discount));
                               @endphp
                                  {{ number_format((float)$foo, 2, '.', '') }}
                                @endif
                            </td>
                            <td>{{ number_format((float)$order->price, 2, '.', '')   }}</td>
                        </tr>
                        @endif
                        @endforeach
                        <tfoot>
            <tr>
               <th>No.</th>
                            <th>{{ __('Order Date') }}</th>
                            <th>{{ __('Order Number') }}</th>
                            <th>{{ __('Shop Name') }}</th>
                            <th>{{ __('Brand') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Item Name') }}</th>
                            <th>{{ __('Payment Status') }}</th>
                            <th>{{ __('Fulfilment Status') }}</th>
                            <th>{{ __('Qty') }}</th>
                            <th>{{ __('SKU/Unit') }}</th>
                            <th>{{ __('RSP') }}</th>
                              <th>{{ __('Discount') }}</th>
                                  <th>{{ __('Discount Type') }}</th>
                            <th>{{ __('Unit Selling Price') }}</th>
                              <th>{{ __('Subtotal') }}</th>
            </tr>
        </tfoot>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
</script>
@endsection
