<div class="panel-heading">
    <h3 class="panel-title text-center">
        {{ $title ?? __('Page blocks') }}
    </h3>
</div>

<div class="panel-body">
    <div class="row">
        <div id="section-add-block" class="col-sm-12">
            <div class="col-sm-6">
                <select id="block-ids" class="form-control">
                    @foreach($allBlocks as $value => $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <a onclick="add_block()" class="btn btn-info">{{__('Add') }}</a>
                <a href="{{ route('admin.custom_page.block.index') }}"
                   class="btn btn-info">{{__('View all blocks') }}</a>
                <a href="javascript:void(0)" onclick="showCreateModal();"
                   class="btn btn-info pull-right">{{__('Create new block') }}</a>
            </div>
        </div>

        <div class="col-sm-12 mt-3">
            <table class="table table-striped table-bordered sortable" cellspacing="0" width="100%"
                   data-update-index-url="{{ generateUpdateIndexBlockToPageUrl($pageType, $page->id) }}"
            >
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{__('Title') }}</th>
                    <th>{{__('Active') }}</th>
                    <th width="10%">{{__('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($blocks as $key => $block)
                    <tr>
                        <td>
                            <span class="item-index">{{ ($key+1) }}</span>
                            <input class="item-id" type="hidden" value="{{ $block->id }}"/>
                        </td>
                        <td>
                          <div>{{$block->title}}</div>
                          <small>{{$block->type}}</small>
                        </td>
                        <td>{{($block->is_enabled)?'Yes':'No'}}</td>
                        <td>
                            <div class="btn-group dropdown">
                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon"
                                        data-toggle="dropdown" type="button">
                                    {{__('Actions') }} <i class="dropdown-caret"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" style="opacity: 1;">
                                    <li>
                                        <a href="{{route('admin.custom_page.block.edit', $block->id)}}">{{__('Edit') }}</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="showDeleteModal({{ json_encode(['item' => $block->title, 'blockId' => $block->id]) }});">
                                          {{ __('Remove') }}
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

@include('admin.inc.modal_create_block', ['page_type' => $pageType??'page'])

@section('script')
    @parent
    <script src="{{ static_asset('js/loading-overlay.min.js') }}"></script>

    <script type="text/javascript">
      let addUrl = '{{ generateAddBlockToPageUrl($pageType, $page->id) }}'
      let removeUrl = '{{ generateRemoveBlockToPageUrl($pageType, $page->id) }}'

      function add_block() {
        const blockId = $('#section-add-block select').val();
        let data = {
          '_token': '{{ @csrf_token() }}',
          'pageId': {{$page->id}},
          'blockId': blockId,
        };
        $.ajax({
          url: addUrl,
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
            $.LoadingOverlay('hide');
          },
          success: function (response) {
            window.location.reload();
          },
          error: function () {
          }
        });
      }

      function remove_block(blockId) {
        let data = {
          '_token': '{{ @csrf_token() }}',
          'pageId': {{$page->id}},
          'blockId': blockId,
        };
        $.ajax({
          url: removeUrl,
          headers: {
            "Authorization": "Bearer " + "{{ Session::get('laravel_token') }}",
          },
          method: 'DELETE',
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
            $.LoadingOverlay('hide');
          },
          success: function (response) {
            window.location.reload();
          },
          error: function () {
          }
        });
      }

      // init sortable table
      $('.sortable').find('tbody').sortable(
          {
              update: function (event, ui) {
                  let self = this;
                  const url = $(this).parent('.sortable').data('update-index-url');
                  if (url != null) {
                      const listIndex = prepareListIndex(self);
                      saveListIndex(listIndex, url);
                  }
              }
          }
      );

      function prepareListIndex(tbody) {
          let listIndex = {};
          $(tbody).find('tr').each(function (index) {
              const id = $(this).find('td:first-child .item-id').val();
              listIndex[id] = index + 1;
              $(this).find('td:first-child .item-index').text(index + 1);
          });
          return listIndex;
      }

      function saveListIndex(listIndex, url) {
          let data = {};
          data['listIndex'] = listIndex;
          data['_token'] = '{{ @csrf_token() }}';
          $.ajax({
              url: url,
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
              success: function (data) {
                  $.LoadingOverlay('hide');
                  console.log(data)
                  if (data.status === true) {
                      showAlert('success', 'Position updated successfully');
                  } else {
                      showAlert('danger', 'Have error!');
                  }
              },
              error: function () {
              }
          });
      }

      function showCreateModal() {
        $('#create-block-modal').modal('show', {backdrop: 'static'});
      }

      function submitForm() {
        $('#create-block-modal form').submit();
      }
    </script>
@endsection
