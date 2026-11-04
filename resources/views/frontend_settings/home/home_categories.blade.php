<div id="home-categories" class="tab-pane fade">
    <div class="row">
        <div class="col-sm-12">
            <a onclick="add_home_category()" class="btn btn-rounded btn-info pull-right">{{ __('Add New Category') }}</a>
        </div>
    </div>
    <br>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Home Categories') }}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered demo-dt-basic" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th width="10%">{{ __('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach(\App\HomeCategory::all() as $key => $home_category)
                    @if ($home_category->category != null)

                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$home_category->category->name}}</td>
                            <td><label class="switch">
                                    <input onchange="update_home_category_status(this)" value="{{ $home_category->id }}" type="checkbox" <?php if($home_category->status == 1) echo "checked";?> >
                                    <span class="slider round"></span></label></td>
                            <td>
                                <div class="btn-group dropdown">
                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                        {{ __('Actions') }} <i class="dropdown-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a onclick="edit_home_category({{ $home_category->id }})">{{ __('Edit') }}</a></li>
                                        <li>
                                            <a onclick="confirm_modal({{ json_encode(['item' => $home_category->category->name, 'url' => route('home_categories.destroy', $home_category->id)]) }});">
                                                {{ __('Delete') }}
                                            </a> 
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('script')
    @parent

    <script type="text/javascript">
        function add_home_category(){
            $.get('{{ route('home_categories.create') }}', {}, function(data){
                $('#home-categories').html(data);
                $('.demo-select2-placeholder').select2();
            });
        }

        function edit_home_category(id){
            let url = '{{ route("home_categories.edit", "home_category_id") }}';
            url = url.replace('home_category_id', id);
            $.get(url, {}, function(data){
                $('#home-categories').html(data);
                $('.demo-select2-placeholder').select2();
            });
        }

        function update_home_category_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('home_categories.update_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    showAlert('success', 'Home Page Category status updated successfully');
                }
                else{
                    showAlert('danger', 'Something went wrong');
                }
            });
        }
    </script>
@endsection
