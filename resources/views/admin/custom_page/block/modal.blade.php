<script>
$(document).on('change','#type', function(){
    $('#confirm-modal').modal('show');
})

$(document).on('click', '#change', function() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        }
    });
    $.ajax({
        type: "POST",
        data: {
            block_id: $('#block_id').val(),
            type: $('#type').val()
        },
        dataType: "json",
        url: '{{ route('admin.custom_page.block.change-type') }}',
        success: function(response) {
            window.location.reload();
        },
        error: function(error) {
            window.location.reload();
        }
    });
})
</script>

<div class="modal fade" id="confirm-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">{{ __('Confirmation') }}</h4>
            </div>
            <div class="modal-body">
                <p>{{ __('You want to change the type of block ?') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button id="change" class="btn btn-danger btn-ok">{{ __('Change') }}</button>
            </div>
        </div>
    </div>
</div>

