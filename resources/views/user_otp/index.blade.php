@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-sm-12">
        </div>
    </div>
    <br>
    <!-- Basic Data Tables -->
    <!--===================================================-->
    <div class="panel">
        <div class="panel-heading bord-btm clearfix pad-all h-100">
            <h3 class="panel-title pull-left pad-no">{{ __('List Customers Awaiting Verification') }}</h3>
            <div class="pull-right clearfix">
                <form class="" id="sort_customers" action="" method="GET">
                    <div class="box-inline pad-rgt pull-left">
                        <div class="" style="min-width: 200px;">
                            <input type="text" class="form-control" id="search" name="keyword" @isset($keyword) value="{{ $keyword }}" @endisset placeholder="{{ __('Type email or name & Enter') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel-body">
            <table id="dataTable" class="table table-striped res-table mar-no" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email Address') }}</th>
                    <th>{{ __('User Type')}}</th>
                    <th>{{ __('Created on') }}</th>
                    <th width="10%">{{ __('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($usersAwaitingVerify as $key => $userAwaitingVerify)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $userAwaitingVerify->username }}</td>
                            <td>{{ $userAwaitingVerify->email }}</td>
                            <td>{{ $userAwaitingVerify->user_type }}</td>
                            <td>{{ $userAwaitingVerify->created_at }}</td>
                            <td>
                                <div class="btn-group dropdown">
                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                        {{ __('Actions') }} <i class="dropdown-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a onclick="confirm_add_customer(
                                                {{ json_encode(['item' => $userAwaitingVerify, 'url' => route('admin.user_otps.registration_by_admin', ['user_type' => $userAwaitingVerify->user_type, 'user_details' => $userAwaitingVerify->user_details, 'original_otp' => $userAwaitingVerify->original_otp])]) }})"
                                            >
                                                @if ($userAwaitingVerify->isSellers())
                                                    {{ __('Add Seller') }}
                                                @else
                                                    {{ __('Add Customer') }}
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a onclick="confirm_modal({{ json_encode(['item' => $userAwaitingVerify->username, 'url' => route('admin.user_otps.destroy', $userAwaitingVerify->id)]) }});">
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
            <div class="clearfix">
                <div class="pull-right">
                    {{ $usersAwaitingVerify->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable( {      
                "searching": false,
                "paging": true,
                "info": true,
                "lengthChange": true,
                "pageLength": 15,
                "bDestroy": true,
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        extend: 'excel',
                        text: 'Export to Excel'
                    }
                ]
            });
        });

        function confirm_add_customer(data)
        {
            $('#confirm-add-customer').modal('show', {backdrop: 'static'});
            const message = `Do you want to add  ${data.item.user_type} "${ data.item.username}" ?`;
            $('#confirm_message_add').html(message);
            $('#confirm_add_customer').attr('href', data.url);
        }
    </script>
@endsection
@section('modal')
<div class="modal fade" id="confirm-add-customer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">{{ __('Confirmation') }}</h4>
            </div>
            <div class="modal-body">
                <p id="confirm_message_add">

                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                <a id="confirm_add_customer" class="btn btn-danger btn-ok">{{ __('Add') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection

