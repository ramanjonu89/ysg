@extends('layouts.app')
@section('content')
    <div class="col-lg-6 col-lg-offset-3">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{__('Locale Settings') }}</h3>
            </div>
            <!--Horizontal Form-->
            <!--===================================================-->
            <form class="form-horizontal" action="{{ route('localesettings.update',$localeSettings->id ) }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="panel-body">
                    
                    @if(isset($languages) && count($languages) > 0)
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="css_url">{{__('Languages') }}</label>
                        <div class="col-sm-9">
                            <select name="language_ids[]" class="form-control demo-select2" data-live-search="true" id="select-languages" multiple required>
                                @foreach($languages as $language)
                                    <option value="{{$language->id}}" {{in_array($language->id, $languageIds) ? 'selected' : ''}} data-id="{{$language->id}}" data-name="{{$language->name}}">{{$language->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group box-language {{!empty($languageIds) ? '' : 'hidden'}}">
                        <label class="col-sm-3 control-label" for="css_url">{{__('Default language') }}</label>
                        <div class="col-sm-9">
                            <div class="box-info-value" style="column-count: 3;margin-top: 5px;">
                                @foreach($languages as $language)
                                    @if(in_array($language->id, $languageIds))
                                    <div class="">
                                        <input type="checkbox" {{isset($localeSettings->default_language) && $localeSettings->default_language == $language->id ? "checked" : ''}} class="checkbox-item checkbox-item-{{$language->id}}" data-id="{{$language->id}}" name="default_language" value="{{$language->id}}">
                                        <label for="info-{{$language->id}}">{{$language->name}}</label>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                <div class="panel-footer text-right">
                    <button class="btn btn-purple" type="submit">{{__('Save') }}</button>
                </div>
            </form>
            <!--===================================================-->
            <!--End Horizontal Form-->
        </div>
    </div>

    <script type="text/javascript">
        $(document).on('click', '.checkbox-item', function() {
            localStorage.setItem('languageChecked',$(this).val());
            let $box = $(this);
            let id = $(this).data('id');
            if ($box.is(":checked")) {
                $('.checkbox-item').each(function() {
                    $(this).prop("checked", false);
                })
                $box.prop("checked", true);
            } else {
                $box.prop("checked", false);
            }
        });

        $(document).on('change', '#select-languages', function(){
            let html        = '';
            let languageIds = $(this).val();
            languageIds.length > 0 ? $('.box-language').removeClass('hidden') : $('.box-language').addClass('hidden');
            $.each( languageIds, function( key, value ) {
                let text = $('#select-languages option[value='+value+']').text();
                html += createItemLanguage(value, text);
            });
            $('.box-info-value').html(html);
        })

        function createItemLanguage(value, text){
            let languageChecked = localStorage.getItem('languageChecked') ? localStorage.getItem('languageChecked') : -1;
            let checked = value == languageChecked ? 'checked': '';
            let html = '<div id="checkbox-item-'+value+'">';
                html += '<input type="checkbox" '+checked+' class="checkbox-item checkbox-item-'+value+'" data-id="'+value+'" name="default_language" value="'+value+'" style="marign-right:5px"> ';
                html += '<label for="info-7">'+text+'</label>';
                html += '</div>';
            return html;
        }
    </script>

@endsection

@section('script')

@endsection