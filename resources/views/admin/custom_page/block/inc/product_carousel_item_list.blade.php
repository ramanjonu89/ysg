<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title text-center">
            {{ $title ?? __('Products') }}
        </h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-rounded btn-info pull-right" data-toggle="modal" data-target="#productListModal">
                    {{__('Add New Products') }}
                </button>
            </div>
            <div class="col-sm-12 mt-3">
                <table class="table table-striped table-bordered sortable" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{__('Title') }}</th>
                        <th width="40%">{{__('Image') }}</th>
                        <th width="10%">{{__('Options') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($blockContents as $key => $item)
                        <tr>
                            <td>
                                <span class="item-index">{{ ($key+1)}}</span>
                                <input class="item-id" type="hidden" value="{{ $item->id }}"/>
                            </td>
                            <td>{{$item->title}}</td>
                            <td><img class="img-responsive" src="{{ $item->image }}"/></td>
                            <td>
                                <div class="btn-group dropdown">
                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon"
                                            data-toggle="dropdown" type="button">
                                        {{__('Actions') }} <i class="dropdown-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" style="opacity: 1;">
                                        <li>
                                          <a onclick="confirm_modal({{ json_encode(['item' => $item->title, 'url' => route('admin.custom_page.block.item.destroy', ['block' => $block, 'content' => $item])]) }});">
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

    @include('admin.components.product_list')

</div>
@push('script')
    <script src="{{ static_asset('js/loading-overlay.min.js') }}"></script>
    <script type="text/javascript">

      let itemList = {
        init: function () {
          this.setVars();
          this.setEl();
          this.setEvents();
        },
        setVars: function () {
        },
        setEl: function () {
        },
        setEvents() {
        },
        addProducts(ids) {
          let data = {
            ids: ids,
            _token: '{{ @csrf_token() }}',
          };
          $.ajax({
            url: "{{ route('admin.api.custom_page.block.item.add_product', $block->id) }}",
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
            complete: function (response) {
            },
            success: function (response) {
              location.reload();
            },
            error: function (response) {
              const data = response.responseJSON;
              $.LoadingOverlay('hide');
              showAlert('danger', data.message);
            }
          });
        },
        setSelected() {
          const rows = this.$table.rows('.selected').indexes();
          productList.selectedIds = this.$table.rows(rows).data().pluck('id').toArray();
        },
        onAdd(ids) {
          this.addProducts(ids);
        },
        callback() {
          console.log('in itemList.callback()');
        }
      };

      $(document).ready(function () {
        productList.callback = function (ids) {
          itemList.onAdd(ids);
        };
      });

      // init sortable table
      applySortTable();
      function applySortTable() {
        const tableBody = $('.sortable tbody');
        tableBody.sortable(
          {
            placeholder: "ui-state-highlight",
            update: function (event, ui) {
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
@endpush
