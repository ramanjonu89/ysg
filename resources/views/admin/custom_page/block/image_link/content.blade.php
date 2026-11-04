<div id="tab-1" class="tab-pane fade active in">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Content') }}</h3>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" id="form-multiple-language" action="{{ route('admin.custom_page.block.update', $block->id) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="panel-body">
                    <div id="tab-form">
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10">
                                <ul class="nav nav-pills mb-5" style="background-color: #d486e1;">
                                    @if (isset($systemLanguages) && count($systemLanguages) > 0)
                                        @foreach ($systemLanguages as $key => $language)
                                            <li class="{{ $key == 0 ? 'active' : '' }}"><a
                                                    href="#{{ $language->id }}" style="color:#fff"
                                                    data-toggle="tab">{{ $language->name }}</a></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content clearfix" style="    background-color: #fff">
                            @if (isset($systemLanguages) && count($systemLanguages) > 0)
                                @foreach ($systemLanguages as $key => $language)
                                    <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ $language->id }}">
                                        @if (isset($block) && $block->ysgBlockTranslation($language->id) !== null)
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="name">{{ __('Title') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]"
                                                       class="form-control" {{ $key == 0 ? 'required' : '' }}  value="{{$block->ysgBlockTranslation($language->id)->title}}">
                                            </div>
                                        </div>
                                        @else
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="name">{{ __('Title') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]"
                                                       class="form-control" {{ $key == 0 ? 'required' : '' }} value="{{$block->title}}">
                                            </div>
                                        </div>
                                        @endif
                                        <input type="hidden" name="language_id[]" value="{{ $language->id }}">
                                    </div>
                                @endforeach
                            @endif

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
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="{{ route('admin.custom_page.block.index') }}">
                        <button type="button" class="btn btn-default">{{ __('Cancel') }}</button>
                    </a>
                    <button class="btn btn-purple" id="btn-save" type="button">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
