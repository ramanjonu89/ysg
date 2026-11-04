@extends('layouts.app')
@section('content')
@php
ini_set('memory_limit', '1024M');
@endphp
<div class="pad-all text-center">
    <form class="" action="{{ route('seller_sale_report.index') }}" method="GET">
        <div class="box-inline mar-btm pad-rgt">
            {{ __('Seller Name') }}:
            <div class="select">
                <select class=" form-control input-sm" name="shop_name" required>
                    @foreach (\App\Shop::where('logo','<>','null')->get() as $key => $seller)
                             <option value="{{ $seller->user_id }}"  @isset($_GET['shop_name']) @if ($_GET['shop_name'] == $seller->user_id) selected @endif @endisset>{{ __($seller->name) }}</option>
                    @endforeach
                     <option value="0"  @isset($_GET['shop_name']) @if ($_GET['shop_name'] == 0) selected @endif @endisset>All</option>
                </select>
            </div>
        </div>
         <div class="box-inline mar-btm pad-rgt">
            {{ __('Start Date') }}:
           <input class="form-control datepicker input-sm" type="text" name="start_date" placeholder="{{ __('Start Date') }}" @isset($_GET['start_date']) value="{{$_GET['start_date']}}" @endisset required>
        </div>
         <div class="box-inline mar-btm pad-rgt">
            {{ __('End Date') }}:
           <input class="form-control datepicker input-sm" type="text" name="end_date" placeholder="{{ __('End Date') }}" @isset($_GET['end_date']) value="{{$_GET['end_date']}}" @endisset required>
        </div>
        <button class="btn btn-default" type="submit">{{ __('Search') }}</button>
    </form>
     <span style="margin: 20px; background: #0080006b; padding: 20px; display: flex; border-radius: 10px;">*Feb 06 2021 - New fields Added - RSP, Discount, Discount Type - Order Before the cut off date will not have the value </span>
</div>
<!-- lets have a table list -->
<div class=" col-md-12">
    <div class="panel">
        <!--Panel heading-->
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Seller Based Selling List Report.') }} </h3>
        </div>
        @if (isset($_GET['shop_name']))
        <!--Panel body-->
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped res-table mar-no dataTable">
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
                            <th>{{ __('Total Shipping Fee') }}</th>
                            <th>{{ __('Shipping Type') }}</th>
                             <th>{{ __('Last Update status') }}</th>
                             <th>{{ __('Ref.No') }}</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                        //$from = date('2020-12-18');
                        // $to = date('2021-05-02');
                         $from = $_GET['start_date'];
                        $to = $_GET['end_date'];
                        $from = date("Y-m-d", strtotime($from));
                       //$to = date("Y-m-d", strtotime($to));
                        $to = date('Y-m-d', strtotime("+1 day", strtotime($to)));
                       // date("Y-m-d H:i:s", substr( $orderx->date, 0, 10))
                        $from = strtotime($from);
                        $to = strtotime($to);
                        if ($_GET['shop_name'] == 0){
                            //$orders = \App\OrderDetail::where('payment_status','paid')->whereBetween('created_at', [$from, $to])->get();
                          $orders = App\Models\OrderDetail::
                                    join('orders', 'orders.id', '=', 'order_details.order_id')
                                    ->where('order_details.payment_status','paid')
                                    ->whereBetween('orders.date', [$from, $to])
                                    ->get();
                        }else{
                            //$orders = \App\OrderDetail::where('payment_status','paid')->where('seller_id',$_GET['shop_name'])->whereBetween('created_at', [$from, $to])->get();
                             $orders = App\Models\OrderDetail::
                                    join('orders', 'orders.id', '=', 'order_details.order_id')
                                    ->where('order_details.payment_status','paid')
                                    ->whereBetween('orders.date', [$from, $to])
                                    ->where('seller_id',$_GET['shop_name'])
                                    ->get();
                        }
                        @endphp
                        @if (count($orders) <10000)
                        @foreach ($orders as $key => $order)
                        @php
                        // var_dump($order);
                        $orderx = \App\Models\Order::where('id',$order->order_id)->first();
                        $seller =  \App\Shop::where('user_id', $order->seller_id)->first();
                        $shipping_type = "Combine";
						if($order->shipping_type == "home_delivery"){
						$shipping_type = "";
						}else{
						$shipping_type = $order->shipping_type;
						}
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
                                  $brand = \App\Brand::select('name')->where('id',$order->product->brand_id)->first();
                                @endphp
                                @if($brand)
                                    {{ $brand->name }}
                                @endif
                            </td>
                            <th>
                                @php
                                $category_id = \App\Models\Category::select('name')->where('id',$order->product->category_id)->first();
                                $subcategory_id = \App\Models\SubCategory::select('name')->where('id',$order->product->subcategory_id)->first();
                                $subsubcategory_id = \App\Models\SubSubCategory::select('name')->where('id',$order->product->subsubcategory_id)->first();
                                @endphp
                                @if(isset($category_id))
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
                            <td>{{ $order->rsp_price }}</td>
                            <td>{{ $order->discount }} </td>
                            <td> {{ $order->discount_type }}</td>
                            <td>
                                @if ($order->discount_type=="percent")
                                @php
                                $foo = (($order->rsp_price)-(($order->rsp_price)*($order->discount)/100));
                                @endphp
                                {{ number_format($foo, 2, '.', '') }}
                                @else
                                @php
                                $foo = (($order->rsp_price)-($order->discount));
                                @endphp
                                {{ number_format($foo, 2, '.', '') }}
                                @endif
                            </td>
                            <td>{{ number_format((float)$order->price, 2, '.', '')   }}</td>
                             <td>{{ number_format((float)$orderx->total_shipping_cost, 2, '.', '')   }}</td>
                             <td>{{$shipping_type}}</td>
                                <td>{{$order->updated_at}}</td>
                                {{-- view here --}}
                                <td>{{isset($order->order) && isset($order->order->paymentReferentId) ? $order->order->paymentReferentId : ''}}</td>
                        </tr>
                        @endif
                        @endforeach
                        @else
                        <h3>More than 4000 records detected. Please choose a smaller daterange. Your browser unable to display the load</h3>
                        @endif
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
                                 <th>{{ __('Total Shipping Fee') }}</th>
                                  <th>{{ __('Shipping Type') }}</th>
                                      <th>{{ __('Last Update status') }}</th>
                                      <th>{{ __('Ref.No') }}</th>
                            </tr>
                        </tfoot>
                    </tbody>
                </table>
            </div>
        </div>
        @else
      <div class="panel-body">
      Please select the date range above to start
      </div>
        @endif
    </div>
</div>
 <script>
  $( function() {
    $( ".datepicker" ).datepicker(
    {
     format: 'mm/dd/yyyy',
    }
    );
  } );
  </script>
@endsection
