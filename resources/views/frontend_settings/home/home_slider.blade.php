<div id="home-slider" class="tab-pane fade home-slider">
    <input type="hidden" >
    <div class="row">
        <div class="col-sm-12">
            <a onclick="add_slider()" class="btn btn-rounded btn-info pull-right">{{ __('Add New Slider') }}</a>
        </div>
    </div>
    <br>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Home slider') }}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered demo-dt-basic sortable" cellspacing="0" width="100%"
                   data-update-index-url="{{ route('admin.api.frontend_setting.home_slider.update_index') }}"
            >
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Photo') }}</th>
                    <th width="50%">{{ __('Link') }}</th>
                    <th>{{ __('Active / NonActive') }}</th>
                    <th width="10%">{{ __('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($sliderList as $key => $slider)
                    <tr>
                        <td>
                            <span class="item-index">{{ ($key+1) }}</span>
                            <input class="item-id" type="hidden" value="{{ $slider->id }}"/>
                        </td>
                        <td><img loading="lazy"  class="img-md" src="{{ my_asset($slider->photo)}}" alt="Slider Image"></td>
                        <td>{{$slider->link}}</td>
                        <td><label class="switch">
                                <input onchange="toggle_publish(this, '{{ route('admin.api.frontend_setting.home_slider.toggle_publish', $slider->id) }}')"
                                       value="{{ $slider->id }}" type="checkbox" @if ($slider->published == 1) checked @endif >
                                <span class="slider round"></span></label></td>
                        <td>
                            <div class="btn-group dropdown">
                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                    {{ __('Actions') }} <i class="dropdown-caret"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a onclick="edit_slider({{ $slider->id }});">{{ __('Edit') }}</a></li>
                                    <li>
                                        <a onclick="confirm_modal({{ json_encode(['item' => 'Slider '. ($key + 1), 'url' => route('sliders.destroy', $slider->id)]) }});">
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
        function add_slider(){
            $.get('{{ route('sliders.create') }}', {}, function(data){
                $('#home-slider').html(data);
            });
        }

        function edit_slider(id){
            let url = '{{ route("sliders.edit", "slider_id") }}';
            url = url.replace('slider_id', id);
            $.get(url, {}, function(data){
                $('#home-slider').html(data);
            });
        }
    </script>
@endsection
