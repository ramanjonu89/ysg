@section('script')
    @parent

    <script type="text/javascript">
        $(document).ready(function(){
            $('#select_sellers option').each(function() {
            const selected = $("#select_sellers option:selected").length;
            if (selected == $("#select_sellers option").length - 1) {
                $("#select_sellers").find('option[value="0"]').prop("selected",true);
            }
        });

        $('#select_sellers').select2({closeOnSelect: false,}).on('select2:selecting', function(e) {
            const value_selected = e.params.args.data.id;
            if (value_selected == 0) {
                $("#select_sellers").find('option').prop("selected",true);
                $("#select2-select_sellers-results").find(".select2-results__option").attr("aria-selected",true);
                $("#select_sellers").trigger('change');
            } else {
                const value_old = (e.target.value == '') ? [value_selected] : $(e.target).val().concat([value_selected]);
                $(e.target).val(value_old).trigger('change');
                $(e.params.args.originalEvent.currentTarget).attr('aria-selected', true);
                return false;
            }
        }).on('select2:unselecting', function(e) {
            const value_unselected = e.params.args.data.id;
            if (value_unselected == 0) {
                $("#select_sellers").find('option').prop("selected",false);
                $("#select2-select_sellers-results").find(".select2-results__option").attr("aria-selected",false);
                $("#select_sellers").trigger('change');
            } else {
                const value_old = (e.target.value == '') ? [value_unselected] : $(e.target).val().concat([value_unselected]);
                $(e.target).val(value_old).trigger('change');
                $(e.params.args.originalEvent.currentTarget).attr('aria-selected', false);
                $("#select_sellers").find('option[value="0"]').prop("selected",false);
                $("#select_sellers").find('option[value="' + value_unselected + '"]').prop("selected",false);
                $("#select2-select_sellers-results").find(".select2-results__option:nth-child(1)").attr("aria-selected",false);
                $("#select_sellers").trigger('change');
                return false;
            }
        });
        })
    </script>
@endsection

@section('css')
    @parent
    <style>
        .select2-selection__rendered {
            max-height: 120px;
            overflow-y: auto !important;
        }
    </style>
@endsection