@extends('layouts.app')
@section('content')
    <div class="col-lg-6 col-lg-offset-3">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{__('General Settings') }}</h3>
            </div>
            <!--Horizontal Form-->
            <!--===================================================-->
            <form class="form-horizontal" id="form-multiple-language"  action="{{ route('generalsettings.update',$generalsetting->id ) }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <input type="hidden" name="_method" value="PATCH">
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

                                        @if (isset($generalsetting) && $generalsetting->generalSettingTranslation($language->id) !== null)
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="name">{{__('Site Name') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="name" name="name[]" value="{{ $generalsetting->generalSettingTranslation($language->id)->site_name }}" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="name">{{__('Footer Text') }}</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" rows="4" name="description[]" required>{{$generalsetting->generalSettingTranslation($language->id)->description}}</textarea>
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            <label class="col-sm-3 control-label" for="name">{{__('Annoucement Text') }}</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" id="annoucement" name="annoucement[]" value="{{$generalsetting->generalSettingTranslation($language->id)->annoucement}}"></input>
                                            </div>
                                        </div>

                                        @else

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="name">{{__('Site Name') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="name" name="name[]" value="{{ $generalsetting->site_name }}" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="name">{{__('Footer Text') }}</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" rows="4" name="description[]" required>{{$generalsetting->description}}</textarea>
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            <label class="col-sm-3 control-label" for="name">{{__('Annoucement Text') }}</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" id="annoucement" name="annoucement[]" value="{{$generalsetting->annoucement}}"></input>
                                            </div>
                                        </div>
                                        @endif

                                        <input type="hidden" name="language_id[]" value="{{ $language->id }}">
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="address">{{__('Address') }}</label>
                            <div class="col-sm-9">
                                <input type="text" id="address" name="address" value="{{ $generalsetting->address }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="phone">{{__('Phone') }}</label>
                            <div class="col-sm-9">
                                <input type="text" id="phone" name="phone" value="{{ $generalsetting->phone }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="name">{{__('Annoucement Text')}}</label>
                            <div class="col-sm-9">
                                <input class="form-control" id="annoucement" name="annoucement" value="{{$generalsetting->annoucement}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="name">{{__('Annoucement Text')}}</label>
                            <div class="col-sm-9">
                                <input class="form-control" id="annoucement" name="annoucement" value="{{$generalsetting->annoucement}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="email">{{__('Email') }}</label>
                            <div class="col-sm-9">
                                <input type="text" id="email" name="email" value="{{ $generalsetting->email }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="facebook">{{__('Facebook') }}</label>
                            <div class="col-sm-9">
                                <input type="text" id="facebook" name="facebook" value="{{ $generalsetting->facebook }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="instagram">{{__('Instagram') }}</label>
                            <div class="col-sm-9">
                                <input type="text" id="instagram" name="instagram" value="{{ $generalsetting->instagram }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="twitter">{{__('Twitter') }}</label>
                            <div class="col-sm-9">
                                <input type="text" id="twitter" name="twitter" value="{{ $generalsetting->twitter }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="youtube">{{__('Youtube') }}</label>
                            <div class="col-sm-9">
                                <input type="text" id="youtube" name="youtube" value="{{ $generalsetting->youtube }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="google_plus">{{__('Google Plus') }}</label>
                            <div class="col-sm-9">
                                <input type="text" id="google_plus" name="google_plus" value="{{ $generalsetting->google_plus }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="telegram">{{__('Telegram')}}</label>
                            <div class="col-sm-9">
                                <input type="text" id="telegram" name="telegram" value="{{ $generalsetting->telegram }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" style="display:none">
                            <label class="col-sm-3 control-label">{{__('System Timezone') }}</label>
                            <div class="col-sm-9">
                                <select name="timezone" class="form-control demo-select2" data-live-search="true">
                                    @foreach (timezones() as $key => $value)
                                        <option value="{{ $value }}" @if (app_timezone() == $value)
                                            selected
                                        @endif>{{ $key }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="show_css_banner">{{__('Enable CSS banner on Menu bar?') }}</label>
                            <div class="col-sm-9">
                                <label class="switch">
                                    <input type="checkbox" id="show_css_banner" name="show_css_banner" {{$generalsetting->show_css_banner?'checked':''}}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @component('admin.components.input_photo', ['image' => asset($generalsetting->css_banner), 'label' => __('CSS banner'), 'fieldName' => 'css_banner'])
                        @endcomponent
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="css_url">{{__('CSS Page URL') }}</label>
                            <div class="col-sm-9">
                                <input type="text" id="css_url" name="css_url" value="{{ $generalsetting->css_url }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="seller_center_master_password">{{__('Seller Center Master Password')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="seller_center_master_password" name="seller_center_master_password" value="{{ $sellerCenterMasterPassword }}" class="form-control">
                        </div>
                    </div>

                    <div class="panel-footer text-right">
                        <button class="btn btn-purple" id="btn-save" type="button">{{__('Save')}}</button>
                    </div>
                </div>
            </form>
            <!--===================================================-->
            <!--End Horizontal Form-->
        </div>
    </div>
@endsection
