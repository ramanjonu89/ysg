@extends('layouts.app')
@section('content')
<div class="pad-all text-center">
    <form class="" action="{{ route('stock_report.index') }}" method="GET">
        <div class="box-inline mar-btm pad-rgt">
            {{ __('Seller Name') }}:
            <div class="select">
                <select class=" demo-select2" name="shop_name" required>
                    @foreach ($shops as $key => $seller)
                        <option value="{{ $seller->user_id }}"  @if (request()->get('shop_name', '') == $seller->user_id) selected @endif>{{ __($seller->name) }}</option>
                    @endforeach
                     <option value="0"  @if (request()->get('shop_name', '') === 0) selected @endif>All</option>
                </select>
            </div>
        </div>
        <div class="box-inline mar-btm pad-rgt">
            {{ __('Sort by Category') }}:
            <div class="select">
                <select id="demo-ease" class="demo-select2" name="category_id" required>
                    @foreach ($categories as $key => $category)
                         <option value="{{ $category->id }}"  @if (request()->get('category_id', '') == $category->id) selected @endif>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button class="btn btn-default" type="submit">{{ __('Filter') }}</button>
    </form>
</div>
<div class="col-md-offset-2 col-md-8">
    <div class="panel">
        <!--Panel heading-->
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Product wise stock report') }}</h3>
        </div>
     @if (request()->has('shop_name'))
        <!--Panel body-->
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped mar-no demo-dt-basic">
                    <thead>
                        <tr>
                            <th>{{ __('Product Name') }}</th>
                            <th>{{ __('Product SKU') }}</th>
                            <th>{{ __('Shop Name') }}</th>
                            <th>{{ __('Stock') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $key => $product)
                            {{-- @php
                            $qty = 0;
                            if ($product->variant_product) {
                                foreach ($product->stocks as $key => $stock) {
                                    $qty += $stock->qty;
                                }
                            } else {
                                $qty = $product->current_stock;
                            }
                            @endphp --}}
                            <tr>
                                <td>{{ __($product->name) }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>{{ $product->shopName }}</td>
                                <td>{{ $product->stocks > 0 ? $product->stocks : $product->current_stock }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
