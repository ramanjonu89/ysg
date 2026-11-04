@php
    $start_date = !empty($block->start_date) ? date('d/m/Y H:i', $block->start_date) : 0;
    $end_date   = !empty($block->end_date) ? date('d/m/Y H:i', $block->end_date) : 0;
@endphp
<div id="tab-1" class="tab-pane fade active in">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Content') }}</h3>
        </div>
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
                                        <li class="{{ $key == 0 ? 'active' : '' }}"><a href="#{{ $language->id }}" style="color:#fff" data-toggle="tab">{{ $language->name }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content clearfix" style="    background-color: #fff">
                        @include('admin.custom_page.block.inc.published', ['block' => $block])
                        @include('admin.custom_page.block.inc.datetimer')

                        @if (isset($systemLanguages) && count($systemLanguages) > 0)
                            @foreach ($systemLanguages as $key => $language)
                                <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ $language->id }}">

                                    @if (isset($block) && $block->ysgBlockTranslation($language->id) !== null)
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="name">{{ __('Title') }}</label>
                                        <div class="col-sm-9">
                                            <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]" class="form-control" {{ $key == 0 ? 'required' : '' }} value="{{ $block->ysgBlockTranslation($language->id)->title }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="title">{{ __('Description') }}</label>
                                        <div class="col-sm-9">
                                            <textarea placeholder="{{ __('Description') }}" id="description" name="description[]" class="form-control">{!! $block->ysgBlockTranslation($language->id)->description ?? '' !!}</textarea>
                                        </div>
                                    </div>

                                    @else

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="name">{{ __('Title') }}</label>
                                        <div class="col-sm-9">
                                            <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]" class="form-control" {{ $key == 0 ? 'required' : '' }} value="{{ $block->title }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="title">{{ __('Description') }}</label>
                                        <div class="col-sm-9">
                                            <textarea placeholder="{{ __('Description') }}" id="description" name="description[]" class="form-control">{!! $block->description ?? '' !!}</textarea>
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
                                    @foreach ($blockTypes as $value => $title)
                                        <option value="{{ $value }}"
                                            {{ $block->type == $value ? 'selected' : '' }}>{{ $title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="image">{{ __('Image') }}</label>
                            <div class="col-sm-9">
                                <div id="photo">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="link">{{ __('Link') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Link') }}" id="link" name="options[link]" class="form-control" value="{{ $block->options->link ?? '' }}">
                            </div>
                        </div>
                    </div>
                    @include('admin.custom_page.block.inc.margin_options')
                    @include('admin.custom_page.block.inc.hide_on_mobile_options')

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
<style>
    .d-none {
        display: none !important;
    }
</style>
@section('script')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $("#photo").spartanMultiImagePicker({
                fieldName: 'image',
                maxCount: 1,
                rowHeight: '200px',
                groupClassName: 'col-md-4 col-sm-9 col-xs-6',
                maxFileSize: '',
                allowedExt: 'jpeg|png|jpg|gif|svg',
                dropFileLabel: "Drop Here",
                @if ($block->image != null)
                    placeholderImage: {
                    image: '{{ $block->image }}',
                    width: '100%'
                    },
                @endif
                onExtensionErr: function(index, file) {
                    console.log(index, file, 'extension err');
                    alert('Please only input png or jpg type file')
                },
                onSizeErr: function(index, file) {
                    console.log(index, file, 'file size too big');
                    alert('File size too big');
                }
            });

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
