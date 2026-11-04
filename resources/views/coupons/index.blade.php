@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-8">
            <a href="{{ route('coupon.create', ['is_generation_bulk' => '1', 'is_cash_voucher' => $is_cash_voucher]) }}"
                class="btn btn-rounded btn-info pull-left">{{ __('Bulk Generation Coupons') }}</a>
            <a href="{{ route('coupon.create', ['is_generation_bulk' => '0', 'is_cash_voucher' => $is_cash_voucher]) }}"
                class="btn btn-rounded btn-info">{{ __('Add New Coupon') }}</a>
        </div>
        <div class="col-lg-4">
            <div class="pull-right">
                <div class="btn-group" style="margin-right: 10px;">
                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-file-excel-o"></i> {{ __('Export') }} <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#" onclick="exportCurrentPage()">{{ __('Export Current Page') }}</a></li>
                        <li><a href="#" onclick="exportAllData()">{{ __('Export All Data') }}</a></li>
                    </ul>
                </div>
                <form method="GET" action="{{ route('coupon.index') }}" class="form-inline" style="display: inline-block;">
                    @if($is_cash_voucher)
                        <input type="hidden" name="is_cash_voucher" value="{{ $is_cash_voucher }}">
                    @endif
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by coupon code" value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                    @if(request('search'))
                        <a href="{{ route('coupon.index', ['is_cash_voucher' => $is_cash_voucher]) }}" class="btn btn-secondary">{{ __('Clear') }}</a>
                    @endif
                </form>
            </div>
        </div>
    </div><br>
    <div class="panel">
        <div class="panel-heading">
            @if ($is_cash_voucher)
                <h3 class="panel-title">{{ __('Cash Voucher') }}</h3>
            @else
                <h3 class="panel-title">{{ __('Coupon Information') }}</h3>
            @endif
        </div>
        <div class="panel-body">
            @if($coupons->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Seller/Vendor') }}</th>
                                <th>{{ __('Usage/Total') }}</th>
                                <th>{{ __('Start Date') }}</th>
                                <th>{{ __('End Date') }}</th>
                                <th>{{ __('Created Date') }}</th>
                                <th>{{ __('Modified Date') }}</th>
                                @if (empty($is_cash_voucher))
                                    <th>{{ __('Published') }}</th>
                                @endif
                                <th>{{ __('Active') }}</th>
                                <th width="10%">{{ __('Options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $key => $coupon)
                                <tr>
                                    <td>{{ ($coupons->currentPage() - 1) * $coupons->perPage() + $key + 1 }}</td>
                                    <td>{{ $coupon->code }}</td>
                                    <td>
                                        @if ($coupon->type == 'cart_base')
                                            {{ __('Cart Base') }}
                                        @elseif ($coupon->type == 'product_base')
                                            {{ __('Product Base') }}
                                        @elseif ($coupon->type == 'category_base')
                                            {{ __('Category Base') }}
                                        @elseif ($coupon->type == 'cash_base')
                                            {{ __('Cash Base') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($coupon->shop)
                                            {{ $coupon->shop->name }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $coupon->number_of_uses }} / {{ $coupon->getTotalCoupon() }}
                                    </td>
                                    <td>{{ date('d-m-Y', $coupon->start_date) }}</td>
                                    <td>{{ date('d-m-Y', $coupon->end_date) }}</td>
                                    <td>{{ $coupon->created_at }}</td>
                                    <td>{{ $coupon->updated_at }}</td>
                                    @if (empty($is_cash_voucher))
                                        <td><label class="switch">
                                            <input onchange="update_published(this)" value="{{ $coupon->id }}" type="checkbox" @if ($coupon->published === 1) checked @endif>
                                            <span class="slider round"></span></label>
                                        </td>
                                    @endif
                                    <td><label class="switch">
                                        <input onchange="toggle_active(this)" value="{{ $coupon->id }}" type="checkbox" @if ($coupon->is_active === 1) checked @endif>
                                        <span class="slider round"></span></label>
                                    </td>
                                    <td>
                                        <div class="btn-group dropdown">
                                            <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon"
                                                data-toggle="dropdown" type="button">
                                                {{ __('Actions') }} <i class="dropdown-caret"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a
                                                        href="{{ route('coupon.edit', $coupon->id) }}">{{ __('Edit') }}</a>
                                                </li>
                                                <li>
                                                    <a
                                                        onclick="confirm_modal({{ json_encode(['item' => $coupon->code, 'url' => route('coupon.destroy', $coupon->id)]) }});">
                                                        {{ __('Delete') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="dataTables_info">
                            Showing {{ $coupons->firstItem() }} to {{ $coupons->lastItem() }} of {{ $coupons->total() }} entries
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dataTables_paginate paging_simple_numbers pull-right">
                            {{ $coupons->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center">
                    <p>{{ __('No coupons found') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
    <script type="text/javascript">
        function update_published(el) {
            $.post('{{ route('coupons.published') }}', {
                _token:'{{ csrf_token() }}',
                id:el.value,
            }, function(data){
                data = JSON.parse(data);
                if (data.status === 1) {
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message);
                }
            });
        }

        function toggle_active(el) {
            $.post('{{ route('coupons.toggle_active') }}', {
                _token:'{{ csrf_token() }}',
                id:el.value,
            }, function(data){
                data = JSON.parse(data);
                if (data.status === 1) {
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message);
                }
            });
        }

        function exportCurrentPage() {
            let table = document.querySelector('.table');
            if (!table) {
                alert('No data to export');
                return;
            }
            
            let clonedTable = table.cloneNode(true);
            
            let actionCells = clonedTable.querySelectorAll('th:last-child, td:last-child');
            actionCells.forEach(cell => cell.remove());
            
            let switchCells = clonedTable.querySelectorAll('.switch');
            switchCells.forEach(switchCell => {
                let isChecked = switchCell.querySelector('input[type="checkbox"]').checked;
                switchCell.parentElement.textContent = isChecked ? 'Active' : 'Inactive';
            });
            
            let filename = @if($is_cash_voucher) 'cash_vouchers_current_page_' @else 'coupons_current_page_' @endif + new Date().toISOString().slice(0,10) + '.xlsx';
            
            TableToExcel.convert(clonedTable, {
                name: filename,
                sheet: {
                    name: @if($is_cash_voucher) 'Cash Vouchers' @else 'Coupons' @endif
                }
            });
        }

        function exportAllData() {
            let params = new URLSearchParams();
            @if($is_cash_voucher)
                params.append('is_cash_voucher', '{{ $is_cash_voucher }}');
            @endif
            
            @if(request('search'))
                params.append('search', '{{ request('search') }}');
            @endif
            
            let url = '{{ route('coupon.export_all') }}';
            if (params.toString()) {
                url += '?' + params.toString();
            }
            
            // Simple redirect to download
            window.location.href = url;
        }
    </script>
@endsection

