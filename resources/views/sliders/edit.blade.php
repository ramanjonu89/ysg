@php
    $start_date = !empty($slider->start_date) ? date('d/m/Y H:i', $slider->start_date) : 0;
    $end_date   = !empty($slider->end_date) ? date('d/m/Y H:i', $slider->end_date) : 0;
@endphp

<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">{{ __('sliders Information') }}</h3>
    </div>
    <!--Horizontal Form-->
    <!--===================================================-->
    <form class="form-horizontal" action="{{ route('sliders.update', $slider->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" value="PATCH">
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-3" for="published">{{ __('Active/ Inactive') }}</label>
                <div class="col-lg-9">
                    <div style="padding: 6px 0;">
                        <label class="switch" style="margin-bottom: 0">
                            <input value="1" name="published" type="checkbox" {{ isset($slider) && $slider->published ? 'checked' : ''}}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3" for="daterange">{{ __('Date/Time') }}</label>
                <div class="col-sm-2">
                    <select class="demo-select2" id="date_timer_type" name="date_timer_type">
                        <option value="date_range" @if($daterange) selected @endif>Date Range</option>
                        <option value="start_date_only" @if($start_date && !$end_date) selected @endif>Start Date Only</option>
                        <option value="end_date_only" @if($end_date && !$start_date) selected @endif>End Date Only</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <div class="date-range">
                        <input type="text" name="daterange" @if($daterange) value="{{ $daterange }}" @endif class="form-control"/>
                    </div>
                    <div class="start-date d-none">
                        <input type="text" name="start_date" @if($start_date) value="{{ $start_date }}" @endif class="form-control"/>
                    </div>
                    <div class="end-date d-none">
                        <input type="text" name="end_date" @if($end_date) value="{{ $end_date }}" @endif class="form-control"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3" for="url">{{ __('URL') }}</label>
                <div class="col-sm-9">
                    <input type="text" placeholder="{{ __('URL') }}" id="url" name="url" value="{{ $slider->link }}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3">
                    <label class="control-label">{{ __('slider Images') }}</label>
                    <strong>({{ __('1270px*400px') }})</strong>
                </div>
                <div class="col-sm-9">
                    @if ($slider->photo != null)
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="img-upload-preview">
                                <img loading="lazy"  src="{{ my_asset($slider->photo) }}" alt="" class="img-responsive">
                                <input type="hidden" name="previous_photo" value="{{ $slider->photo }}">
                                <button type="button" class="btn btn-danger close-btn remove-files"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    @endif
                    <div id="photo">

                    </div>
                </div>
            </div>
        </div>

        <div class="panel-footer text-right">
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
    $(document).ready(function(){
        $('.demo-select2').select2();
        $('.remove-files').on('click', function(){
            $(this).parents(".col-md-4").remove();
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

