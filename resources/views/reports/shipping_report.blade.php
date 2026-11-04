@extends('layouts.app')



@section('content')



<div class="pad-all text-center">
    
    <form class="" action="{{ route('shipping_report.index') }}" method="GET">
        
      
        
       <div class="box-inline mar-btm pad-rgt" style="display:none">
            
            {{ __('Seller Name') }}:
            
            <div class="select">
                
                <select class=" form-control input-sm" name="shop_name" required>
                    @foreach (\App\Shop::where('logo','<>','null')->get() as $key => $seller)

                             <option value="{{ $seller->user_id }}"  @isset($_GET['shop_name']) @if ($_GET['shop_name'] == $seller->user_id) selected @endif @endisset>{{ __($seller->name) }}</option>

                    @endforeach
                     <option value="0"  @isset($_GET['shop_name']) @if ($_GET['shop_name'] == 0) selected @endif @endisset>All (Testing)</option>
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
    
</div>


<!-- lets have a table list -->

<div class=" col-md-12">
    
    <div class="panel">
        
        <!--Panel heading-->
        
        <div class="panel-heading">
            
            <h3 class="panel-title">{{ __('Shipping Report.') }} </h3>
            
        </div>
      
        
        
        
        @if (isset($_GET['shop_name'])) 
        <!--Panel body-->
        
        <div class="panel-body">
            
            <div class="table-responsive">
                
               <table class="table table-striped mar-no demo-dt-basic">
                    
                    <thead>
                        
                        <tr>
                            <th>No.</th>
                            <th>{{ __('Order Date') }}</th>
                            <th>{{ __('Order Number') }}</th>
                            
                         
                           
                            <th>{{ __('Shipping Type') }}</th>
                           
                             <th>{{ __('Total Shipping Fee') }}</th>
                              <th>{{ __('Delivery Status') }}</th>
							
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
                            
                          $orders = App\OrderDetail::
                                    join('orders', 'orders.id', '=', 'order_details.order_id')
                                    ->where('order_details.payment_status','paid')
                                    ->whereBetween('orders.date', [$from, $to])
                                    ->get();
                           echo "here";
                            
                            
                        }else{
                            //$orders = \App\OrderDetail::where('payment_status','paid')->where('seller_id',$_GET['shop_name'])->whereBetween('created_at', [$from, $to])->get();
                            
                            
                             $orders = App\OrderDetail::
                                    join('orders', 'orders.id', '=', 'order_details.order_id')
                                    ->where('order_details.payment_status','paid')
                                    ->whereBetween('orders.date', [$from, $to])
                                    ->where('seller_id',$_GET['shop_name'])
                                    ->get();
                        }
                        @endphp
                        @foreach ($orders as $key => $order)
                        
                        @php
                        // var_dump($order);
                        
                        $orderx = \App\Order::where('id',$order->order_id)->first();
                        
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
							<td>{{$shipping_type}}</td>
                           <td>{{ number_format((float)$orderx->total_shipping_cost, 2, '.', '')   }}</td>
                            
                            
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
                           
                            
                            
                        </tr>
                        
                        
                        @endif
                        @endforeach
                        
                        <tfoot>
                            <tr>
                            <th>No.</th>
                            <th>{{ __('Order Date') }}</th>
                            <th>{{ __('Order Number') }}</th>
                            
                         
                           
                            <th>{{ __('Shipping Type') }}</th>
                           
                             <th>{{ __('Total Shipping Fee') }}</th>
                              <th>{{ __('Delivery Status') }}</th>
						
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

