$(document).on("nifty.ready", function () {
    $(".editor").each(function (el) {
        var $this = $(this);
        var buttons = $this.data("buttons");
        buttons = !buttons
            ? "bold,underline,italic,hr,|,ul,ol,|,align,paragraph,|,image,video,table,"
               +"link,undo,redo,font,fontsize,brush,|,source"
            : buttons;

        var editor = new Jodit(this, {
            uploader: {
                insertImageAsBase64URI: true,
            },
            toolbarAdaptive: false,
            defaultMode: "1",
            toolbarSticky: false,
            showXPathInStatusbar: false,
            buttons: buttons,
        });

    });
    if($('.newsletter-editor').length > 0){
        var newslettereditor = new Jodit('.newsletter-editor', {
            uploader: {
                insertImageAsBase64URI: false,
            },
            toolbarAdaptive: false,
            defaultMode: "1",
            toolbarSticky: false,
            showXPathInStatusbar: false,
            buttons: "bold,underline,italic,hr,|,ul,ol,|,align,paragraph,|,image,table,link,undo,redo,font,fontsize",
        });
    }

    $(".res-table").DataTable({
      dom: 'Bfrtip',
       buttons : [ {
            extend : 'excel',
            text : 'Export to Excel',
            exportOptions : {
                modifier : {
                    // DataTables core
                    order : 'index',  // 'current', 'applied', 'index',  'original'
                    page : 'all',      // 'all',     'current'
                    search : 'none'     // 'none',    'applied', 'removed'
                }
            },
            customizeData: function (data) {
                for (let i = 0; i < data.body.length; i++) {
                    for (let j = 0; j < data.body[i].length; j++) {
                        if (this.isLongNumber(data.body[i][j])) {
                            data.body[i][j] = '\u200C' + data.body[i][j]
                        }
                    }
                }
            },
            isLongNumber: function (text) {
                let reg = /^\d+$/ // only digits
                const EXCEL_MAX_NUMBER_LENGTH = 11

                return text.length > EXCEL_MAX_NUMBER_LENGTH && reg.test(text)
            },
            orientation: 'landscape'
        } ],
        responsive: true,
        paging: false,
        searching: true,
        ordering: true,
        bInfo: false,
    });

    // SELECT2 SINGLE
    // =================================================================
    // Require Select2
    // https://github.com/select2/select2
    // =================================================================
    $(".demo-select2").select2();

    // SELECT2 SINGLE
    // color select select2
    $(".color-var-select").select2({
        templateResult: colorCodeSelect,
        templateSelection: colorCodeSelect,
        escapeMarkup: function (m) {
            return m;
        },
    });
    function colorCodeSelect(state) {
        var colorCode = $(state.element).val();
        if (!colorCode) return state.text;
        return (
            "<span class='color-preview' style='background-color:" +
            colorCode +
            ";'></span>" +
            state.text
        );
    }

    // SELECT2 Maximum Limit 4
    // =================================================================
    // Require Select2
    // https://github.com/select2/select2
    // =================================================================
    $(".demo-select2-max-4").select2({
        maximumSelectionLength: 4,
    });

    $(".demo-select2-max-10").select2({
        maximumSelectionLength: 10,
    });

    // SELECT2 PLACEHOLDER
    // =================================================================
    // Require Select2
    // https://github.com/select2/select2
    // =================================================================
    $(".demo-select2-placeholder").select2({
        placeholder: "Select an option",
        allowClear: true,
    });

    // SELECT2 SELECT BOXES
    // =================================================================
    // Require Select2
    // https://github.com/select2/select2
    // =================================================================
    $(".demo-select2-multiple-selects").select2();

    // $('.demo-sw').each(function(){
    // 	new Switchery(this);
    // });
    //
    // $('.demo-dt-basic').on( 'length.dt', function ( e, settings, len ) {
    //
    // } );

    //$('.demo-dp-component .input-group.date').datepicker({autoclose:true, startDate: '-0d'});

    // BOOTSTRAP DATEPICKER WITH RANGE SELECTION
    // =================================================================
    // Require Bootstrap Datepicker
    // http://eternicode.github.io/bootstrap-datepicker/
    // =================================================================
    $("#demo-dp-range .input-daterange").datepicker({
        startDate: "-0d",
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true,
    });

    // language flag select2
    $(".country-flag-select").select2({
        templateResult: countryCodeFlag,
        templateSelection: countryCodeFlag,
        escapeMarkup: function (m) {
            return m;
        },
    });
    function countryCodeFlag(state) {
        var flagName = $(state.element).data("flag");
        if (!flagName) return state.text;
        return (
            "<img  class='flag' src='" +
            flagName +
            "' height='14' />" +
            state.text
        );
    }

    $(".pos-customer").select2({
        templateResult: posCustomerSelect,
        templateSelection: posCustomerSelect,
        escapeMarkup: function (m) {
            return m;
        },
    });
    function posCustomerSelect(state) {
        var contact = $(state.element).data("contact");
        if (!contact) return state.text;
        return (
            "<span class='d-flex justify-content-between'><span  class='flex-shrink-0 '>" +
            state.text +
            "</span><span class='flex-grow-1 text-truncate mar-lft text-right'>" +
            contact +
            "</span></span>"
        );
    }
    $('[data-toggle="tooltip"]').tooltip();
    $(document).on("click", function (e) {
        $('[data-toggle="popover"]').each(function () {
            if (
                !$(this).is(e.target) &&
                $(this).has(e.target).length === 0 &&
                $(".popover").has(e.target).length === 0
            ) {
                (
                    ($(this).popover("hide").data("bs.popover") || {})
                        .inState || {}
                ).click = false; // fix for BS 3.3.6
            }
        });
    });
    $(document).on('click', '.dropdown-menu', function (e) {
        e.stopPropagation();
    });
});

function showAlert(type, message) {
    $.niftyNoty({
        type: type,
        container: "floating",
        html: message,
        closeBtn: true,
        floating: {
            position: "top-right",
            animationIn: "fadeIn",
            animationOut: "fadeOut",
        },
        focus: true,
        timer: 3000,
    });
}

$(document).on('click','#btn-save, #btn-save-and-back', function(event){
    event.preventDefault();
    let formId = $(this).parents('form').attr('id');
    submitWithValidateForm('#'+formId);
})

function submitWithValidateForm(id){
    let validate = checkRequired(id);
    if(validate[1] == false){
        toastr.error("Please enter field required", "Error");
        $(id + ' .nav-pills li a[href="#'+validate[0]+'"]').trigger('click');
        return false;
    }else{
        $(id).submit();
    }
}

function checkRequired(id) {
    let temp = 0;
    let language_id = '';
    $(id + ' .form-control:not(div)').each(function() {
        if ($(this).prop('required') === true) {
            let value = $(this).val();
            if (value.trim().length == 0) {
                temp++;
                $(this).css('border-color', 'red');
                language_id = $(this).parent().parent().parent().attr('id');
            } else {
                $(this).css('border-color', '#828bb2');
            }
        }
    });
    if (temp > 0){
        return [language_id, false];
    }else return [language_id, true];
}