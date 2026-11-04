<div id="tab-2" class="tab-pane fade">
    <div class="row">
        <div class="col-sm-12">
            <a href="{{ route('admin.custom_page.block.item.create', $block->id)}}"
               class="btn btn-rounded btn-info pull-right">{{ __('Add New Item') }}</a>
        </div>
    </div>
    <br>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Items') }}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered demo-dt-basic sortable" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Active') }}</th>
                    <th width="10%">{{ __('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($blockContents as $key => $item)
                    <tr>
                        <td>
                            <span class="item-index">{{ ($key+1) }}</span>
                            <input class="item-id" type="hidden" value="{{ $item->id }}"/>
                        </td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->is_enabled ? 'Yes' : 'No'}}</td>
                        <td>
                            <div class="btn-group dropdown">
                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon"
                                        data-toggle="dropdown" type="button">
                                    {{ __('Actions') }} <i class="dropdown-caret"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" style="opacity: 1;">
                                    <li>
                                        <a href="{{ route('admin.custom_page.block.item.edit', ['block' => $block, 'content' => $item]) }}">
                                            {{ __('Edit') }}
                                        </a>
                                    </li>
                                    <li><a 
                                        onclick="confirm_modal({{ json_encode(['item' => $item->title, 'url' => route('admin.custom_page.block.item.destroy', ['block' => $block, 'content' => $item])]) }});">
                                            {{ __('Delete') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
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
    </script>
@endsection
