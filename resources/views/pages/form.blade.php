@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-8 col-lg-offset-2">
        <div class="panel">
            <div class="panel-heading bord-btm">
                <h3 class="panel-title">{{ !isset($page) ? __('Create Custom Page')  : __('Edit Page') }}</h3>
            </div>
            <!--Horizontal Form-->
            <!--===================================================-->
            <form class="form-horizontal" id="form-multiple-language"  action="{{ !isset($page) ? route('pages.store') : route('pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="panel-body">
                @if(isset($page)) <input type="hidden" name="_method" value="PATCH"> @endif

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

                                        @if (isset($page) && $page->pageTranslation($language->id) !== null)

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="title">{{ __('Title') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]" value="{{$page->pageTranslation($language->id)->title}}" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="content">{{ __('Content') }}</label>
                                            <div class="col-sm-9">
                                                <textarea class="editor" name="content[]" required>{!! $page->pageTranslation($language->id)->content !!}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="slug">{{ __('Meta Title') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Meta Title') }}" id="meta_title" value="{{$page->pageTranslation($language->id)->meta_title}}" name="meta_title[]" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="meta_description">{{ __('Meta Description') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Meta Description') }}" id="meta_description" value="{{$page->pageTranslation($language->id)->meta_description}}" name="meta_description[]" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="keywords">{{ __('Keywords') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Keywords') }}" id="keywords" name="keywords[]" value="{{$page->pageTranslation($language->id)->keywords}}" class="form-control">
                                            </div>
                                        </div>

                                        @else

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="title">{{ __('Title') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]" value="{{isset($page) ? $page->title : '' }}" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="content">{{ __('Content') }}</label>
                                            <div class="col-sm-9">
                                                <textarea class="editor" name="content[]" required> {!! isset($page) ? $page->content : '' !!}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="slug">{{ __('Meta Title') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Meta Title') }}" id="meta_title" value="{{isset($page) ? $page->meta_title : ''}}" name="meta_title[]" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="meta_description">{{ __('Meta Description') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Meta Description') }}" id="meta_description" value="{{isset($page) ? $page->meta_description : ''}}" name="meta_description[]" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="keywords">{{ __('Keywords') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Keywords') }}" id="keywords" value="{{isset($page) ? $page->keywords : ''}}" name="keywords[]" class="form-control">
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
                                <input type="text" placeholder="{{ __('your-slug') }}" id="slug" name="slug" class="form-control" value="{{isset($page) ? $page->slug : ''}}" required>
                                <small><code>http://domain.com/your-slug</code> Only a-z, numbers, hypen allowed</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="meta_image">{{ __('Meta Image') }} <small>(200x300)</small></label>
                            <div class="col-sm-9">
                                <input type="file" id="meta_image" name="meta_image" class="form-control">
                            </div>
                        </div>
                    </div>
                    {{-- end  fix --}}

                </div>
                <div class="panel-footer text-right">
                    <button class="btn btn-purple" id="btn-save" type="button">{{ !isset($page) ?  __('Add New') :  __('Update')  }}</button>
                </div>
            </form>
            <!--===================================================-->
            <!--End Horizontal Form-->
        </div>
    </div>
</div>
@endsection
