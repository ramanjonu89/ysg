@extends('layouts.app')



@section('content')



<div class="row">

    <div class="col-sm-12">

        <a href="{{ route('subcategories.create') }}" class="btn btn-rounded btn-info pull-right">{{ __('Add New Subcategory') }}</a>

    </div>

</div>



<br>



<!-- Basic Data Tables -->

<!--===================================================-->

<div class="panel">

    <div class="panel-heading bord-btm clearfix pad-all h-100">

        <h3 class="panel-title pull-left pad-no">{{ __('Sub-Categories') }}</h3>

        <div class="pull-right clearfix">

            <form class="" id="sort_subcategories" action="" method="GET">

                <div class="box-inline pad-rgt pull-left">

                    <div class="" style="min-width: 200px;">

                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ __('Type name & Enter') }}">

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="panel-body">

        <table class="table table-striped res-table1 mar-no" cellspacing="0" width="100%">

            <thead>

                <tr>

                    <th>#</th>

                    <th>{{ __('Subcategory') }}</th>

                    <th>{{ __('Category') }}</th>

                    <th>{{ __('Published') }}</th>

                    <th width="10%">{{ __('Options') }}</th>

                </tr>

            </thead>

            <tbody>

                @foreach($subcategories as $key => $subcategory)
                    @if ($subcategory->viewableSubCategories())

                        <tr>

                            <td>{{ ($key+1) + ($subcategories->currentPage() - 1)*$subcategories->perPage() }}</td>

                            <td>{{ $subcategory->getTranslationNameAttribute() }}</td>

                            <td>{{$subcategory->category->translation_name}}</td>
                            
                            <td><label class="switch">
                                <input onchange="update_status(this)" value="{{ $subcategory->id }}" type="checkbox" @if ($subcategory->status === 1) checked @endif>
                                <span class="slider round"></span></label></td>
                            <td>

                                <div class="btn-group dropdown">

                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">

                                        {{ __('Actions') }} <i class="dropdown-caret"></i>

                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-right">

                                        <li><a href="{{route('subcategories.edit', encrypt($subcategory->id))}}">{{ __('Edit') }}</a></li>

                                        <!-- <li><a onclick="confirm_modal('{{route('subcategories.destroy', $subcategory->id)}}');">{{ __('Delete') }}</a></li> -->

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

                {{ $subcategories->appends(request()->input())->links() }}

            </div>

        </div>

    </div>

</div>



@endsection

@section('script')

    <script>
        function update_status(el){
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            
            $.post('{{ route('subcategories.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
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
