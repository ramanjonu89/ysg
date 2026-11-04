@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{ route('admin.sellers.export-all', ['keyword' => $sort_search, 'approved_status' => $approved]) }}" class="btn btn-success">{{ __('Export All to Excel') }}</a>
            <a href="{{ route('sellers.create') }}" class="btn btn-rounded btn-info pull-right">{{ __('Add New Seller') }}</a>
        </div>
    </div>
    <br>
    <!-- Sellers List with Backend Sorting -->
    <!--===================================================-->
    <div class="panel">
        <div class="panel-heading bord-btm clearfix pad-all h-100">
            <h3 class="panel-title pull-left pad-no">{{ __('Sellers') }}</h3>
            <div class="pull-right clearfix">
                <form class="" id="sort_sellers" action="" method="GET">
                    <div class="box-inline pad-rgt pull-left">
                        <div class="select" style="min-width: 300px;">
                            <select class="form-control demo-select2" name="approved_status" id="approved_status" onchange="sort_sellers()">
                                <option value="">{{ __('Filter by Approval') }}</option>
                                <option value="1"  @isset($approved) @if($approved == '1') selected @endif @endisset>{{ __('Approved') }}</option>
                                <option value="0"  @isset($approved) @if($approved == '0') selected @endif @endisset>{{ __('Non-Approved') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="box-inline pad-rgt pull-left">
                        <div class="" style="min-width: 200px;">
                            <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ __('Type name or email & Enter') }}">
                        </div>
                    </div>
                    <input type="hidden" name="sort_by" id="sort_by" value="{{ $sort_by ?? 'index' }}">
                    <input type="hidden" name="sort_direction" id="sort_direction" value="{{ $sort_direction ?? 'asc' }}">
                </form>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped sellers-table mar-no" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('ID (user)') }}</th>
                    <th class="sortable" data-sort="name">
                        {{ __('Name') }}
                        @if($sort_by == 'name')
                            <i class="fa fa-sort-{{ $sort_direction == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fa fa-sort"></i>
                        @endif
                    </th>
                    <th class="sortable" data-sort="phone">
                        {{ __('Phone') }}
                        @if($sort_by == 'phone')
                            <i class="fa fa-sort-{{ $sort_direction == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fa fa-sort"></i>
                        @endif
                    </th>
                    <th class="sortable" data-sort="email">
                        {{ __('Email Address') }}
                        @if($sort_by == 'email')
                            <i class="fa fa-sort-{{ $sort_direction == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fa fa-sort"></i>
                        @endif
                    </th>
                    <th class="sortable" data-sort="verification_status">
                        {{ __('Approval') }}
                        @if($sort_by == 'verification_status')
                            <i class="fa fa-sort-{{ $sort_direction == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fa fa-sort"></i>
                        @endif
                    </th>
                    <th>{{ __('Display') }}</th>
                    <th>{{ __('Shipping Method') }}</th>
                    <th class="sortable" data-sort="products_count">
                        {{ __('Num. of Products') }}
                        @if($sort_by == 'products_count')
                            <i class="fa fa-sort-{{ $sort_direction == 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fa fa-sort"></i>
                        @endif
                    </th>
                    <th>{{ __('Total Sales (Excl. Shipping') }}</th>
                    <th width="10%">{{ __('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($sellers as $key => $seller)
                    @if($seller->user != null)
                        <tr>
                            <td>
                                <span class="item-index">{{ ($key+1) + ($sellers->currentPage() - 1)*$sellers->perPage() }}</span>
                                <input class="item-id" type="hidden" value="{{ $seller->id }}"/>
                            </td>
                            <td>{{$seller->user->id}}</td>
                            <td>@if($seller->user->banned == 1) <i class="fa fa-ban text-danger" aria-hidden="true"></i> @endif {{$seller->user->name}}</td>
                            <td>{{$seller->user->phone}}</td>
                            <td>{{$seller->user->email}}</td>
                            <td>
                                <label class="switch">
                                    <input onchange="update_approved(this)" value="{{ $seller->id }}" type="checkbox" <?php if($seller->verification_status == 1) echo "checked";?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                <label class="switch">
                                    <input onchange="toggle_display(this)" value="{{ $seller->id }}" type="checkbox" <?php if($seller->display == 1) echo "checked";?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                @php
                                    $shop = $seller->user->shop;
                                    $orderType = $shop ? $shop->order_type : null;
                                @endphp
                                @if($orderType == \App\Models\Shop::ORDER_TYPE_DELIVERY)
                                    <span class="badge badge-info">Home</span>
                                @elseif($orderType == \App\Models\Shop::ORDER_TYPE_SELF_COLLECTION)
                                    <span class="badge badge-warning">Self</span>
                                @elseif($orderType == \App\Models\Shop::ORDER_TYPE_BOTH)
                                    <span class="badge badge-success">Both</span>
                                @else
                                    <span class="badge badge-secondary">Not Set</span>
                                @endif
                            </td>
                            <td>{{ \App\Product::where('user_id', $seller->user->id)->count() }}</td>
                            <td>
                                @php
                                    $price = \App\OrderDetail::select(DB::raw('sum(price) as total_sales'))->where('payment_status','paid')->where('delivery_status','!=','cancelled')->where('seller_id', $seller->user_id)->first();
                                @endphp
                                @if(isset($price))
                                    {{ single_price($price->total_sales) }}
                                @endif

                            </td>
                            <td>
                                <div class="btn-group dropdown">
                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                        {{ __('Actions') }} <i class="dropdown-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" style="padding:0px;margin:0;list-style:none;position:relative;right:0;">
                                        <li><a onclick="show_seller_profile('{{$seller->id}}');">{{ __('Profile') }}</a></li>
                                        <li><a href="{{route('sellers.login', encrypt($seller->id))}}">{{ __('Log in as this Seller') }}</a></li>
                                        <li><a href="{{route('sellers.edit', encrypt($seller->id))}}">{{ __('Edit') }}</a></li>
                                        @if($seller->user->banned != 1)

                                            <li><a href="#" onclick="confirm_ban('{{route('sellers.ban', $seller->id)}}');">{{ __('Ban this seller') }}  <i class="fa fa-ban text-danger" aria-hidden="true"></i> </a></li>
                                        @else

                                            <li><a href="#" onclick="confirm_unban('{{route('sellers.ban', $seller->id)}}');">{{ __('Unban this seller') }} <i class="fa fa-check text-success" aria-hidden="true"></i></a></li>
                                        @endif
                                        @if(null !== $seller->user->company_id)
                                            <li><a href="{{route('sellers.oms_sync', $seller->user)}}">{{ __('OMS sync') }} <i class="fa fa-refresh text-success" aria-hidden="true"></i></a></li>
                                        @endif
                                            <li><a onclick="confirm_modal({{ json_encode(['item' => $seller->user->name, 'url' => route('sellers.destroy', $seller->id)]) }});">
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
                    {{ $sellers->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="profile_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">
            </div>
        </div>
    </div>
@endsection

@section('script')
    <style>
        .sortable {
            cursor: pointer;
            user-select: none;
        }
        .sortable:hover {
            background-color: #f5f5f5;
        }
        .sortable i {
            margin-left: 5px;
            opacity: 0.7;
        }
    </style>

    <script src="{{ static_asset('js/loading-overlay.min.js') }}"></script>

    <script type="text/javascript">
        // Disable DataTable auto-initialization for this specific table
        $(document).ready(function() {
            if ($.fn.DataTable) {
                $.fn.DataTable.ext.selector.table = function(selector) {
                    return $(selector).not('.sellers-table');
                };
            }
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            console.log('Document ready, jQuery version:', $.fn.jquery);
            console.log('Sortable elements found:', $('.sortable').length);
            console.log('Form found:', $('#sort_sellers').length);
            console.log('Hidden inputs found:', $('#sort_by').length, $('#sort_direction').length);

            $('.sortable').click(function(e) {
                e.preventDefault();
                console.log('Sortable clicked!');

                var sortBy = $(this).data('sort');
                var currentSortBy = $('#sort_by').val();
                var currentSortDirection = $('#sort_direction').val();

                console.log('Current sort by:', currentSortBy);
                console.log('Current sort direction:', currentSortDirection);
                console.log('New sort by:', sortBy);

                var newSortDirection = 'asc';
                if (sortBy === currentSortBy && currentSortDirection === 'asc') {
                    newSortDirection = 'desc';
                }

                console.log('New sort direction:', newSortDirection);

                $('#sort_by').val(sortBy);
                $('#sort_direction').val(newSortDirection);

                console.log('About to submit form...');
                console.log('Form action:', $('#sort_sellers').attr('action'));
                $('#sort_sellers').submit();
            });

            $('#search').keypress(function(e) {
                if (e.which === 13) {
                    $('#sort_sellers').submit();
                }
            });

            $('.sellers-table tbody').sortable(
                {
                    update: function( event, ui ) {
                        const listIndex = prepareListIndex();
                        saveListIndex(listIndex);
                    }
                }
            );

            function prepareListIndex() {
                let baseIndex = ( {{$sellers->currentPage()}} - 1 ) * {{$sellers->perPage()}};
                let listIndex = {};
                $('.sellers-table tbody tr').each(function( index ) {
                    const id = $(this).find('td:first-child .item-id').val();
                    const newIndex = (index + 1) + baseIndex;
                    listIndex[id] = newIndex;
                    $(this).find('td:first-child .item-index').text(newIndex);
                });
                return listIndex;
            }

            function saveListIndex(listIndex) {

                let data = {};
                data['listIndex'] = listIndex;
                data['_token'] = '{{ @csrf_token() }}';

                $.ajax({
                    url: "{{ route('admin.api.sellers.update_index') }}",
                    headers: {
                        "Authorization": "Bearer " + "{{ Session::get('laravel_token') }}",
                    },
                    method: 'POST',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        $.LoadingOverlay("show", {
                            size: 10,
                            image: "",
                            fontawesome: "fa fa-cog fa-spin"
                        });
                    },
                    complete: function () {
                        $.LoadingOverlay('hide');
                    },
                    success: function (response) {
                    },
                    error: function () {
                    }
                });
            }

        } );

        function show_seller_payment_modal(id){
            $.post('{{ route('sellers.payment_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#payment_modal #modal-content').html(data);
                $('#payment_modal').modal('show', {backdrop: 'static'});
                $('.demo-select2-placeholder').select2();
            });
        }

        function show_seller_profile(id){
            $.post('{{ route('sellers.profile_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#profile_modal #modal-content').html(data);
                $('#profile_modal').modal('show', {backdrop: 'static'});
            });
        }

        function update_approved(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }

            $.post('{{ route('sellers.approved') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    showAlert('success', 'Approved sellers updated successfully');
                }
                else{
                    showAlert('danger', 'Something went wrong');
                }
            });
        }

        function toggle_display(el){
            $.post('{{ route('admin.api.sellers.toggle_display') }}', {_token:'{{ csrf_token() }}', sellerId:el.value}, function(data){
                if(data.code == 200){
                    showAlert('success', '{{ __('Toggle Display Successfully') }}');
                }
                else{
                    showAlert('danger', '{{ __('Something went wrong') }}');
                }
            });
        }

        function sort_sellers(el){
            $('#sort_sellers').submit();
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
                    <p>{{ __('Do you really want to ban this seller?') }}</p>
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
                    <p>{{ __('Do you really want to unban this seller?') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <a id="confirmationunban" class="btn btn-success btn-ok">{{ __('Proceed!') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
