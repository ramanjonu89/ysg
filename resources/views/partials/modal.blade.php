<script type="text/javascript">
    function confirm_delete(message, el) {
        bootbox.confirm(message, function (result) {
            if (result) {
                $(el).closest('form').submit();
            }
        })
    }

    function confirm_modal(data) {
        const message = `Do you want to delete "${data.item}" ?`;
        
        jQuery('#confirm-delete').modal('show', {backdrop: 'static'});
        document.getElementById('delete_link').setAttribute('href', data.url);
        $('#confirm_message').html(message);
    }

</script>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">{{ __('Confirmation') }}</h4>
            </div>
            <div class="modal-body">
                <p id="confirm_message">

                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                <a id="delete_link" class="btn btn-danger btn-ok">{{ __('Delete') }}</a>
            </div>
        </div>
    </div>
</div>

