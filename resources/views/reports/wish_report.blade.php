@extends('layouts.app')
@section('content')
    <div class="pad-all text-center">
        <form class="" action="{{ route('wish_report.index') }}" method="GET">
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
                 {{ __('Sort by Category') }}:
                 <div class="select">
                     <select id="demo-ease" class="demo-select2" name="category_id" required>
                         @foreach (\App\Models\Category::all() as $key => $category)
                          <option value="{{ $category->id }}"  @isset($_GET['category_id']) @if ($_GET['category_id'] == $category->id) selected @endif @endisset>{{ $category->name  }}</option>
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
                <h3 class="panel-title">{{ __('Product Wish Report') }}</h3>
            </div>
    @if (isset($_GET['shop_name']))
            <!--Panel body-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped mar-no demo-dt-basic">
                        <thead>
                            <tr>
                                <th>{{ __('Product Name') }}</th>
                                <th>{{ __('Shop Name') }}</th>
                                <th>{{ __('Number of Wish') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $key => $product)
                                @if($product->wishlists != null)
                                @if ($_GET['shop_name'] > 0)
                                @if($product->user->id == $_GET['shop_name'])
                                    <tr>
                                        <td>{{ __($product->name) }}</td>
                                        <td>{{ __($product->user->name) }}</td>
                                        <td>{{ $product->wishlists->count() }}</td>
                                    </tr>
                                @endif
                                @else
                                    <tr>
                                        <td>{{ __($product->name) }}</td>
                                        <td>{{ __($product->user->name) }}</td>
                                        <td>{{ $product->wishlists->count() }}</td>
                                    </tr>
                                @endif
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
  @endif
        </div>
    </div>
@endsection
