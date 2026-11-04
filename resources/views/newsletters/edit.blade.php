@php
    $start_date = !empty($newsletter->start_date) ? date('d/m/Y H:i', $newsletter->start_date) : 0;
    $end_date   = !empty($newsletter->end_date) ? date('d/m/Y H:i', $newsletter->end_date) : 0;
@endphp
@extends('layouts.app')
@section('content')
    <div class="panel">
        <div class="panel-heading bord-btm clearfix pad-all h-100">
            <h3 class="panel-title pull-left pad-no">{{ __('Newsletter Configuration') }}</h3>
        </div>
        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('newsletters.update', $newsletter->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="active">{{ __('Active/ Inactive') }}</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            <input type="checkbox" id="status" name="status" {{ $newsletter->status ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                @include('admin.custom_page.block.inc.datetimer')
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="description">{{ __('Description') }}</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="description" id="description"
                            value="{{ $newsletter->description }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="url_link">{{ __('Redirect Link') }}</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="link" id="link"
                            value="{{ $newsletter->link }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">{{ __('Promotion Banner Images') }}</label>
                    <div class="col-sm-9">
                        @if ($newsletter->photo != null)
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="img-upload-preview">
                                    <img loading="lazy"  src="{{ my_asset($newsletter->photo) }}" alt="" class="img-responsive">
                                    <input type="hidden" name="previous_photo" value="{{ $newsletter->photo }}">
                                    <button type="button" class="btn btn-danger close-btn remove-files"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        @endif
                        <div id="photo">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="active">{{ __('Subscribers Email box') }}</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            <input type="checkbox" id="status" name="subbox" value="1" {{ $newsletter->subbox ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-right">
                <a href="{{ route('newsletters.index') }}">
                    <button type="button" class="btn btn-default">{{ __('Cancel') }}</button>
                </a>
                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
            </div>
        </form>
        <!--===================================================-->
        <!--End Horizontal Form-->
    </div>
    <style>
        .d-none {
            display: none !important;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.demo-select2').select2();
        });

        $("#photo").spartanMultiImagePicker({
            fieldName:        'photo',
            maxCount:         1,
            rowHeight:        '200px',
            groupClassName:   'col-md-4 col-sm-9 col-xs-6',
            maxFileSize:      '',
            dropFileLabel : "Drop Here",
            onExtensionErr : function(index, file){
                console.log(index, file,  'extension err');
                alert('Please only input png or jpg type file')
            },
            onSizeErr : function(index, file){
                console.log(index, file,  'file size too big');
                alert('File size too big');
            }
        });


        $(document).ready(function () {
            $('.demo-select2').select2();
            $('input[name="daterange"]').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                locale: {
                    format: 'DD/MM/YYYY HH:mm:ss'
                }
            });

            $('input[name="start_date"]').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                singleDatePicker: true,
                locale: {
                    format: 'DD/MM/YYYY HH:mm:ss'
                }
            });

            $('input[name="end_date"]').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                singleDatePicker: true,
                locale: {
                    format: 'DD/MM/YYYY HH:mm:ss'
                }
            });

            const date_timer_type = $('#date_timer_type').val();
            enable_date_ranger(date_timer_type);
        });

        function enable_date_ranger(date_timer_type) {
            if (date_timer_type == 'start_date_only') {
                $('.start-date').removeClass('d-none');
                $('.date-range').addClass('d-none');
                $('.end-date').addClass('d-none');
            } else if(date_timer_type == 'end_date_only') {
                $('.start-date').addClass('d-none');
                $('.date-range').addClass('d-none');
                $('.end-date').removeClass('d-none');
            } else {
                $('.start-date').addClass('d-none');
                $('.date-range').removeClass('d-none');
                $('.end-date').addClass('d-none');
            }
        }

        $('#date_timer_type').on('change', function() {
            enable_date_ranger(this.value);
        });

    </script>
@endsection
