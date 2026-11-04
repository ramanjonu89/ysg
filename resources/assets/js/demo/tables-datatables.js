
// Tables-DataTables.js
// ====================================================================
// This file should not be included in your project.
// This is just a sample how to initialize plugins or components.
//
// - ThemeOn.net -



$(document).on('nifty.ready', function() {


    // DATA TABLES
    // =================================================================
    // Require Data Tables
    // -----------------------------------------------------------------
    // http://www.datatables.net/
    // =================================================================

    $.fn.DataTable.ext.pager.numbers_length = 5;


    // Basic Data Tables with responsive plugin
    // -----------------------------------------------------------------
   var table1 = $('.demo-dt-basic1').dataTable( {
     dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "responsive": true,
        "language": {
            "paginate": {
              "previous": '<i class="demo-psi-arrow-left"></i>',
              "next": '<i class="demo-psi-arrow-right"></i>'
            }
        }
    } );
    var table = $(".demo-dt-basic").DataTable(
    {
     dom: 'Bfrtip',
         buttons : [ {
            extend : 'excel',
            text : 'Export to Excel',
            filename: "Seller_Based_Selling_Report_"+new Date().toISOString().slice(0, 10),
            exportOptions : {
                modifier : {
                    // DataTables core
                    order : 'index',  // 'current', 'applied', 'index',  'original'
                    page : 'all',      // 'all',     'current'
                    search : 'applied',     // 'none',    'applied', 'removed'
                     rows: ':visible'
                }
            }
        } ],
          "lengthMenu": [[50, 25, 50, -1], [50, 25, 50, "All"]],
            "order": [[ 1, "desc" ]],
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
    
    var table2 = $(".demo-dt-basic2").DataTable(
    {
     dom: 'Bfrtip',
         buttons : [ {
            extend : 'excel',
            text : 'Export to Excel',
            filename: "customer_report_"+new Date().toISOString().slice(0, 10),
            exportOptions : {
                modifier : {
                    // DataTables core
                    order : 'index',  // 'current', 'applied', 'index',  'original'
                    page : 'all',      // 'all',     'current'
                    search : 'applied'     // 'none',    'applied', 'removed'
                }
            }
        } ],
         "order": [[ 1, "desc" ]],
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
        bInfo: true,
         "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
   
    }
    
    
);

var table3 = $(".demo-dt-basic3").DataTable(
    {
     dom: 'Bfrtip',
         buttons : [ {
            extend : 'excel',
            text : 'Export to Excel',
            filename: "total_sales"+new Date().toISOString().slice(0, 10),
            exportOptions : {
                modifier : {
                    // DataTables core
                    order : 'index',  // 'current', 'applied', 'index',  'original'
                    page : 'all',      // 'all',     'current'
                    search : 'applied'     // 'none',    'applied', 'removed'
                }
            }
        } ],
          "order": [[ 1, "desc" ]],
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
        bInfo: true,
         "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
   
    }
    
    
);
var table4 = $(".demo-dt-basic4").DataTable(
    {
     dom: 'Bfrtip',
         buttons : [ {
            extend : 'excel',
            text : 'Export to Excel',
            filename: "total_coupon_discount_"+new Date().toISOString().slice(0, 10),
            exportOptions : {
                modifier : {
                    // DataTables core
                    order : 'index',  // 'current', 'applied', 'index',  'original'
                    page : 'all',      // 'all',     'current'
                    search : 'applied'     // 'none',    'applied', 'removed'
                }
            }
        } ],
          "order": [[ 1, "desc" ]],
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
        bInfo: true,
         "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
   
    }
    
    
);
 // Date range vars
    minDateFilter = "";
    maxDateFilter = "";
    
    $("#daterange").daterangepicker();
    $("#daterange").on("apply.daterangepicker", function(ev, picker) {
        minDateFilter = Date.parse(picker.startDate);
        maxDateFilter = Date.parse(picker.endDate);
        console.log("inside");
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var date = Date.parse(data[1]);
            
            if (
            (isNaN(minDateFilter) && isNaN(maxDateFilter)) ||
            (isNaN(minDateFilter) && date <= maxDateFilter) ||
            (minDateFilter <= date && isNaN(maxDateFilter)) ||
            (minDateFilter <= date && date <= maxDateFilter)
            ) {
                return true;
            }
            return false;
        });
        table.draw();
        
    }); 
    
    
    // Date range vars
    minDateFilter = "";
    maxDateFilter = "";
    
    $("#daterange2").daterangepicker();
    $("#daterange2").on("apply.daterangepicker", function(ev, picker) {
        minDateFilter = Date.parse(picker.startDate);
        maxDateFilter = Date.parse(picker.endDate);
        console.log("inside3");
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var date = Date.parse(data[1]);
            
            if (
            (isNaN(minDateFilter) && isNaN(maxDateFilter)) ||
            (isNaN(minDateFilter) && date <= maxDateFilter) ||
            (minDateFilter <= date && isNaN(maxDateFilter)) ||
            (minDateFilter <= date && date <= maxDateFilter)
            ) {
                return true;
            }
            return false;
        });
        table3.draw();
        
    }); 
    
       // Date range vars
    minDateFilter = "";
    maxDateFilter = "";
    
    $("#daterange4").daterangepicker();
    $("#daterange4").on("apply.daterangepicker", function(ev, picker) {
        minDateFilter = Date.parse(picker.startDate);
        maxDateFilter = Date.parse(picker.endDate);
        console.log("inside4");
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var date = Date.parse(data[1]);
            
            if (
            (isNaN(minDateFilter) && isNaN(maxDateFilter)) ||
            (isNaN(minDateFilter) && date <= maxDateFilter) ||
            (minDateFilter <= date && isNaN(maxDateFilter)) ||
            (minDateFilter <= date && date <= maxDateFilter)
            ) {
                return true;
            }
            return false;
        });
        table4.draw();
        
    }); 


    // Row selection (single row)
    // -----------------------------------------------------------------
    var rowSelection = $('#demo-dt-selection').DataTable({
        "responsive": true,
        "language": {
            "paginate": {
              "previous": '<i class="demo-psi-arrow-left"></i>',
              "next": '<i class="demo-psi-arrow-right"></i>'
            }
        }
    });

    $('#demo-dt-selection').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            rowSelection.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );






    // Row selection and deletion (multiple rows)
    // -----------------------------------------------------------------
    var rowDeletion = $('#demo-dt-delete').DataTable({
        "responsive": true,
        "language": {
            "paginate": {
              "previous": '<i class="demo-psi-arrow-left"></i>',
              "next": '<i class="demo-psi-arrow-right"></i>'
            }
        },
        "dom": '<"toolbar">frtip'
    });
    $('#demo-custom-toolbar').appendTo($("div.toolbar"));

    $('#demo-dt-delete tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );

    $('#demo-dt-delete-btn').click( function () {
        rowDeletion.rows('.selected').remove().draw( false );
    } );






    // Add Row
    // -----------------------------------------------------------------
    var t = $('#demo-dt-addrow').DataTable({
        "responsive": true,
        "language": {
            "paginate": {
              "previous": '<i class="demo-psi-arrow-left"></i>',
              "next": '<i class="demo-psi-arrow-right"></i>'
            }
        },
        "dom": '<"newtoolbar">frtip'
    });
    $('#demo-custom-toolbar2').appendTo($("div.newtoolbar"));

    var randomInt = function(min,max){
        return Math.floor(Math.random()*(max-min+1)+min);
    }
    $('#demo-dt-addrow-btn').on( 'click', function () {
        t.row.add( [
            'Adam Doe',
            'New Row',
            'New Row',
            randomInt(1,100),
            '2015/10/15',
            '$' + randomInt(1,100) +',000'
        ] ).draw();
    } );


});
