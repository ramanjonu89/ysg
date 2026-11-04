@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-sm-12">
        <a href="{{ route('customers.export-all', array_merge(['keyword' => ''], request()->query())) }}" class="btn btn-success export-email-btn">{{ __('Export via Email') }}</a>
        <!-- <a href="{{ route('sellers.create') }}" class="btn btn-info pull-right">{{ __('add_new') }}</a> -->
        </div>
    </div>
    <br>
    <!-- Basic Data Tables -->
    <!--===================================================-->
    <div class="panel">
        <div class="panel-heading bord-btm clearfix pad-all h-100">
            <h3 class="panel-title pull-left pad-no">{{ __('Customers') }}</h3>
            <div class="pull-right clearfix">
                <form class="" id="sort_customers" action="" method="GET">
                    <div id="filters-container">
                        @if(!empty($filters) && count($filters) > 0)
                            @foreach($filters as $index => $filter)
                                <div class="filter-row" style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px; justify-content: flex-end;">
                                    <div style="min-width: 150px;">
                                        <select class="form-control" name="filters[{{ $index }}][column]">
                                            <option value="name" {{ ($filter['column'] ?? '') == 'name' ? 'selected' : '' }}>Name</option>
                                            <option value="email" {{ ($filter['column'] ?? '') == 'email' ? 'selected' : '' }}>Email Address</option>
                                            <option value="phone" {{ ($filter['column'] ?? '') == 'phone' ? 'selected' : '' }}>Phone</option>
                                            <option value="delivery_address" {{ ($filter['column'] ?? '') == 'delivery_address' ? 'selected' : '' }}>Delivery Address</option>
                                            <option value="delivery_contact" {{ ($filter['column'] ?? '') == 'delivery_contact' ? 'selected' : '' }}>Delivery Contact</option>
                                        </select>
                                    </div>
                                    <div style="min-width: 150px;">
                                        <select class="form-control" name="filters[{{ $index }}][condition]">
                                            <option value="contains" {{ ($filter['condition'] ?? '') == 'contains' ? 'selected' : '' }}>Contains</option>
                                            <option value="not_contains" {{ ($filter['condition'] ?? '') == 'not_contains' ? 'selected' : '' }}>Does not contain</option>
                                            <option value="starts_with" {{ ($filter['condition'] ?? '') == 'starts_with' ? 'selected' : '' }}>Starts with</option>
                                            <option value="ends_with" {{ ($filter['condition'] ?? '') == 'ends_with' ? 'selected' : '' }}>Ends with</option>
                                            <option value="equals" {{ ($filter['condition'] ?? '') == 'equals' ? 'selected' : '' }}>Equals</option>
                                            <option value="not_equals" {{ ($filter['condition'] ?? '') == 'not_equals' ? 'selected' : '' }}>Does not equal</option>
                                        </select>
                                    </div>
                                    <div style="width: 150px;">
                                        <input type="text" class="form-control" name="filters[{{ $index }}][keyword]" value="{{ $filter['keyword'] ?? '' }}" placeholder="Keyword">
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-danger btn-sm remove-filter" title="Remove filter">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="filter-row" style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px; justify-content: flex-end;">
                                <div style="min-width: 150px;">
                                    <select class="form-control" name="filters[0][column]">
                                        <option value="name">Name</option>
                                        <option value="email">Email Address</option>
                                        <option value="phone">Phone</option>
                                        <option value="delivery_address">Delivery Address</option>
                                        <option value="delivery_contact">Delivery Contact</option>
                                    </select>
                                </div>
                                <div style="min-width: 150px;">
                                    <select class="form-control" name="filters[0][condition]">
                                        <option value="contains">Contains</option>
                                        <option value="not_contains">Does not contain</option>
                                        <option value="starts_with">Starts with</option>
                                        <option value="ends_with">Ends with</option>
                                        <option value="equals">Equals</option>
                                        <option value="not_equals">Does not equal</option>
                                    </select>
                                </div>
                                <div style="width: 150px;">
                                    <input type="text" class="form-control" name="filters[0][keyword]" placeholder="Keyword">
                                </div>
                                <div>
                                    <button type="button" class="btn btn-danger btn-sm remove-filter" title="Remove filter">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div style="margin-top: 10px; text-align: right;">
                        <button type="button" class="btn btn-info" id="add-filter">
                            <i class="fa fa-plus"></i> Add Filter
                        </button>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-default">Reset</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped res-table mar-no dataTable" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            @php
                                $nameSortUrl = $sort_field == 'name' && $sort_direction == 'asc'
                                    ? request()->fullUrlWithQuery(['sort_field' => 'name', 'sort_direction' => 'desc'])
                                    : ($sort_field == 'name' && $sort_direction == 'desc'
                                        ? request()->fullUrlWithQuery(['sort_field' => null, 'sort_direction' => null])
                                        : request()->fullUrlWithQuery(['sort_field' => 'name', 'sort_direction' => 'asc']));
                            @endphp
                            <a href="{{ $nameSortUrl }}" class="sort-link">
                                {{ __('Name') }}
                                @if($sort_field == 'name')
                                    @if($sort_direction == 'asc')
                                        <i class="fa fa-sort-asc sort-icon"></i>
                                    @elseif($sort_direction == 'desc')
                                        <i class="fa fa-sort-desc sort-icon"></i>
                                    @endif
                                @else
                                    <i class="fa fa-sort sort-icon inactive"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            @php
                                $emailSortUrl = $sort_field == 'email' && $sort_direction == 'asc'
                                    ? request()->fullUrlWithQuery(['sort_field' => 'email', 'sort_direction' => 'desc'])
                                    : ($sort_field == 'email' && $sort_direction == 'desc'
                                        ? request()->fullUrlWithQuery(['sort_field' => null, 'sort_direction' => null])
                                        : request()->fullUrlWithQuery(['sort_field' => 'email', 'sort_direction' => 'asc']));
                            @endphp
                            <a href="{{ $emailSortUrl }}" class="sort-link">
                                {{ __('Email Address') }}
                                @if($sort_field == 'email')
                                    @if($sort_direction == 'asc')
                                        <i class="fa fa-sort-asc sort-icon"></i>
                                    @elseif($sort_direction == 'desc')
                                        <i class="fa fa-sort-desc sort-icon"></i>
                                    @endif
                                @else
                                    <i class="fa fa-sort sort-icon inactive"></i>
                                @endif
                            </a>
                        </th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Delivery address') }}</th>
                        <th>{{ __('Delivery Contact') }}</th>
                        <th>{{ __('Receive Special Offers') }}</th>
                        <th>
                            @php
                                $createdAtSortUrl = $sort_field == 'created_at' && $sort_direction == 'asc'
                                    ? request()->fullUrlWithQuery(['sort_field' => 'created_at', 'sort_direction' => 'desc'])
                                    : ($sort_field == 'created_at' && $sort_direction == 'desc'
                                        ? request()->fullUrlWithQuery(['sort_field' => null, 'sort_direction' => null])
                                        : request()->fullUrlWithQuery(['sort_field' => 'created_at', 'sort_direction' => 'asc']));
                            @endphp
                            <a href="{{ $createdAtSortUrl }}" class="sort-link">
                                {{ __('Created on') }}
                                @if($sort_field == 'created_at')
                                    @if($sort_direction == 'asc')
                                        <i class="fa fa-sort-asc sort-icon"></i>
                                    @elseif($sort_direction == 'desc')
                                        <i class="fa fa-sort-desc sort-icon"></i>
                                    @endif
                                @else
                                    <i class="fa fa-sort sort-icon inactive"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            @php
                                $createdAtSortUrl = $sort_field == 'last_login' && $sort_direction == 'asc'
                                    ? request()->fullUrlWithQuery(['sort_field' => 'last_login', 'sort_direction' => 'desc'])
                                    : ($sort_field == 'last_login' && $sort_direction == 'desc'
                                        ? request()->fullUrlWithQuery(['sort_field' => null, 'sort_direction' => null])
                                        : request()->fullUrlWithQuery(['sort_field' => 'last_login', 'sort_direction' => 'asc']));
                            @endphp
                            <a href="{{ $createdAtSortUrl }}" class="sort-link">
                                {{ __('Last Login At') }}
                                @if($sort_field == 'last_login')
                                    @if($sort_direction == 'asc')
                                        <i class="fa fa-sort-asc sort-icon"></i>
                                    @elseif($sort_direction == 'desc')
                                        <i class="fa fa-sort-desc sort-icon"></i>
                                    @endif
                                @else
                                    <i class="fa fa-sort sort-icon inactive"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            @php
                                $createdAtSortUrl = $sort_field == 'last_order' && $sort_direction == 'asc'
                                    ? request()->fullUrlWithQuery(['sort_field' => 'last_order', 'sort_direction' => 'desc'])
                                    : ($sort_field == 'last_order' && $sort_direction == 'desc'
                                        ? request()->fullUrlWithQuery(['sort_field' => null, 'sort_direction' => null])
                                        : request()->fullUrlWithQuery(['sort_field' => 'last_order', 'sort_direction' => 'asc']));
                            @endphp
                            <a href="{{ $createdAtSortUrl }}" class="sort-link">
                                {{ __('Last Order Date') }}
                                @if($sort_field == 'last_order')
                                    @if($sort_direction == 'asc')
                                        <i class="fa fa-sort-asc sort-icon"></i>
                                    @elseif($sort_direction == 'desc')
                                        <i class="fa fa-sort-desc sort-icon"></i>
                                    @endif
                                @else
                                    <i class="fa fa-sort sort-icon inactive"></i>
                                @endif
                            </a>
                        </th>
                        <th>{{ __('Payment Status')}}</th>
                        <th>{{ __('Total Successful Orders')}}</th>
                        <th>{{ __('Referral Code')}}</th>
                        <th>{{ __('Referral Code Used')}}</th>
                        <th>{{ __('Microsite') }}</th>
                        <th width="10%">{{ __('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customerKey => $customer)
                        @if ($customer->user != null)
                            @php
                                $addressIndex = 0;
                                $phoneIndex   = 0;
                            @endphp
                            <tr>
                                <td>{{ ($customers->currentPage() - 1) * $customers->perPage() + $customerKey + 1 }}</td>
                                <td>@if($customer->user->banned == 1) <i class="fa fa-ban text-danger" aria-hidden="true"></i> @endif {{$customer->user->name}}</td>
                                <td>{{$customer->user->email}}</td>
                                <td>{{$customer->user->phone}}</td>
                                <td>
                                    @foreach($customer->user->addresses as $address)
                                        @php $addressIndex++; @endphp
                                        @if($addressIndex > 1)
                                            ;
                                        @endif
                                        @if(!empty($address->address))
                                            {{ $address->address }}
                                        @endif
                                        @if(!empty($address->postal_code))
                                            , {{ $address->country }} {{ $address->postal_code }}
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($customer->user->addresses as $address)
                                        @php $phoneIndex++; @endphp
                                        @if($phoneIndex > 1)
                                            ;
                                        @endif
                                        @if(!empty($address->phone))
                                            {{ $address->fullPhone() }}
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{ $customer->user->referral_code_register }}</td>
                                <td>{{ $customer->user->created_at }}</td>
                                <td>{{ $customer->user->last_login }}</td>
                                <td>{{ $customer->last_order_date }}</td>
                                <td>{{ $customer->last_payment_status }}</td>
                                <td>{{ $customer->total_successful_orders }}</td>
                                <td>{{ $customer->user->referral_code_used }}</td>
                                <td>{{ $customer->user->introduced_by }}</td>
                                <td>{{ ($microsite = $customer->user->micrositeUsers) ? $microsite->id : '' }}</td>
                                <td>
                                    <div class="btn-group dropdown">
                                        <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                            {{ __('Actions') }} <i class="dropdown-caret"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="{{route('customers.login', encrypt($customer->id))}}">{{ __('Log in as this Customer') }}</a></li>
                                            @if($customer->user->banned != 1)
                                                <li><a href="#" onclick="confirm_ban('{{route('customers.ban', $customer->id)}}');">{{ __('Ban this Customer') }}  <i class="fa fa-ban text-danger" aria-hidden="true"></i> </a></li>
                                            @else
                                                <li><a href="#" onclick="confirm_unban('{{route('customers.ban', $customer->id)}}');">{{ __('Unban this Customer') }} <i class="fa fa-check text-success" aria-hidden="true"></i></a></li>
                                            @endif
                                                <li>
                                                    <a onclick="confirm_modal({{ json_encode(['item' => $customer->user->name, 'url' => route('customers.destroy', $customer->id)]) }});">
                                                        {{ __('Delete') }}
                                                    </a>
                                                </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-right">
                    {{ $customers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('style')
<style>
.sort-link {
    color: inherit;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.sort-link:hover {
    color: inherit;
    text-decoration: none;
}
.sort-icon {
    margin-left: 5px;
    font-size: 12px;
}
.sort-icon.inactive {
    opacity: 0.3;
}
.sort-icon:not(.inactive) {
    color: #007bff;
}
</style>
@endsection
@section('script')
    <script src="{{ static_asset_with_version('js/loading-overlay.min.js') }}"></script>
    <script type="text/javascript">
        function sort_customers(el){
            $('#sort_customers').submit();
        }
        function confirm_ban(url)
        {
            $('#confirm-ban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmation').setAttribute('href' , url);
        }
        function confirm_unban(url)
        {
            $('#confirm-unban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationunban').setAttribute('href' , url);
        }

        $(document).ready(function() {
            let filterIndex = $('.filter-row').length;

            $('#add-filter').on('click', function() {
                const newFilter = `
                    <div class="filter-row" style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px; justify-content: flex-end;">
                        <div style="min-width: 150px;">
                            <select class="form-control" name="filters[${filterIndex}][column]">
                                <option value="name">Name</option>
                                <option value="email">Email Address</option>
                                <option value="phone">Phone</option>
                                <option value="delivery_address">Delivery Address</option>
                                <option value="delivery_contact">Delivery Contact</option>
                            </select>
                        </div>
                        <div style="min-width: 150px;">
                            <select class="form-control" name="filters[${filterIndex}][condition]">
                                <option value="contains">Contains</option>
                                <option value="not_contains">Does not contain</option>
                                <option value="starts_with">Starts with</option>
                                <option value="ends_with">Ends with</option>
                                <option value="equals">Equals</option>
                                <option value="not_equals">Does not equal</option>
                            </select>
                        </div>
                        <div style="width: 150px;">
                            <input type="text" class="form-control" name="filters[${filterIndex}][keyword]" placeholder="Keyword">
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger btn-sm remove-filter" title="Remove filter">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#filters-container').append(newFilter);
                filterIndex++;
            });

            $(document).on('click', '.remove-filter', function() {
                if ($('.filter-row').length > 1) {
                    $(this).closest('.filter-row').remove();
                } else {
                    alert('You must have at least one filter');
                }
            });

            $(document).on('click', '.export-email-btn', function (e) {
                $.LoadingOverlay("show", {
                    size: 10,
                    image: "",
                    fontawesome: "fa fa-cog fa-spin"
                });
            });
        });

        $(document).ready(function() {
            $('.dataTable').DataTable({
                dom: 'lrt',
                paging: false,
                info: false,
                searching: false,
                lengthChange: false,
                responsive: true,
                autoWidth: false,
                destroy: true
            });
        });
    </script>
@endsection
@section('modal')
    <div class="modal fade" id="confirm-ban" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">{{ __('Confirmation') }}</h4>
                </div>
                <div class="modal-body">
                    <p>{{ __('Do you really want to ban this Customer?') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <a id="confirmation" class="btn btn-danger btn-ok">{{ __('Proceed!') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirm-unban" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">{{ __('Confirmation') }}</h4>
                </div>
                <div class="modal-body">
                    <p>{{ __('Do you really want to unban this Customer?') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <a id="confirmationunban" class="btn btn-success btn-ok">{{ __('Proceed!') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
