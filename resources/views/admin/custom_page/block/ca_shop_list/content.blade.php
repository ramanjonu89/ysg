<div id="tab-1" class="tab-pane fade active in">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Content') }}</h3>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" action="{{ route('admin.custom_page.block.update', $block->id) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="active">{{ __('Active') }}</label>
                        <div class="col-sm-9">
                            <label class="switch">
                                <input type="checkbox" id="active" name="active" {{$block->is_enabled?'checked':''}}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    @include('admin.custom_page.block.inc.margin_options')
                    @include('admin.custom_page.block.inc.hide_on_mobile_options')

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="name">{{ __('Title') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ __('Title') }}" id="title" name="title"
                                   class="form-control" required value="{{$block->title}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="name">{{ __('Type') }}</label>
                        <div class="col-sm-9">
                            <select name="type" id="type" class="form-control">
                                @foreach($blockTypes as $value => $title)
                                    <option value="{{$value}}" {{ ($block->type == $value)?'selected':'' }}>{{$title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="{{ route('admin.custom_page.block.index') }}">
                        <button type="button" class="btn btn-default">{{ __('Cancel') }}</button>
                    </a>
                    <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
