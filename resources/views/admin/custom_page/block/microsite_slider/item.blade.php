
@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Content') }}</h3>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" id="form-multiple-language" action="{{ $formAction }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="panel-body">
                    <input type="hidden" name="block_id" value="{{$block->id}}">
                    <div id="tab-form">
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10">
                                <ul class="nav nav-pills mb-5" style="background-color: #d486e1;">
                                    @if (isset($systemLanguages) && count($systemLanguages) > 0)
                                        @foreach ($systemLanguages as $key => $language)
                                            <li class="{{ $key == 0 ? 'active' : '' }}"><a href="#{{ $language->id }}" style="color:#fff" data-toggle="tab">{{ $language->name }}</a></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>

                        @include('admin.custom_page.block.inc.published',  ['block' => $blockContent])

                        <div class="tab-content clearfix" style="background-color: #fff">
                            @if (isset($systemLanguages) && count($systemLanguages) > 0)
                                @foreach ($systemLanguages as $key => $language)
                                    <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ $language->id }}">
                                        @if (isset($blockContent) && $blockContent->ysgBlockContentTranslation($language->id) !== null)
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="title">{{ __('Title') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]" class="form-control" {{ $key == 0 ? 'required' : '' }} value="{{$blockContent->ysgBlockContentTranslation($language->id)->title??''}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="title">{{ __('Description') }}</label>
                                            <div class="col-sm-9">
                                                <textarea placeholder="{{ __('Description') }}" id="description" name="description[]" class="form-control">{{$blockContent->ysgBlockContentTranslation($language->id)->description??''}}</textarea>
                                            </div>
                                        </div>
                                        @else
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="title">{{ __('Title') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]" class="form-control" {{ $key == 0 ? 'required' : '' }} value="{{$blockContent->title??''}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="title">{{ __('Description') }}</label>
                                            <div class="col-sm-9">
                                                <textarea placeholder="{{ __('Description') }}" id="description" name="description[]" class="form-control">{{$blockContent->description??''}}</textarea>
                                            </div>
                                        </div>
                                        @endif
                                        <input type="hidden" name="language_id[]" value="{{ $language->id }}">
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        @component('admin.components.input_photo', ['image' => $blockContent->image, 'label' => __('Image'), 'fieldName' => 'image']) @endcomponent
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="link">{{ __('Link') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Link') }}" id="link" name="options[link]"
                                    class="form-control" value="{{$blockContent->options->link??''}}">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="{{ route('admin.custom_page.block.edit', ['block' => $block]) }}">
                        <button type="button" class="btn btn-default">{{ __('Cancel') }}</button>
                    </a>
                    <button class="btn btn-purple" id="btn-save" type="button">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
