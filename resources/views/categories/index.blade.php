@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('admin.categories.export-all') }}" class="btn btn-success">{{ __('Export All to Excel') }}</a>
        <a href="{{ route('categories.create') }}" class="btn btn-rounded btn-info pull-right">{{ __('Add New Category') }}</a>
    </div>
</div>
<br>
<!-- Basic Data Tables -->
<!--===================================================-->
<div class="panel">
    <div class="panel-heading bord-btm clearfix pad-all h-100">
        <h3 class="panel-title pull-left pad-no">{{ __('Categories') }}</h3>
        <div class="pull-right clearfix">
            <form class="" id="sort_categories" action="" method="GET">
                <div class="box-inline pad-rgt pull-left">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ __('Type name & Enter') }}">
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
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Banner') }}</th>
                    <th>{{ __('Icon') }}</th>
                    <th>{{ __('Featured') }}</th>
                    <th>{{ __('Published') }}</th>
                    <th>{{ __('Commission') }}</th>
                    <th width="10%">{{ __('Options') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach($categories as $key => $category)
                    <tr>
                        <td>{{ ($key+1) + ($categories->currentPage() - 1)*$categories->perPage() }}</td>
                        <td>{{$category->name}}</td>
                        <td><img loading="lazy"  class="img-md" src="{{ my_asset($category->banner) }}" alt="{{ __('banner') }}"></td>
                        <td><img loading="lazy"  class="img-xs" src="{{ my_asset($category->icon) }}" alt="{{ __('icon') }}"></td>
                        <td><label class="switch">
                            <input onchange="update_featured(this)" value="{{ $category->id }}" type="checkbox" @if ($category->featured === 1) checked @endif >
                            <span class="slider round"></span></label></td>
                        <td><label class="switch">
                            <input onchange="update_status(this)" value="{{ $category->id }}" type="checkbox" @if ($category->status === 1) checked @endif>
                            <span class="slider round"></span></label></td>
                        <td>{{ $category->commision_rate }} %</td>

                        <td>
                            <div class="btn-group dropdown">
                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                    {{ __('Actions') }} <i class="dropdown-caret"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a href="{{route('categories.edit', encrypt($category->id))}}">{{ __('Edit') }}</a></li>
                                    <!-- <li><a onclick="confirm_modal('{{route('categories.destroy', $category->id)}}');">{{ __('Delete') }}</a></li> -->
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $categories->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
    <script type="text/javascript">
        function update_featured(el){
            if(el.checked){
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('categories.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                data = JSON.parse(data);
                if(data.status === 1) {
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message);
                }
            });
        }

        function update_status(el){
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }

            $.post('{{ route('categories.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                data = JSON.parse(data);
                if(data.status === 1) {
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message);
                }
            });
        }
    </script>
@endsection

