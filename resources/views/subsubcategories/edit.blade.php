@extends('layouts.app')
@section('content')
<div class="col-lg-6 col-lg-offset-3">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Sub Subcategory Information') }}</h3>
        </div>
        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('subsubcategories.update', $subSubCategory->id) }}" method="POST" enctype="multipart/form-data">
            <input name="_method" type="hidden" value="PATCH">
            @csrf
            <div class="panel-body">
                <div id="tab-form">
                    <ul class="nav nav-pills mb-5 tab-language">
                        @if (isset($systemLanguages) && count($systemLanguages) > 0)
                            @foreach ($systemLanguages as $key => $language)
                                <li class="{{ $key == 0 ? 'active' : '' }}"><a href="#{{ $language->id }}"
                                    data-toggle="tab">{{ $language->name }}</a></li>
                            @endforeach
                        @endif
                    </ul>

                    <div class="tab-content clearfix">
                        @if (isset($systemLanguages) && count($systemLanguages) > 0)
                            @foreach ($systemLanguages as $key => $language)
                                <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ $language->id }}">
                                    @include('subsubcategories.tabs.form')
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{ __('Category') }}</label>
                    <div class="col-sm-9">
                        <select name="category_id" id="category_id" class="form-control sub-demo-select2" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{ __('Subcategory') }}</label>
                    <div class="col-sm-9">
                        <select name="sub_category_id" id="sub_category_id" class="form-control demo-select2" required>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{ __('Slug') }}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{ __('Slug') }}" id="slug" name="slug" value="{{ $subSubCategory->slug }}" class="form-control">
                    </div>
                </div>
            </div>
            <div class="panel-footer text-right">
                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
            </div>
        </form>
        <!--===================================================-->
        <!--End Horizontal Form-->
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    function get_subcategories_by_category(){
		var category_id = $('#category_id').val();
		$.post('{{ route('subcategories.get_subcategories_by_category') }}',{_token:'{{ csrf_token() }}', category_id:category_id}, function(data){
		    $('#sub_category_id').html(null);
            var extra = "";
		    for (var i = 0; i < data.length; i++) {
                data[i].id == '{{ $subSubCategory->sub_category_id }}' ? extra = "selected " : extra = "";
                $('#sub_category_id').append($(`<option value="${data[i].id}" ${extra}>${data[i].name}</option>`));
		    }
		    $('.sub-demo-select2').select2();
		});
	}
    $('.demo-select2').select2();
    $(document).ready(function(){
        $("#category_id > option").each(function() {
            if(this.value == '{{$subSubCategory->subcategory->category_id}}'){
                $("#category_id").val(this.value).change();
            }
        });
        get_subcategories_by_category();
    });
    $('#category_id').on('change', function() {
        get_subcategories_by_category();
    });
</script>
@endsection
