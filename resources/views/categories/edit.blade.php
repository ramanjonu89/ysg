@extends('layouts.app')
@section('content')
<div class="col-lg-6 col-lg-offset-3">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Category Information') }}</h3>
        </div>
        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            <input name="_method" type="hidden" value="PATCH">
        	@csrf
            <div class="panel-body">
                <div id="tab-form">
                    <ul class="nav nav-pills mb-5 tab-language">
                        @if (isset($systemLanguages) && count($systemLanguages) > 0)
                            @foreach ($systemLanguages as $key => $language)
                                <li class="{{ $key == 0 ? 'active' : '' }}"><a href="#{{ $language->id }}"
                                    data-toggle="tab">{{ $language->name }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                    <div class="tab-content clearfix">
                        @if (isset($systemLanguages) && count($systemLanguages) > 0)
                            @foreach ($systemLanguages as $key => $language)
                                <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ $language->id }}">
                                    @include('categories.tabs.form')
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="form-group" style="display:none">
                        <label class="col-sm-2 control-label" for="name">{{ __('Type') }}</label>
                        <div class="col-sm-10">
                            <select name="digital" required class="form-control demo-select2-placeholder">
                                <option value="0" @if ($category->digital == '0') selected @endif>{{ __('Physical') }}</option>
                                <option value="1" @if ($category->digital == '1') selected @endif>{{ __('Digital') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="banner">{{ __('Banner') }} <small>(200x300)</small></label>
                        <div class="col-sm-10">
                            <input type="file" id="banner" name="banner" class="form-control" value="{{ $category->banner }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="icon">{{ __('Icon') }} <small>(32x32)</small></label>
                        <div class="col-sm-10">
                            <input type="file" id="icon" name="icon" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="name">{{ __('Slug') }}</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="{{ __('Slug') }}" id="slug" name="slug" value="{{ $category->slug }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="text-left col-sm-12 control-label pt-0 strong-600" style="text-decoration: underline" for="name">{{ __('Age Restricted') }}</label>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label pt-0" for="name">{{ __('18 Years & Above') }}</label>
                        <div class="col-sm-10">
                            <label class="switch">
                                <input type="checkbox" id="checkbox_18_old" name="is_age_restricted" value="18"
                                    @if($category->is_age_restricted == 18) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label pt-0" for="name">{{ __('21 Years & Above') }}</label>
                        <div class="col-sm-10">
                            <label class="switch">
                                <input type="checkbox" id="checkbox_21_old" name="is_age_restricted" value="21"
                                    @if($category->is_age_restricted == 21) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="text-left col-sm-12 control-label pt-0 strong-600" style="text-decoration: underline" for="name">{{ __('Review Categories') }}</label>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label pt-0"></label>
                        <div class="col-sm-10">
                            @if(isset($reviewCategories) && count($reviewCategories) > 0)
                                @php
                                    $selectedReviewCategoryIds = $category->reviewCategories->pluck('id')->toArray();
                                @endphp
                                @foreach($reviewCategories as $reviewCategory)
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" 
                                                   name="review_categories[]" 
                                                   value="{{ $reviewCategory->id }}"
                                                   @if(in_array($reviewCategory->id, $selectedReviewCategoryIds)) checked @endif>
                                            {{ $reviewCategory->title }}
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">{{ __('No review categories available') }}</p>
                            @endif
                        </div>
                    </div>
                    @if (\App\BusinessSetting::where('type', 'category_wise_commission')->first()->value == 1)
                        <div class="form-group" style="display:none">
                            <label class="col-sm-2 control-label" for="name">{{ __('Commission Rate') }}</label>
                            <div class="col-sm-8">
                                <input type="number" min="0" step="0.01" id="commision_rate" name="commision_rate" value="{{ $category->commision_rate }}" class="form-control">
                            </div>
                            <div class="col-lg-2">
                                <option class="form-control">%</option>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="panel-footer text-right">
                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
            </div>
        </form>
        <!--===================================================-->
        <!--End Horizontal Form-->
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function(){
            $('#checkbox_18_old').change(function(){
                if(this.checked) {
                    $('#checkbox_21_old').prop('checked', false);
                }
            });

            $('#checkbox_21_old').change(function(){
                if(this.checked) {
                    $('#checkbox_18_old').prop('checked', false);
                }
            });
        });
    </script>
@endsection

