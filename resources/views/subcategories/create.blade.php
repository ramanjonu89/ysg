@extends('layouts.app')
@section('content')
<div class="col-lg-6 col-lg-offset-3">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Subcategory Information') }}</h3>
        </div>

        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" id="form-multiple-language" action="{{ route('subcategories.store') }}" method="POST" enctype="multipart/form-data">
        	@csrf
            <div class="panel-body">
                <div id="tab-form">
                    <ul class="nav nav-pills mb-5" style="background-color: #d486e1;">
                        @if (isset($systemLanguages) && count($systemLanguages) > 0)
                            @foreach ($systemLanguages as $key => $language)
                                <li class="{{ $key == 0 ? 'active' : '' }}"><a href="#{{ $language->id }}"
                                        style="color:#fff" data-toggle="tab">{{ $language->name }}</a></li>
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
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="name">{{ __('Category') }}</label>
                        <div class="col-sm-9">
                            <select name="category_id" required class="form-control demo-select2-placeholder">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{isset($subcategory) && $subcategory->category_id ==  $category->id ? 'selected' :  ''}}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if(isset($subcategory))
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="name">{{ __('Slug') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ __('Slug') }}" id="slug" name="slug" value="{{ $subcategory->slug }}" class="form-control">
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="panel-footer text-right">
                <button class="btn btn-purple" id="btn-save" type="button">{{ __('Save') }}</button>
            </div>
        </form>
        <!--===================================================-->
        <!--End Horizontal Form-->
    </div>
</div>
@endsection

