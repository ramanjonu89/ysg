@extends('layouts.app')
@section('content')
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ ucfirst(str_replace('_', ' ', $policy->name)) }}</h3>
            </div>
            <!--Horizontal Form-->
            <!--===================================================-->
            <form class="form-horizontal" id="form-multiple-language"  action="{{ route('policies.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="panel-body">
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
                        <div class="tab-content clearfix">
                            @if (isset($systemLanguages) && count($systemLanguages) > 0)
                                @foreach ($systemLanguages as $key => $language)
                                    <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ $language->id }}">
                                        @if (isset($policy) && $policy->policyTranslation($language->id) !== null)
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="name">{{ __('Content') }}</label>
                                            <div class="col-sm-10">
                                                <textarea class="editor" name="content[]" {{ $key == 0 ? 'required' : '' }}>{{ $policy->policyTranslation($language->id)->content }}</textarea>
                                            </div>
                                        </div>
                                        @else
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="name">{{ __('Content') }}</label>
                                            <div class="col-sm-10">
                                                <textarea class="editor" name="content[]" {{ $key == 0 ? 'required' : '' }}>{{ $policy->content }}</textarea>
                                            </div>
                                        </div>
                                        @endif
                                        <input type="hidden" name="language_id[]" value="{{ $language->id }}">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <input type="hidden" name="name" value="{{ $policy->name }}">
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
