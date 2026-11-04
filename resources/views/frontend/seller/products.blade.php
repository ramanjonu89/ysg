@extends('frontend.layouts.app')
@section('content')
    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @include('frontend.inc.seller_side_nav')
                </div>
                <div class="col-lg-9">
                    <div class="main-content">
                        <!-- Page title -->
                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                        {{ __('Products') }}
                                    </h2>
                                </div>

                                <div class="col-md-6">
                                    <div class="float-md-right">
                                        @include('frontend.inc.breadcrumb', [
                                            'breadcrumbs' => [
                                                [
                                                    'link' => route('dashboard'),
                                                    'text' => __('Dashboard'),
                                                ],
                                                [
                                                    'link' => route('seller.products'),
                                                    'text' => __('Products'),
                                                ],
                                            ],
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated)
                                <div class="col-md-4">
                                    <div class="dashboard-widget text-center green-widget text-white mt-4 c-pointer">
                                        <i class="la la-dropbox"></i>
                                        <span
                                                class="d-block title heading-3 strong-400">{{ max(0, Auth::user()->seller->remaining_uploads) }}</span>
                                        <span class="d-block sub-title">{{ __('Remaining Uploads') }}</span>
                                    </div>
                                </div>
                            @endif

                            @if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated)
                                @php
                                    $seller_package = \App\SellerPackage::find(Auth::user()->seller->seller_package_id);
                                @endphp
                                <div class="col-md-4">
                                    <a href="{{ route('seller_packages_list') }}"
                                       class="dashboard-widget text-center red-widget text-white mt-4 d-block">
                                        @if ($seller_package != null)
                                            <img alt="" src="{{ my_asset($seller_package->logo) }}" height="44"
                                                 class="img-fit mw-100 mx-auto mb-1">
                                            <span class="d-block sub-title mb-2">{{ __('Current Package') }}:
                                                {{ $seller_package->name }}</span>
                                        @else
                                            <i class="la la-frown-o mb-1"></i>
                                            <div class="d-block sub-title mb-2">{{ __('No Package Found') }}</div>
                                        @endif
                                        <div class="btn btn-styled btn-white btn-outline py-1">
                                            {{ __('Upgrade Package') }}</div>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="card no-border mt-4">
                            <div class="card-header py-2">
                                <div class="row align-items-center">
                                    <div class="col-md-6 col-xl-3">
                                        <h6 class="mb-0">All Products</h6>
                                    </div>

                                    <div class="col-md-6 col-xl-3 ml-auto" style="display:none">
                                        <form class="" action="" method="GET">
                                            <input type="text" class="form-control" id="search" name="search"
                                                   @isset($search) value="{{ $search }}" @endisset
                                                   placeholder="{{ __('Search product') }}">
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <table class="table table-sm table-hover table-responsive-md res-table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('SKU') }}</th>
                                        <th>{{ __('Current Qty') }}</th>
                                        <th>{{ __('Base Price') }}</th>
                                        <th>{{ __('Sales Price') }}</th>
                                        <th>{{ __('Discount Type') }}</th>
                                        <th>{{ __('Published') }}</th>
                                        <th>{{ __('Featured') }}</th>
                                        <th>{{ __('Options') }}</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach ($products as $key => $product)
                                        <tr>

                                            <td>{{ $key + 1 }}</td>
                                            <td><a href="{{ route('product', $product->slug) }}"
                                                   target="_blank">{{ $product->translation_name }}</a></td>
                                            <td>
                                                {{ $product->unit }}
                                            </td>

                                            <td>
                                                @php
                                                    $qty = 0;
                                                    if ($product->variant_product) {
                                                        foreach ($product->stocks as $stock) {
                                                            $qty += $stock->qty;
                                                        }
                                                    } else {
                                                        $qty = $product->current_stock;
                                                    }
                                                    if ($qty < 0) {
                                                        echo '0';
                                                    } else {
                                                        echo $qty;
                                                    }
                                                @endphp
                                            </td>

                                            <td>{{ $product->unit_price }}</td>
                                            <td>

                                                @php
                                                    if ($product->discount_type == 'percent') {
                                                        $salesamount = number_format($product->unit_price - ($product->unit_price * $product->discount) / 100, 2, '.', '');
                                                    } else {
                                                        $salesamount = number_format($product->unit_price - $product->discount, 2, '.', '');
                                                    }
                                                @endphp
                                                {{ $salesamount }}

                                            </td>
                                            <td>
                                                @if($product->discount_type == 'amount')
                                                    $
                                                @endif
                                                {{ $product->discount }}
                                                @if($product->discount_type == 'percent')
                                                    %
                                                @endif
                                            </td>
                                            <td><label class="switch">
                                                    <input onchange="update_published(this)" value="{{ $product->id }}"
                                                           type="checkbox" {{$product->published == 1 ? 'checked' : ''}}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>

                                            <td>
                                                <label class="switch">
                                                    <input onchange="update_featured(this)" value="{{ $product->id }}"
                                                           type="checkbox" {{$product->featured == 1 ? 'checked' : ''}}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn" type="button"
                                                            id="dropdownMenuButton-{{ $key }}"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>

                                                    <div class="dropdown-menu dropdown-menu-right"
                                                         aria-labelledby="dropdownMenuButton-{{ $key }}">
                                                        <a href="{{ route('seller.products.edit', encrypt($product->id)) }}"
                                                           class="dropdown-item">{{ __('Edit') }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript">
      function update_featured(el) {

        console.log(el.checked);

        const status = (el.checked)?1:0;

        const url = '/seller/products/' + el.value + '/featured'; // router('seller.products.featured');

        $.post(url, {
          _token: '{{ csrf_token() }}',
          id: el.value,
          status: status
        }, function (data) {
          if (parseInt(data) === 1) {
            showFrontendAlert('success', 'Featured products updated successfully');
          } else {
            showFrontendAlert('danger', 'Something went wrong');
          }
        });
      }

      function update_published(el) {
        const status = (el.checked) ? 1 : 0;

        $.post('{{ route('products.published') }}', {
          _token: '{{ csrf_token() }}',
          id: el.value,
          status: status
        }, function (data) {
            if (parseInt(data) === 1) {
                showFrontendAlert('success', 'Published products updated successfully');
            } else {
                showFrontendAlert('danger', 'Something went wrong');
            }
        });
      }
    </script>
@endsection
<link href="{{ static_asset('css/bootstrap.min.css') }}" rel="stylesheet">
