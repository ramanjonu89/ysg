@extends('frontend.layouts.app')
@section('content')
    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @include('frontend.inc.seller_side_nav')
                </div>
                <div class="col-lg-9">
                    <!-- Page title -->
                    <div class="page-title">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                    {{ __('Dashboard') }}
                                </h2>
                            </div>
                            <div class="col-md-6">
                                <div class="float-md-right">
                                    @include('frontend.inc.breadcrumb', ['breadcrumbs' => [
                                        [
                                            'link' => route('dashboard'),
                                            'text' => __('Dashboard')
                                        ],
                                    ]])
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- dashboard content -->
                    <div class="">
                        <div class="row">
                            <div class="col-md-3 col-6">
                                <div class="dashboard-widget text-center green-widget mt-4 c-pointer">
                                    <a href="{{ route('seller.products') }}" class="d-block">
                                        <i class="fa fa-upload"></i>
                                        <span class="d-block title heading-3 strong-400">{{ count(\App\Product::where('user_id', Auth::user()->id)->get()) }}</span>
                                        <span class="d-block sub-title">{{ __('Products') }}</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="dashboard-widget text-center red-widget mt-4 c-pointer">
                                    <a href="{{ route('orders.index') }}" class="d-block">
                                        <i class="fa fa-cart-plus"></i>
                                        <span class="d-block title heading-3 strong-400">
                                            @php
                                        $orders = DB::table('orders')
                                                    ->orderBy('code', 'desc')
                                                    ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                                                    ->where('order_details.seller_id', Auth::user()->id)
                                                    ->select('orders.id')
                                                    ->distinct()
                                                    ->count();
                                    @endphp
                                            {{ $orders }}
                                            </span>
                                        <span class="d-block sub-title">{{ __('Total sale') }}</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="dashboard-widget text-center blue-widget mt-4 c-pointer">
                                    <a href="{{ route('orders.index') }}" class="d-block">
                                        <i class="fa fa-dollar"></i>
                                        <span class="d-block title heading-3 strong-400">{{ single_price($vc_revenue) }}</span>
                                        <span class="d-block sub-title">{{ __('Total earnings') }}</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="dashboard-widget text-center yellow-widget mt-4 c-pointer">
                                    <a href="{{ route('orders.index') }}" class="d-block">
                                        <i class="fa fa-check-square-o"></i>
                                        <span class="d-block title heading-3 strong-400">{{ count(\App\OrderDetail::where('seller_id', Auth::user()->id)->where('delivery_status', 'delivered')->get()) }}</span>
                                        <span class="d-block sub-title">{{ __('Successful orders') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-box bg-white mt-4">
                                    <div class="form-box-title px-3 py-2 text-center">
                                        {{ __('Orders') }}
                                    </div>
                                    <div class="form-box-content p-3">
                                        <table class="table mb-0 table-bordered" style="font-size:14px;">
                                            <tr>
                                                <td>{{ __('Total Product Sold') }}:</td>
                                                <td><strong class="heading-6">{{ count(\App\OrderDetail::where('payment_status','paid')->where('seller_id', Auth::user()->id)->get()) }}</strong></td>
                                            </tr>
                                            <tr >
                                                <td>{{ __('Pending product delivery') }}:</td>
                                                <td><strong class="heading-6">{{ count(\App\OrderDetail::where('seller_id', Auth::user()->id)->where('delivery_status', 'pending')->get()) }}</strong></td>
                                            </tr>
                                            <tr >
                                                <td>{{ __('Cancelled orders by products') }}:</td>
                                                <td><strong class="heading-6">{{ count(\App\OrderDetail::where('seller_id', Auth::user()->id)->where('delivery_status', 'cancelled')->get()) }}</strong></td>
                                            </tr>
                                            <tr >
                                                <td>{{ __('Successful delivery by products') }}:</td>
                                                <td><strong class="heading-6">{{ count(\App\OrderDetail::where('seller_id', Auth::user()->id)->where('delivery_status', 'delivered')->get()) }}</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-lg-12">
                        </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-box bg-white mt-4">
                                    <div class="form-box-title px-3 py-2 text-center">
                                        {{ __('Products') }}
                                    </div>
                                    <div class="form-box-content p-3 category-widget">
                                        <ul class="clearfix">
                                            @foreach ($categories as $key => $category)
                                            <li><a>{{ $category->getTranslationNameAttribute() }}<span>({{ count($category->products->where(['user_id' =>  Auth::user()->id])) }})</span></a></li>
                                            @endforeach
                                        </ul>
                                        <!--<div class="text-center">
                                            <a href="{{ route('seller.products.upload') }}" class="btn pt-3 pb-1">{{ __('Add New Product') }}</a>
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
