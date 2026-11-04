<div id="home-banner-2" class="tab-pane fade home-banner">
    <div class="row">
        <div class="col-sm-12">
            <a onclick="add_banner_2()" class="btn btn-rounded btn-info pull-right">{{ __('Add New Banner') }}</a>
        </div>
    </div>
    <br>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Home banner') }}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered demo-dt-basic sortable" cellspacing="0" width="100%"
                   data-update-index-url="{{ route('admin.api.frontend_setting.home_banner.update_index') }}"
            >
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Photo') }}</th>
                    <th>{{ __('Position') }}</th>
                    <th>{{ __('Active / NonActive') }}</th>
                    <th width="10%">{{ __('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($listBanner as $key => $banner)
                    <tr>
                        <td>
                            <span class="item-index">{{ ($key+1) }}</span>
                            <input class="item-id" type="hidden" value="{{ $banner->id }}"/>
                        </td>
                        <td><img loading="lazy"  class="img-md" src="{{ my_asset($banner->photo)}}" alt="banner Image"></td>
                        <td>{{ __('Banner Position ') }}{{ $banner->position }}</td>
                        <td><label class="switch">
                                <input onchange="toggle_publish(this, '{{ route('admin.api.frontend_setting.home_banner.toggle_publish', $banner->id) }}')"
                                       type="checkbox" @if ($banner->published == 1) checked @endif >
                                <span class="slider round"></span></label></td>
                        <td>
                            <div class="btn-group dropdown">
                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                    {{ __('Actions') }} <i class="dropdown-caret"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a onclick="edit_home_banner_2({{ $banner->id }})">{{ __('Edit') }}</a></li>
                                    <li>
                                        <a onclick="confirm_modal({{ json_encode(['item' => 'Banner '. ($key + 1), 'url' => route('home_banners.destroy', $banner->id)]) }});">
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

    <script type="text/javascript">
        function add_banner_2(){
            $.get('{{ route('home_banners.create', 2)}}', {}, function(data){
                $('#home-banner-2').html(data);
            });
        }

        function edit_home_banner_2(id){
            let url = '{{ route("home_banners.edit", "home_banner_id") }}';
            url = url.replace('home_banner_id', id);
            $.get(url, {}, function(data){
                $('#home-banner-2').html(data);
                $('.demo-select2-placeholder').select2();
            });
        }
    </script>
@endsection
