@section('modal')
    <div class="modal fade" id="create-block-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="form-horizontal" id="form-multiple-language"  action="{{ route('admin.custom_page.block.store') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">{{ __('Confirmation') }}</h4>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="page_type" value="{{ $pageType ?? 'page' }}" />
                        @if ($page ?? false)
                            <input type="hidden" name="entity_id" value="{{ $page->id }}" />
                        @endif
                        <div class="panel-body">
                            <div id="tab-form">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <ul class="nav nav-pills mb-5" style="background-color: #d486e1;">
                                            @if (isset($systemLanguages) && count($systemLanguages) > 0)
                                                @foreach ($systemLanguages as $key => $language)
                                                    <li class="{{ $key == 0 ? 'active' : '' }}"><a href="#tab{{ $language->code }}" style="color:#fff" data-toggle="tab">{{ $language->name }}</a></li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="tab-content clearfix" style="    background-color: #fff">
                                    @if (isset($systemLanguages) && count($systemLanguages) > 0)
                                        @foreach ($systemLanguages as $key => $language)
                                            <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="tab{{ $language->code }}">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label" for="name">{{ __('Title') }}</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]" class="form-control" {{ $key == 0 ? 'required' : '' }}>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="language_id[]" value="{{ $language->id }}">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="name">{{ __('Type') }}</label>
                                    <div class="col-sm-10">
                                        <select name="type" id="type" class="form-control" onchange="coupon_form()">
                                            @foreach($blockTypes as $value => $title)
                                                <option value="{{$value}}">{{$title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button id="btn-save" class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="delete-block-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <a id="delete_block" class="btn btn-danger btn-ok">{{ __('Delete') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    @parent

    <script type="text/javascript">
        function showCreateModal()
        {
            $('#create-block-modal').modal('show', {backdrop: 'static'});
        }

        function showDeleteModal(data)
        {
            const message = `Do you want to delete "${data.item}" ?`;

            $('#delete-block-modal #confirm_message').html(message);
            $('#delete-block-modal').modal('show', {backdrop: 'static'});
            document.getElementById('delete_block').setAttribute('onClick', 'remove_block(' + data.blockId + ')');
        }
    </script>
@endsection
