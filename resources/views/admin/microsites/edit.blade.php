@extends('layouts.app')

@section('content')
    <form action="{{ route('admin.microsites.update', [$microsite->id]) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="panel tab-pane" id="microsite">
            <div class="row microsite-container">
                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">
                                {{ __('Microsite contents') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="meta_title">{{ __('Meta title') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Meta title') }}" id="meta_title"
                                           name="meta_title" class="form-control" value="{{$microsite->meta_title}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="meta_keywords">{{ __('Meta keywords') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Meta keywords') }}" id="meta_keywords"
                                           name="meta_keywords" class="form-control"
                                           value="{{$microsite->meta_keywords}}"/>
                                    <small>{{ __('Each keyword separated by comma, example: E-commerce, fashion') }}</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="meta_description">{{ __('Meta description') }}</label>
                                <div class="col-sm-9">
                                    <textarea placeholder="{{ __('Meta description') }}" id="meta_description"
                                              name="meta_description"
                                              class="form-control">{!! $microsite->meta_description !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="description">{{ __('Microsite description') }}</label>
                                <div class="col-sm-9">
                                    <textarea placeholder="{{ __('Microsite description') }}" id="description"
                                              name="description"
                                              class="form-control editor">{!! $microsite->description !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="terms_and_conditions">{{ __('Terms & Conditions') }}</label>
                                <div class="col-sm-9">
                                    <textarea placeholder="{{ __('Terms & Conditions') }}" id="terms_and_conditions"
                                              name="terms_and_conditions"
                                              class="form-control editor">{!! $microsite->terms_and_conditions !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="policy">{{ __('Privacy Policy') }}</label>
                                <div class="col-sm-9">
                                    <textarea placeholder="{{ __('Privacy Policy') }}" id="policy"
                                              name="policy"
                                              class="form-control editor">{!! $microsite->policy !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="return_policy">{{ __('Cancellation & Returns Policy') }}</label>
                                <div class="col-sm-9">
                                    <textarea placeholder="{{ __('Cancellation & Returns Policy') }}" id="return_policy"
                                              name="return_policy"
                                              class="form-control editor">{!! $microsite->return_policy !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="favicon">{{ __('Favicon') }} <small>(32x32)</small></label>
                                <div class="col-sm-9">
                                    <input type="file" id="favicon" name="favicon" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="color">{{ __('Brand color') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Brand color') }}" id="color"
                                           name="color" class="form-control color-picker" value="{{$microsite->color}}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="second_color">{{ __('Secondary color') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Secondary color') }}" id="second_color"
                                           name="second_color" class="form-control color-picker" value="{{$microsite->second_color}}"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="mail_driver">{{ __('Mail Driver') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Mail Driver') }}" id="mail_driver"
                                           name="mail_driver" class="form-control" value="{{$microsite->mail_driver}}"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="mail_host">{{ __('Mail Host') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Mail Host') }}" id="mail_host"
                                           name="mail_host" class="form-control" value="{{$microsite->mail_host}}"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="mail_port">{{ __('Mail Port') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Mail Port') }}" id="mail_port"
                                           name="mail_port" class="form-control" value="{{$microsite->mail_port}}"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="mail_username">{{ __('Mail Username') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Mail Username') }}" id="mail_username"
                                           name="mail_username" class="form-control" value="{{$microsite->mail_username}}"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="mail_password">{{ __('Mail Password') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Mail Password') }}" id="mail_password"
                                           name="mail_password" class="form-control" value="{{$microsite->mail_password}}"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="mail_encryption">{{ __('Mail encryption') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Mail encryption') }}" id="mail_encryption"
                                           name="mail_encryption" class="form-control" value="{{$microsite->mail_encryption}}"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="mail_from_name">{{ __('Mail From Name') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Mail From Name') }}" id="mail_from_name"
                                           name="mail_from_name" class="form-control" value="{{$microsite->mail_from_name}}"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                       for="mail_from_address">{{ __('Mail From Address') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('Mail From Address') }}" id="mail_from_address"
                                           name="mail_from_address" class="form-control" value="{{$microsite->mail_from_address}}"/>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <button class="btn btn-purple" type="submit">{{__('Save') }}</button>
                            <button class="btn btn-success" type="submit" name="redirect" value="list">{{__('Save and back to list') }}</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="panel panel-primary">
                        @include('admin.custom_page.page.blocks', ['page' => $microsite, 'title' => __('Microsite blocks'), 'pageType' => 'microsite'])
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
<script type="text/javascript">
    $('.color-picker').spectrum({
        type: "component",
        showPalette: false
    });
</script>
@endsection
