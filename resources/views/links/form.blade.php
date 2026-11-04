@extends('layouts.app')
@section('content')
    <div class="col-lg-6 col-lg-offset-3">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ __('Useful Link') }}</h3>
            </div>
            <!--Horizontal Form-->
            <!--===================================================-->
            <form class="form-horizontal" id="form-multiple-language"  action="{{ !isset($link) ? route('links.store') : route('links.update',$link->id)}}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="panel-body">

                 @if(isset($link)) <input type="hidden" name="_method" value="PATCH"> @endif
                    {{-- fix --}}
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

                                        @if (isset($link) && $link->linkTranslation($language->id) !== null)
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="name">{{ __('Name') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Name') }}" id="name" name="name[]" value="{{ $link->linkTranslation($language->id)->name }}" class="form-control" required>
                                            </div>
                                        </div>
                                        @else
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="name">{{ __('Name') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Name') }}" id="name" name="name[]" value="{{ isset($link) ? $link->name : ''}} " class="form-control" required>
                                            </div>
                                        </div>
                                        @endif

                                    </div>
                                    <input type="hidden" name="language_id[]" value="{{ $language->id }}">
                                @endforeach
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="link">{{ __('Link') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Link') }}" id="link" name="link" value="{{isset($link) ? $link->url : ''}}" class="form-control" required>
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
