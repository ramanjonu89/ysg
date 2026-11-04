@extends('layouts.app')



@section('content')



<div class="row">

    <div class="col-sm-12">

        <a href="{{ route('subsubcategories.create') }}" class="btn btn-rounded btn-info pull-right">{{ __('Add New Sub Subcategory') }}</a>

    </div>

</div>



<br>



<!-- Basic Data Tables -->

<!--===================================================-->

<div class="panel">

    <div class="panel-heading bord-btm clearfix pad-all h-100">

        <h3 class="panel-title pull-left pad-no">{{ __('Sub-Sub-categories') }}</h3>

        <div class="pull-right clearfix">

            <form class="" id="sort_subsubcategories" action="" method="GET">

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

                    <th>{{ __('Sub Subcategory') }}</th>

                    <th>{{ __('Subcategory') }}</th>

                    <th>{{ __('Category') }}</th>

                    <th>{{ __('Published') }}</th>

                    {{-- <th>{{ __('Attributes') }}</th> --}}

                    <th width="10%">{{ __('Options') }}</th>

                </tr>

            </thead>

            <tbody>

                @foreach($subSubCategories as $key => $subsubcategory)
                    @if ($subsubcategory->viewableSubSubCategories()) 
                        <tr>

                            <td>{{ ($key+1)}}</td>

                            <td>{{__($subsubcategory->name)}}</td>

                            <td>{{$subsubcategory->subcategory->name}}</td>

                            <td>{{$subsubcategory->subcategory->category->name}}</td>

                            <td><label class="switch">
                                <input onchange="update_status(this)" value="{{ $subsubcategory->id }}" type="checkbox" @if ($subsubcategory->status === 1) checked @endif>
                                <span class="slider round"></span></label></td>
                            <td>

                                <div class="btn-group dropdown">

                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">

                                        {{ __('Actions') }} <i class="dropdown-caret"></i>

                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-right">

                                        <li><a href="{{route('subsubcategories.edit', encrypt($subsubcategory->id))}}">{{ __('Edit') }}</a></li>

                                        <!-- <li><a onclick="confirm_modal('{{route('subsubcategories.destroy', $subsubcategory->id)}}');">{{ __('Delete') }}</a></li> -->

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

                {{ $subSubCategories->appends(request()->input())->links() }}

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
            
            $.post('{{ route('subsubcategories.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
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

