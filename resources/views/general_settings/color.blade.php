@extends('layouts.app')
@section('content')
    <div class="col-lg-6 col-lg-offset-3">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ __('Color Settings') }}</h3>
            </div>
            <!--Horizontal Form-->
            <!--===================================================-->
            <form class="form-horizontal" action="{{ route('generalsettings.color.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="panel-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"
                                   for="frontend_color">{{ __('Color') }}</label>
                            <div class="col-sm-8">
                                <input type="text" placeholder="#e62e04" id="frontend_color"
                                       name="frontend_color" class="form-control color-picker" value="{{ $generalSetting->frontend_color }}"/>
                            </div>
                        </div>

                        <div class="row">

                            @foreach($defaultColors as $defaultColor)
                                <div class="color-radio col-sm-3">
                                    <label>
                                        <input type="checkbox" class="color-control-input" value="{{$defaultColor}}">
                                        <span class="color-control-box" style="background:{{$defaultColor}};"></span>
                                    </label>
                                </div>
                            @endforeach


                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"
                                   for="frontend_color_hover">{{ __('Color Hover') }}</label>
                            <div class="col-sm-8">
                                <input type="text" placeholder="#e62e04" id="frontend_color_hover"
                                       name="frontend_color_hover" class="form-control color-picker" value="{{ $generalSetting->frontend_color_hover }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"
                                   for="frontend_color_hover">{{ __('Secondary color') }}</label>
                            <div class="col-sm-8">
                                <input type="text" placeholder="#4F136D" id="frontend_secondary_color"
                                       name="frontend_secondary_color" class="form-control color-picker" value="{{ $generalSetting->frontend_secondary_color }}"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-heading">
                    <h3 class="panel-title">{{ __('Menu Bar Color Settings') }}</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"
                                   for="color-menu-bar-area">{{ __('Area Back Ground') }}</label>
                            <div class="col-sm-7">
                                <input type="text" placeholder="#e62e04" id="color-menu-bar-area"
                                       name="color_menu_bar_area" class="form-control color-picker" value="{{ $generalSetting->color_menu_bar_area }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"
                                   for="color-menu-bar-main">{{ __('Main Back Ground') }}</label>
                            <div class="col-sm-7">
                                <input type="text" placeholder="#e62e04" id="color-menu-bar-main"
                                       name="color_menu_bar_main" class="form-control color-picker" value="{{ $generalSetting->color_menu_bar_main }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"
                                   for="color-menu-bar-text">{{ __('Text') }}</label>
                            <div class="col-sm-7">
                                <input type="text" placeholder="#e62e04" id="color-menu-bar-text"
                                       name="color_menu_bar_text" class="form-control color-picker" value="{{ $generalSetting->color_menu_bar_text }}"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-footer text-right">
                    <button class="btn btn-purple" type="submit">{{ __('save') }}</button>
                </div>
            </form>
            <!--===================================================-->
            <!--End Horizontal Form-->
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
      $('.color-picker').spectrum({
        type: "component",
        showPalette: false
      });

      let timeoutObject;
      let selectColors = $('.color-control-input');
      $('.color-control-input').click(function () {
        selectColors.prop('checked', false);
        $(this).prop('checked', true);
        $('input.color-picker').val($(this).val());

        clearTimeout(timeoutObject);
        timeoutObject = setTimeout(function(){
          selectColors.prop('checked', false);
        }, 500);
      });

    </script>
@endsection
