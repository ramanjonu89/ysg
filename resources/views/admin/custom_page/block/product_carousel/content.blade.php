@php
    $start_date = !empty($block->start_date) ? date('d/m/Y H:i', $block->start_date) : 0;
    $end_date   = !empty($block->end_date) ? date('d/m/Y H:i', $block->end_date) : 0;
@endphp

<form class="form-horizontal" id="form-multiple-language" action="{{ route('admin.custom_page.block.update', $block->id) }}" method="POST"
      enctype="multipart/form-data">
    @csrf
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Content') }}</h3>
        </div>
        <div class="panel-body">
            <div id="tab-form">
                <div class="row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <ul class="nav nav-pills mb-5" style="background-color: #d486e1;">
                            @if (isset($systemLanguages) && count($systemLanguages) > 0)
                                @foreach ($systemLanguages as $key => $language)
                                    <li class="{{ $key == 0 ? 'active' : '' }}">
                                        <a href="#{{ $language->id }}" style="color:#fff" data-toggle="tab">{{ $language->name }}</a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

                @include('admin.custom_page.block.inc.published', ['block' => $block])
                @include('admin.custom_page.block.inc.datetimer')

                <div class="tab-content clearfix" style="    background-color: #fff">
                    @if (isset($systemLanguages) && count($systemLanguages) > 0)
                        @foreach ($systemLanguages as $key => $language)
                            <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ $language->id }}">
                                @php
                                $titleValue = (old('title') && isset(old('title')[$key])) ? old('title')[$key] : $block->getTitle($language->id);
                                @endphp

                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="name">{{__('Title') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]"
                                               class="form-control" {{ $key == 0 ? 'required' : '' }}
                                               value="{{ $titleValue }}">
                                    </div>
                                </div>
                                <input type="hidden" name="language_id[]" value="{{ $language->id }}">
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{__('Type') }}</label>
                    <div class="col-sm-9">
                        <select name="type" id="type" class="form-control" autocomplete="off" disabled>
                            @foreach($blockTypes as $value => $title)
                                <option value="{{$value}}" {{ ($block->type == $value)?'selected':'' }}>{{$title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="mobile_bg">{{ __('Mobile Background') }} <span
                                class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <div id="mobile_bg" data-image="{{$block->options->mobile_bg??''}}"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="tablet_bg">{{ __('Tablet Background') }} <span
                                class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <div id="tablet_bg" data-image="{{$block->options->tablet_bg??''}}"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="pc_bg">{{ __('PC Background') }} <span
                                class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <div id="pc_bg" data-image="{{$block->options->pc_bg??''}}"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="bg_padding">{{__('Padding') }}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{__('0,0,0,0') }}" id="bg_padding" name="options[bg_padding]"
                               class="form-control"
                               value="{{$block->options->bg_padding??(old('options')?old('options')['bg_padding']:'0,0,0,0')}}">
                        <small>{{__('Top,Left,Right,Bottom (as pixel)')}}</small>
                    </div>
                </div>

                @include('admin.custom_page.block.inc.margin_options')
                @include('admin.custom_page.block.inc.hide_on_mobile_options')
            </div>
        </div>
        <div class="panel-footer text-right">
            <a href="{{ route('admin.custom_page.block.index') }}">
                <button type="button" class="btn btn-default">{{__('Cancel') }}</button>
            </a>
            <button class="btn btn-purple" id="btn-save" type="button">{{__('Save') }}</button>
            @if ($block->page || $block->microsite)
                <button class="btn btn-purple" id="btn-save-and-back" type="button" name="redirect"
                        value="{{ $block->page_type }}">{{__("Save and back to $block->page_type")}}</button>
            @endif

        </div>
    </div>
</form>

<input type="hidden" id="block_id" value="{{$block->id}}">
@include('admin.custom_page.block.modal')
<style>
    .d-none {
        display: none !important;
    }
</style>
@section('script')
    <script type="text/javascript">

        $(document).ready(function () {
            const bg = ['mobile_bg', 'tablet_bg', 'pc_bg'];
            bg.forEach(addImagePicker);

            function addImagePicker(item, index, arr) {
            const el = $("#" + item);

            let options = {
                fieldName: 'options[' + item + ']',
                maxCount: 1,
                rowHeight: '200px',
                groupClassName: 'col-md-4 col-sm-9 col-xs-6',
                maxFileSize: '',
                allowedExt: 'jpeg|png|jpg|gif|svg',
                dropFileLabel: "Drop Here",
                onExtensionErr: function (index, file) {
                alert('Please only input png or jpg type file')
                },
                onSizeErr: function (index, file) {
                alert('File size too big');
                }
            };

            if (el.data('image')) {
                options.placeholderImage = {
                image: el.data('image'),
                width: '100%'
                }
            }

            el.spartanMultiImagePicker(options);
            }
            $('.demo-select2').select2();
            $('input[name="daterange"]').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                locale: {
                    format: 'DD/MM/YYYY HH:mm:ss'
                }
            });

            $('input[name="start_date"]').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                singleDatePicker: true,
                locale: {
                    format: 'DD/MM/YYYY HH:mm:ss'
                }
            });

            $('input[name="end_date"]').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                singleDatePicker: true,
                locale: {
                    format: 'DD/MM/YYYY HH:mm:ss'
                }
            });

            const date_timer_type = $('#date_timer_type').val();
            enable_date_ranger(date_timer_type);
        });

        function enable_date_ranger(date_timer_type) {
            if (date_timer_type == 'start_date_only') {
                $('.start-date').removeClass('d-none');
                $('.date-range').addClass('d-none');
                $('.end-date').addClass('d-none');
            } else if(date_timer_type == 'end_date_only') {
                $('.start-date').addClass('d-none');
                $('.date-range').addClass('d-none');
                $('.end-date').removeClass('d-none');
            } else {
                $('.start-date').addClass('d-none');
                $('.date-range').removeClass('d-none');
                $('.end-date').addClass('d-none');
            }
        }

        $('#date_timer_type').on('change', function() {
            enable_date_ranger(this.value);
        });

    </script>
@endsection