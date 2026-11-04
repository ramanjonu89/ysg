@extends('layouts.app')



@section('content')









<div class="col-md-offset-2 col-md-8">
    
    <div class="panel">
        
        <!--Panel heading-->
        
        <div class="panel-heading">
            
            <h3 class="panel-title">{{ __('Order Summary') }}</h3>
            
        </div>
        
        
        
        <!--Panel body-->
        
        <div class="panel-body">
            
            <div class="table-responsive">
                
                <table class="table table-striped mar-no demo-dt-basic">
                    
                    <thead>
                        
                        <tr>
                         <th>{{ __('S/N') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('No. of Order') }}</th>
                            <th>{{ __('No. of Product') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            
                        </tr>
                        
                    </thead>
                    
                    <tbody>
                        @php
                        
                        $orders = \App\Order::select(DB::raw('count(*) as no_of_order'),DB::raw('date(created_at) as order_date'),DB::raw('sum(grand_total) as total_amount'))->where('payment_status','=','paid')->groupBy('order_date')->get();
                        
                        @endphp
                        @foreach ($orders as $key => $order)
                        
                        
                         @php
                       $order_details = \App\OrderDetail::select(DB::raw('count(*) as id'))->where('payment_status','=','paid')->where('delivery_status','!=','cancelled')->where(DB::raw('date(created_at)'),'=',$order->order_date)->first(); 
                       $order_details_cancelled = \App\OrderDetail::select(DB::raw('sum(price) as price'))->where('payment_status','=','paid')->where('delivery_status','=','cancelled')->where(DB::raw('date(created_at)'),'=',$order->order_date)->first();
                       $grant_total = $order->total_amount - $order_details_cancelled->price;
                       
                       @endphp
                        <tr>
                            
                            
                            <td>
                                
                                {{ $key+1 }}
                                
                                
                            </td>
                            <td>
                             {{$order->order_date}}
                            </td>
                            <td>
                                {{$order->no_of_order}}
                            </td>
                            
                             <td>
                              {{$order_details->id}}
                            </td>
                            <td>
                                {{$grant_total}}
                            </td>
                        </tr>
                        
                        
                        
                        @endforeach
                        
                    </tbody>
                    
                        </table>
                    
            </div>
            
        </div>
        
    </div>
    
</div>



@endsection

