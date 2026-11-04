@extends('layouts.app')
@section('content')
    <div class="col-lg-6 col-lg-offset-3">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ __('Category Information') }}</h3>
            </div>

            <form class="form-horizontal" id="form-multiple-language"
                action="{{ route('categories.store') }}"
                method="POST" enctype="multipart/form-data">
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
                                        @include('categories.tabs.form')
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        @if (isset($category))
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="name">{{ __('Slug') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="{{ __('Slug') }}" id="slug" name="slug"
                                        value="{{ $category->slug }}" class="form-control">
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="banner">{{ __('Banner') }}
                                <small>({{ __('200x300') }})</small></label>
                            <div class="col-sm-10">
                                <input type="file" id="banner" name="banner" class="form-control"
                                    {{ isset($category) && $category->banner == 'required' ? '' : '' }}>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="icon">{{ __('Icon') }}
                                <small>({{ __('32x32') }})</small></label>
                            <div class="col-sm-10">
                                <input type="file" id="icon" name="icon" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="text-left col-sm-12 control-label pt-0 strong-600" style="text-decoration: underline" for="name">{{ __('Age Restricted') }}</label>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label pt-0" for="name">{{ __('18 Years & Above') }}</label>
                            <div class="col-sm-10">
                                <label class="switch">
                                    <input type="checkbox" id="checkbox_18_old" name="is_age_restricted" value="18">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label pt-0" for="name">{{ __('21 Years & Above') }}</label>
                            <div class="col-sm-10">
                                <label class="switch">
                                    <input type="checkbox" id="checkbox_21_old" name="is_age_restricted" value="21">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
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

@section('script')
    <script type="text/javascript">
        $(document).ready(function(){
            $('#checkbox_18_old').change(function(){
                if(this.checked) {
                    $('#checkbox_21_old').prop('checked', false);
                }
            });

            $('#checkbox_21_old').change(function(){
                if(this.checked) {
                    $('#checkbox_18_old').prop('checked', false);
                }
            });
        });
    </script>
@endsection