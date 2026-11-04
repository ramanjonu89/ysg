{{-- 
  Usage: 
  <span class="show-ajax-modal-detail" data-url="{{ route('ajax.hitories.show', $entity->id) }}" style="cursor: pointer;"></span> 
--}}
@section('modal')
    <div class="modal fade" id="ajax-modal" tabindex="-1" role="dialog" aria-labelledby="ajaxModalLabel" aria-hidden="true">
        <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="ajaxModalLabel">{{ $modalTitle }}</h4>
                    </div>
                    <div class="modal-body ajax-modal-body">
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
        </div>
    </div>
@endsection

@section('script')
    @parent
    <script src="{{ static_asset('js/loading-overlay.min.js') }}"></script>

    <script type="text/javascript">
        $('.show-ajax-modal-detail').click(function() {
            var url = $(this).data("url"); 
            $.ajax({
                type: "GET",
                url: url,
                success: function(res) {
                    // update modal content
                    $('.ajax-modal-body').html(res.body);
                    $('#ajaxModalLabel').html(res.title)
                    // show modal
                    $('#ajax-modal').modal('show');
                    
                },
                error:function(request, status, error) {
                    console.log("ajax call went wrong:" + request.responseText);
                }
            });
        })
    </script>
@endsection