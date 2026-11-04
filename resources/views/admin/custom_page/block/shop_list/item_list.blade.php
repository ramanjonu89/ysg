<div id="tab-2" class="tab-pane fade">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Seller List') }}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered sortable dataTable" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($blockContents as $key => $seller)
                    <tr>
                        <td>
                            <span class="item-index">{{ $key + 1 }}</span>
                            <input class="item-id" type="hidden" value="{{ $seller->id }}"/>
                        </td>
                        <td>{{$seller->user->name}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('script')
    @parent
    <script src="{{ static_asset('js/loading-overlay.min.js') }}"></script>

    <script type="text/javascript">
        // init sortable table
        applySortTable();

        function applySortTable() {
            const tableBody = $('.sortable tbody');
            tableBody.sortable(
                {
                    update: function (event, ui)  {
                        const listIndex = prepareListIndex(tableBody);
                        saveListIndex(listIndex);
                    }
                }
            );
        }

        function prepareListIndex(tableBody) {
            var listIndex = {};
            tableBody.find('tr').each(function (index) {
                const id = $(this).find('td:first-child .item-id').val();
                listIndex[id] = index + 1;
                $(this).find('td:first-child .item-index').text(index + 1);
            });
            return listIndex;
        }

        function saveListIndex(listIndex) {
            let data = {};
            data['listIndex'] = listIndex;
            data['_token'] = '{{ @csrf_token() }}';
            $.ajax({
                url: "{{ route('admin.api.custom_page.block.item.update_index', $block->id) }}",
                headers: {
                    "Authorization": "Bearer " + "{{ Session::get('laravel_token') }}",
                },
                method: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    $.LoadingOverlay("show", {
                        size: 10,
                        image: "",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
                complete: function () {
                },
                success: function (response) {
                    $.LoadingOverlay('hide');
                },
                error: function () {
                }
            });
        }

        var table = $(".dataTable").DataTable(
            {
                dom: 'Bfrtip',
                buttons : [ {
                    extend : 'excel',
                    text : 'Export to Excel',
                    filename: "Seller_Based_Selling_Report_"+new Date().toISOString().slice(0, 10),
                    exportOptions : {
                        modifier : {
                            // DataTables core
                            // order : 'index',  // 'current', 'applied', 'index',  'original'
                            page : 'all',      // 'all',     'current'
                            search : 'applied',     // 'none',    'applied', 'removed'
                            rows: ':visible'
                        }
                    }
                } ],
                "lengthMenu": [[50, 25, 50, -1], [50, 25, 50, "All"]],
                "order": [[ 0, "asc" ]],
                initComplete: function () {
                    this.api().columns(3).every( function () {
                        var column = this;
                        var select = $('<select><option value=""></option></select>')
                            .appendTo( $(column.footer()).empty() )
                            .on( 'change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );

                                column
                                    .search( val ? '^'+val+'$' : '', true, false )
                                    .draw();
                            } );

                        column.data().unique().sort().each( function ( d, j ) {
                            select.append( '<option value="'+d+'">'+d+'</option>' )
                        } );
                    } );
                }
            });

    </script>
@endsection
