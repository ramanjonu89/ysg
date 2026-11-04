<!-- Modal -->
<div id="productListModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Product List</h4>
            </div>
            <div class="modal-body">
                <table id="productListTable" class="display table table-striped table-bordered sortable dataTable"
                       style="width:100%">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Seller</th>
                        <th>Brands</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>SubSubCategory</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Seller</th>
                        <th>Brands</th>
                        <th>Category</th>
                        <th>SubCategory</th>
                        <th>SubSubCategory</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="productList.onAdd()">Add</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script type="text/javascript">
      let productList = {
        init: function () {
          this.setVars();
          this.setEl();
          this.setEvents();
        },
        setVars: function () {
          this.selectedIds = [];
        },
        setEl: function () {
          this.$elModal = $('#productListModal');
          this.$elTable = $('#productListTable');
          // this.apiUrl = '/admin/product-list';
          this.$table = null;
        },
        setEvents() {
          let self = this;
          self.initTable();

          this.$table.on('select', function (e, dt, type, indexes) {
            if (type === 'row') {
              self.setSelected()
            }
          })
            .on('deselect', function (e, dt, type, indexes) {
              if (type === 'row') {
                self.setSelected()
              }
            });
        },
        initTable: function () {
          let self = this;
          this.$table = this.$elTable.DataTable({
            select: true,
            processing: true,
            serverSide: true,
            searchDelay: 1000,
            ajax: {
              url: '{{ route('admin.api.product-list') }}',
              type: 'POST',
              data: {
                isPublished: true,
                _token: '{{ @csrf_token() }}'
              }
            },
            columns: [
              {data: 'name', orderable: false},
              {data: 'user.name', searchable: false, orderable: false},
              {data: 'brand.name', searchable: false, orderable: false},
              {data: 'category.name', searchable: false, orderable: false},
              {data: 'sub_category.name', searchable: false, orderable: false},
              {data: 'sub_sub_category.name', searchable: false, orderable: false},
            ]
          });
        },
        setSelected() {
          const rows = this.$table.rows('.selected').indexes();
          productList.selectedIds = this.$table.rows(rows).data().pluck('id').toArray();
        },
        onAdd() {
          this.$elModal.modal('hide');
          this.callback(productList.selectedIds);
        },
        callback() {
          console.log('need to add callback function');
        }
      };
      $(document).ready(function () {
        productList.init();
      });
    </script>
@endpush
