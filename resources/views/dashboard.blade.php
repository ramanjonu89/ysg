@extends('layouts.app')
@section('content')
@if(env('MAIL_USERNAME') == null && env('MAIL_PASSWORD') == null)
<div class="row">
    <div class="col-sm-12">
        <div class="bg-danger pad-all text-center mar-btm">
            <h4 class="text-light mar-btm">{{ __('Please Configure SMTP Setting to work all email sending funtionality') }}.</h4>
            <a class="btn btn-info btn-rounded" href="{{ route('smtp_settings.index') }}">Configure Now</a>
        </div>
    </div>
</div>
@endif
@if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))
<div class="row">
    <div class="col-md-6">
        <div class="panel">
            <div class="panel-body text-center dash-widget dash-widget-left">
                <div class="dash-widget-vertical">
                    <div class="rorate">{{ __('PRODUCTS') }}</div>
                </div>
                <div class="pad-ver mar-top text-main">
                    <i class="demo-pli-data-settings icon-4x"></i>
                </div>
                <br>
                <p class="text-lg text-main">{{ __('Total published products') }}: <span class="text-bold">{{ $totalPublishedProducts }}</span></p>
                @if (\App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
                <p class="text-lg text-main">{{ __('Total sellers products') }}: <span class="text-bold">{{ $totalSellerPublishedProducts }}</span></p>
                @endif
                <p class="text-lg text-main">{{ __('Total admin products') }}: <span class="text-bold">{{ $totalAdminPublishedProducts }}</span></p>
                <br>
                <a href="{{ route('products.admin') }}" class="btn btn-primary mar-top">{{ __('Manage Products') }} <i class="fa fa-long-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-sm-6">
                <div class="panel">
                    <div class="pad-top text-center dash-widget">
                        <p class="text-normal text-main">{{ __('Total product category') }}</p>
                        <p class="text-semibold text-3x text-main">{{ $categories->count() }}</p>
                        <a href="{{ route('categories.create') }}" class="btn btn-primary mar-top btn-block top-border-radius-no">{{ __('Create Category') }}</a>
                    </div>
                </div>
                <div class="panel">
                    <div class="pad-top text-center dash-widget">
                        <p class="text-normal text-main">{{ __('Total product sub sub category') }}</p>
                        <p class="text-semibold text-3x text-main">{{ \App\Models\SubSubCategory::all()->count() }}</p>
                        <a href="{{ route('subsubcategories.create') }}" class="btn btn-primary mar-top btn-block top-border-radius-no">{{ __('Create Sub Sub Category') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel">
                    <div class="pad-top text-center dash-widget">
                        <p class="text-normal text-main">{{ __('Total product sub category') }}</p>
                        <p class="text-semibold text-3x text-main">{{ \App\Models\SubCategory::all()->count() }}</p>
                        <a href="{{ route('subcategories.create') }}" class="btn btn-primary mar-top btn-block top-border-radius-no">{{ __('Create Sub Category') }}</a>
                    </div>
                </div>
                <div class="panel">
                    <div class="pad-top text-center dash-widget">
                        <p class="text-normal text-main">{{ __('Total product brand') }}</p>
                        <p class="text-semibold text-3x text-main">{{ \App\Brand::all()->count() }}</p>
                        <a href="{{ route('brands.create') }}" class="btn btn-primary mar-top btn-block top-border-radius-no">{{ __('Create Brand') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@if((Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions))) && \App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
<div class="row">
    <div class="col-md-4">
        <div class="panel">
            <div class="panel-body text-center dash-widget dash-widget-left">
                <div class="dash-widget-vertical">
                    <div class="rorate">{{ __('ORDERS') }}</div>
                </div>
                <br>
                @php
                $totalitemunitsold = \App\OrderDetail::whereDate('created_at', \Carbon\Carbon::today())->where('payment_status','paid')->where('delivery_status','!=','cancelled')->sum('quantity');
                @endphp
                <p class="text-normal text-main">{{ __('Total Item Sold ') }}</p>
                <p class="text-semibold text-3x text-main">{{ $totalitemunitsold }}</p>
                <br>
                <br>
                <br>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel">
            <div class="panel-body text-center dash-widget">
                <br>
                @php
                $dailytotalsales = \App\Order::whereDate('created_at', \Carbon\Carbon::today())->where('payment_status','paid')->sum('grand_total');$todaydate = \Carbon\Carbon::today()->toDateString();
                    $totalorders = DB::select( DB::raw("select count(id) as totalorders from orders where id in (SELECT order_id FROM `order_details` where  payment_status = 'paid' and delivery_status != 'cancelled' and created_at like '$todaydate%' group by (order_id))") );
                foreach ($totalorders as $totalorder) {
                $totalorder_1 = $totalorder->totalorders;
                }
                @endphp
                <p class="text-normal text-main">{{ __('Today Sales ') }}</p>
                <p class="text-semibold text-3x text-main">${{ $dailytotalsales }}</p>
                <br>
                <br>
                <br>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel">
            <div class="panel-body text-center dash-widget">
                <br>
                    @php
                    $dailytotalsales = \App\Order::whereDate('created_at', \Carbon\Carbon::today())->where('payment_status','paid')->sum('grand_total');
					$todaydate = \Carbon\Carbon::today()->toDateString();
                    $totalorders = DB::select( DB::raw("select count(id) as totalorders from orders where id in (SELECT order_id FROM `order_details` where  payment_status = 'paid' and delivery_status != 'cancelled' and created_at like '$todaydate%' group by (order_id))") );
					foreach ($totalorders as $totalorder) {
						$totalorder_1 = $totalorder->totalorders;
					}
                    @endphp
                <p class="text-normal text-main">{{ __('Today Orders') }}</p>
                <p class="text-semibold text-3x text-main">{{ $totalorder_1 }}</p>
                <br>
                <br>
            </div>
        </div>
    </div>
  </div>
   <div class="row">
    <div class="col-md-4">
        <div class="panel">
            <div class="panel-body text-center dash-widget">
                <br>
                <p class="text-normal text-main">{{ __('Month To Date Total Sales') }}</p>
                <p class="text-semibold text-3x text-main">${{ $monthtodatetotalsales }}</p>
                <br>
                <br>
            </div>
        </div>
    </div>
     <div class="col-md-4">
        <div class="panel">
            <div class="panel-body text-center dash-widget">
                <br>
                <p class="text-normal text-main">{{ __('Month To Date Item Sold') }}</p>
                <p class="text-semibold text-3x text-main">{{ $monthtodateitemsold }}</p>
                <br>
                <br>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel">
            <div class="panel-body text-center dash-widget">
                <br>
                <p class="text-normal text-main">{{ __('Month To Date Total order') }}</p>
                <p class="text-semibold text-3x text-main">{{ $monthtodatetotalorders }}</p>
                <br>
                <br>
            </div>
        </div>
    </div>
</div>
@endif
@if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))
    <div class="row">
    <div class="col-md-6">
        <div class="panel">
            <!--Panel heading-->
            <div class="panel-heading">
                <h3 class="panel-title">{{ __('Category wise product sale') }}</h3>
            </div>
            <!--Panel body-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mar-no">
                        <thead>
                            <tr>
                                <th>{{ __('Category Name') }}</th>
                                <th>{{ __('Sale') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $key => $category)
                                <tr>
                                    <td>{{ $category->getTranslationNameAttribute() }}</td>
                                    <td>{{ $category->product_sales }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel">
            <!--Panel heading-->
            <div class="panel-heading">
                <h3 class="panel-title">{{ __('Category wise product stock') }}</h3>
            </div>
            <!--Panel body-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mar-no">
                        <thead>
                            <tr>
                                <th>{{ __('Category Name') }}</th>
                                <th>{{ __('Stock') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $key => $category)
                                <tr>
                                    <td>{{ $category->getTranslationNameAttribute()  }}</td>
                                    <td>{{ $category->product_stocks }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@if(Auth::user()->user_type == 'admin' || in_array('9', json_decode(Auth::user()->staff->role->permissions)))
    <div class="row">
    <div class="col-md-6">
        <div class="panel">
            <div class="panel-body text-center dash-widget pad-no">
                <div class="pad-ver mar-top text-main">
                    <i class="demo-pli-data-settings icon-4x"></i>
                </div>
                <br>
                <p class="text-3x text-main bg-primary pad-ver">{{ __('Frontend') }}
                    <strong>{{ __('Setting') }}</strong>
                </p>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-sm-6">
                <div class="panel">
                    <div class="pad-top text-center dash-widget">
                        <p class="text-semibold text-lg text-main mar-ver">
                            {{ __('Home page') }} <br>
                            {{ __('setting') }}
                        </p>
                        <br>
                        <a href="{{ route('frontend_settings.home.index') }}" class="btn btn-primary mar-top btn-block top-border-radius-no">{{ __('Click Here') }}</a>
                    </div>
                </div>
                <div class="panel">
                    <div class="pad-top text-center dash-widget">
                        <p class="text-semibold text-lg text-main mar-ver">
                            {{ __('Policy page') }} <br>
                            {{ __('setting') }}
                        </p>
                        <br>
                        <a href="{{route('privacypolicy.index', 'privacy_policy') }}" class="btn btn-primary mar-top btn-block top-border-radius-no">{{ __('Click Here') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel">
                    <div class="pad-top text-center dash-widget">
                        <p class="text-semibold text-lg text-main mar-ver">
                            {{ __('General') }} <br>
                            {{ __('setting') }}
                        </p>
                        <br>
                        <a href="{{route('generalsettings.index') }}" class="btn btn-primary mar-top btn-block top-border-radius-no">{{ __('Click Here') }}</a>
                    </div>
                </div>
                <div class="panel">
                    <div class="pad-top text-center dash-widget">
                        <p class="text-semibold text-lg text-main mar-ver">
                            {{ __('Useful link') }} <br>
                            {{ __('setting') }}
                        </p>
                        <br>
                        <a href="{{route('links.index') }}" class="btn btn-primary mar-top btn-block top-border-radius-no">{{ __('Click Here') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection