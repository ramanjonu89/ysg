@extends('layouts.app')
@section('content')
<div class="col-lg-6 col-lg-offset-3">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Subcategory Information') }}</h3>
        </div>
        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('subcategories.update', $subcategory->id) }}" method="POST" enctype="multipart/form-data">
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
                                    @include('subcategories.tabs.form')
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{ __('Category') }}</label>
                    <div class="col-sm-9">
                        <select name="category_id" required class="form-control demo-select2">
                            @foreach($categories as $category)
                                <option value="{{$category->id}}" <?php if($subcategory->category_id == $category->id) echo "selected";?> >{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{ __('Slug') }}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{ __('Slug') }}" id="slug" name="slug" value="{{ $subcategory->slug }}" class="form-control">
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
