<div id="top-10" class="tab-pane fade">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Top 10 Information') }}</h3>
        </div>
        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('top_10_settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3" for="url">{{ __('Top Categories (Max 10)') }}</label>
                    <div class="col-sm-9">
                        <select class="form-control demo-select2-max-10" name="top_categories[]" multiple>
                            @foreach (\App\Models\Category::all() as $key => $category)
                                <option value="{{ $category->id }}" @if($category->top == 1) selected @endif>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3" for="url">{{ __('Top Brands (Max 10)') }}</label>
                    <div class="col-sm-9">
                        <select class="form-control demo-select2-max-10" name="top_brands[]" multiple>
                            @foreach (\App\Brand::all() as $key => $brand)

                                <option value="{{ $brand->id }}" @if($brand->top == 1) selected @endif>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
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
