@section('script')
    @parent
    <script type="text/javascript">

        $(document).ready(function () {
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