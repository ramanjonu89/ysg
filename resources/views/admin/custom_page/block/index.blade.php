@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="#" onclick="showCreateModal();" class="btn btn-rounded btn-info pull-right">{{ __('Add New Block') }}</a>
        </div>
    </div>
    <br>
    <!-- Basic Data Tables -->
    <!--===================================================-->
    <div class="panel">
        <div class="panel-heading bord-btm clearfix pad-all h-100">
            <h3 class="panel-title pull-left pad-no">{{ __('Blocks') }}</h3>
            <div class="pull-right clearfix">
                <form class="" action="" method="GET">
                    <div class="box-inline pad-rgt pull-left">
                        <div class="" style="min-width: 200px;">
                            <input type="text" class="form-control" id="search" name="keyword" @isset($keyword) value="{{ $keyword }}" @endisset 
                                   placeholder="{{ __('Type title & Enter') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped res-table mar-no" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Active') }}</th>
                    <th width="10%">{{ __('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($blockList as $key => $item)
                    <tr>
                        <td>
                            <span class="item-index">{{ ($key+1) + ($blockList->currentPage() - 1)*$blockList->perPage() }}</span>
                            <input class="item-id" type="hidden" value="{{ $item->id }}"/>
                        </td>
                        <td>{{$item->title}}</td>
                        <td>{{$item->type}}</td>
                        <td>{{($item->is_enabled)?'Yes':'No'}}</td>
                        <td>
                            <div class="btn-group dropdown">
                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                    {{ __('Actions') }} <i class="dropdown-caret"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" style="opacity: 1;">
                                    <li><a href="{{route('admin.custom_page.block.edit', $item->id)}}">{{ __('Edit') }}</a></li>
                                    <li>
                                        <a onclick="confirm_modal({{ json_encode(['item' => $item->title, 'url' => route('admin.custom_page.block.destroy', $item->id)]) }});">
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
                    {{ $blockList->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection

@include('admin.inc.modal_create_block')

@section('script')
    <script src="{{ static_asset('js/loading-overlay.min.js') }}"></script>
    <script  type="text/javascript">
        $(document).ready(function() {
            $('.table').DataTable( {      
                "searching": false,
                "paging": false,
                "info": true,
                "lengthChange":false,
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
    </script>
@endsection

