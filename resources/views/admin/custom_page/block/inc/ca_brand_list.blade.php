<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">{{ __('Brand List') }}</h3>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-bordered sortable dataTable" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>{{ __('Title') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($blockContents as $key => $brand)
                <tr>
                    <td>{{$brand->name}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

@section('script')
    @parent
    <script type="text/javascript">
        var table = $(".dataTable").DataTable(
            {
                dom: 'Bfrtip',
                buttons : [ {
                    extend : 'excel',
                    text : 'Export to Excel',
                    filename: "Brand_Based_Selling_Report_"+new Date().toISOString().slice(0, 10),
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

