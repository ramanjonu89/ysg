@extends('layouts.app')
@section('content')
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ __('Flash Deal Information') }}</h3>

            </div>
            <!--Horizontal Form-->
            <!--===================================================-->
            <form class="form-horizontal" action="{{ route('flash_deals.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="name">{{ __('Title') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ __('Title') }}" id="name" name="title" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="background_color">{{ __('Background Color') }} <small>(Hexa-code)</small></label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ __('#FFFFFF') }}" id="background_color" name="background_color" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="name">{{ __('Text Color') }}</label>
                        <div class="col-lg-9">
                            <select name="text_color" id="text_color" class="form-control demo-select2" required>
                                <option value="">{{ __('Select One') }}</option>
                                <option value="white">{{ __('White') }}</option>
                                <option value="dark">{{ __('Dark') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="banner">{{ __('Banner') }} <small>(1920x500)</small></label>
                        <div class="col-sm-9">
                            <input type="file" id="banner" name="banner" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="start_date">{{__('Date Range')}}</label>
                        <div class="col-sm-3">
                            <input type="text" name="daterange" value="" class="form-control"/>
                        </div>
                    </div>

                </div>
                <div class="panel-footer text-right">
                    <a href="{{ route('flash_deals.index') }}">
                        <button class="btn btn-success" type="button">{{__('Cancel')}}</button>
                    </a>
                    <button class="btn btn-purple" type="submit">{{__('Continue')}}</button>
                </div>
            </form>
            <!--===================================================-->
            <!--End Horizontal Form-->
        </div>
    </div>

    <style>
        .daterangepicker {
            z-index: 10000;
        }
    </style>

@endsection
@section('script')
    <script type="text/javascript">
      $(document).ready(function(){

        $('input[name="daterange"]').daterangepicker({
          timePicker: true,
          timePicker24Hour: true,
          locale: {
            format: 'DD/MM/YYYY HH:mm'
          }
        });
      });
    </script>
@endsection
