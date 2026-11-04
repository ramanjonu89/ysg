@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('review_categories.create') }}" class="btn btn-rounded btn-info pull-right">{{ __('Add New Review Category') }}</a>
    </div>
</div>

<br>

<div class="panel">
    <div class="panel-heading bord-btm clearfix pad-all h-100">
        <h3 class="panel-title pull-left pad-no">{{ __('Review Categories') }}</h3>
        <div class="pull-right clearfix">
            <form class="" id="sort_review_categories" action="" method="GET">
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
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Enabled') }}</th>
                    <th width="10%">{{ __('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($review_categories as $key => $review_category)
                    <tr>
                        <td>{{ ($key+1) + ($review_categories->currentPage() - 1)*$review_categories->perPage() }}</td>
                        <td>{{$review_category->title}}</td>
                        <td>
                            <label class="switch">
                                <input onchange="update_status(this)" value="{{ $review_category->id }}" type="checkbox" @if ($review_category->enabled) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <div class="btn-group dropdown">
                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                    {{ __('Actions') }} <i class="dropdown-caret"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a href="{{route('review_categories.edit', encrypt($review_category->id))}}">{{ __('Edit') }}</a></li>
                                    <li><a onclick="confirm_modal('{{route('review_categories.destroy', $review_category->id)}}');">{{ __('Delete') }}</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="clearfix">
            <div class="pull-right">
                {{ $review_categories->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        function update_status(el){
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }

            $.post('{{ route('review_categories.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                data = JSON.parse(data);
                if (data.status === 1) {
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message);
                }
            });
        }
    </script>
@endsection

