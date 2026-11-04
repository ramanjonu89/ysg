<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">{{ __('Slider Information') }}</h3>
    </div>
    <!--Horizontal Form-->
    <!--===================================================-->
    <form class="form-horizontal" action="{{ route('sliders.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-3" for="published">{{ __('Active/ Inactive') }}</label>
                <div class="col-lg-9">
                    <div style="padding: 6px 0;">
                        <label class="switch" style="margin-bottom: 0">
                            <input value="1" name="published" type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3" for="daterange">{{ __('Date/Time') }}</label>
                <div class="col-sm-2">
                    <select class="demo-select2" id="date_timer_type" name="date_timer_type">
                        <option value="date_range">Date Range</option>
                        <option value="start_date_only">Start Date Only</option>
                        <option value="end_date_only">End Date Only</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <div class="date-range">
                        <input type="text" name="daterange" value="" class="form-control"/>
                    </div>
                    <div class="start-date d-none">
                        <input type="text" name="start_date" value="" class="form-control"/>
                    </div>
                    <div class="end-date d-none">
                        <input type="text" name="end_date" value="" class="form-control"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3" for="url">{{ __('URL') }}</label>
                <div class="col-sm-9">
                    <input type="text" id="url" name="url" placeholder="http://example.com/" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3">
                    <label class="control-label">{{ __('Slider Images') }}</label>
                    <strong>(1270*400px)</strong>
                </div>
                <div class="col-sm-9">
                    <div id="photos">

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
        $("#photos").spartanMultiImagePicker({
            fieldName:        'photos[]',
            maxCount:         10,
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
    });

    $('#date_timer_type').on('change', function() {
        if (this.value == 'start_date_only') {
            $('.start-date').removeClass('d-none');
            $('.date-range').addClass('d-none');
            $('.end-date').addClass('d-none');
        } else if(this.value == 'end_date_only') {
            $('.start-date').addClass('d-none');
            $('.date-range').addClass('d-none');
            $('.end-date').removeClass('d-none');
        } else {
            $('.start-date').addClass('d-none');
            $('.date-range').removeClass('d-none');
            $('.end-date').addClass('d-none');
        }
    });

</script>

