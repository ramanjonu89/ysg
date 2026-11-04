<form class="form-horizontal" id="form-custom-page" action="{{ route('admin.custom_page.page.update', $page->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="panel-heading">
        <h3 class="panel-title text-center">
            {{ __('Page content') }}
        </h3>
    </div>
    <div class="panel-body">

        <ul class="nav nav-pills mb-5" style="background-color: #d486e1;">
            @if (isset($systemLanguages) && count($systemLanguages) > 0)
                @foreach ($systemLanguages as $key => $language)
                    <li class="{{ $key == 0 ? 'active' : '' }}"><a href="#{{ $language->id }}" style="color:#fff" data-toggle="tab">{{ $language->name }}</a></li>
                @endforeach
            @endif
        </ul>


        <div class="tab-content clearfix" style="background-color: #fff">
            @if (isset($systemLanguages) && count($systemLanguages) > 0)
                @foreach ($systemLanguages as $key => $language)
                    <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ $language->id }}">

                        @if (isset($page) && $page->ysgPageTranslation($language->id) !== null)
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="title">{{ __('Title') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]" class="form-control" {{ $key == 0 ? 'required' : '' }} value="{{$page->ysgPageTranslation($language->id)->title}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="description">{{ __('Description') }}</label>
                            <div class="col-sm-9">
                                <textarea placeholder="{{ __('Description') }}" id="description" name="description[]" class="form-control">{{$page->ysgPageTranslation($language->id)->description}}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="meta_title">{{ __('Meta Title') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Meta Title') }}" id="meta_title" name="meta_title[]" class="form-control" value="{{$page->ysgPageTranslation($language->id)->meta_title}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="meta_description">{{ __('Meta Description') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Meta Description') }}" id="meta_description"  name="meta_description[]" class="form-control" value="{{$page->ysgPageTranslation($language->id)->meta_description}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="meta_keywords">{{ __('Meta Keywords') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Meta Keywords') }}" id="meta_keywords" name="meta_keywords[]" class="form-control" value="{{$page->ysgPageTranslation($language->id)->meta_keywords}}">
                            </div>
                        </div>

                        @else
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="title">{{ __('Title') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]" class="form-control" {{ $key == 0 ? 'required' : '' }} value="{{$page->title}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="description">{{ __('Description') }}</label>
                            <div class="col-sm-9">
                                <textarea placeholder="{{ __('Description') }}" id="description" name="description[]" class="form-control">{{$page->description}}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="meta_title">{{ __('Meta Title') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Meta Title') }}" id="meta_title" name="meta_title[]" class="form-control" value="{{$page->meta_title}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="meta_description">{{ __('Meta Description') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Meta Description') }}" id="meta_description"  name="meta_description[]" class="form-control" value="{{$page->meta_description}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="meta_keywords">{{ __('Meta Keywords') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Meta Keywords') }}" id="meta_keywords" name="meta_keywords[]" class="form-control" value="{{$page->meta_keywords}}">
                            </div>
                        </div>
                        @endif


                        <input type="hidden" name="language_id[]" value="{{ $language->id }}">
                    </div>
                @endforeach
            @endif
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="slug">{{ __('Slug') }}</label>
            <div class="col-sm-9">
                <input type="text" placeholder="{{ __('Slug') }}" id="slug" name="slug" class="form-control" required value="{{$page->slug}}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="active">{{ __('Active') }}</label>
            <div class="col-sm-9">
                <label class="switch">
                    <input type="checkbox" id="active" name="active" {{$page->is_enabled?'checked':''}}>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="need_login">{{ __('Log-in Required') }}</label>
            <div class="col-sm-9">
                <label class="switch">
                    <input type="checkbox" id="need_login" name="need_login" {{$page->need_login?'checked':''}}>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="exclusive_access">{{ __('Exclusive Access') }}</label>
            <div class="col-sm-9">
                <label class="switch">
                    <input type="checkbox" id="exclusive_access"
                           name="exclusive_access" {{$page->exclusive_access?'checked':''}}>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="parent_id">{{ __('Parent Page') }}</label>
            <div class="col-sm-9">
                <select name="parent_id" id="parent_id" class="form-control">
                    <option value=""></option>
                    @foreach($service->getIdsNotCurrent($page->id) as $value => $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="panel-footer text-right">
        <a href="{{ route('admin.custom_page.page.index') }}">
            <button type="button" class="btn btn-default">{{__('Cancel') }}</button>
        </a>
        <button class="btn btn-purple" id="btn-save" type="button">{{__('Save') }}</button>
    </div>

</form>
