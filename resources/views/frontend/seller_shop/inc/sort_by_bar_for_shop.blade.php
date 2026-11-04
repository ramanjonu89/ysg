<div class="sort-by-bar row no-gutters bg-white mb-3 px-3 pt-2">
    <div class="col-xl-5 d-flex d-xl-block justify-content-between align-items-end ">
        <div class="sort-by-box flex-grow-1">
            <div class="form-group mb-2">
                <label>{{ __('Search') }}</label>
                <div class="search-widget">
                    <input class="form-control input-lg" type="text" name="q"
                           placeholder="{{ __('Search products') }}"
                           @isset($keyword) value="{{ $keyword }}" @endisset>
                    <button type="submit" class="btn-inner">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="d-xl-none ml-3 form-group">
            <button type="button" class="btn p-1 btn-sm" id="side-filter">
                <i class="la la-filter la-2x"></i>
            </button>
        </div>
    </div>
    <div class="col-xl-4 offset-xl-1">
        <div class="row no-gutters">
            <div class="col-12">
                <div class="sort-by-box px-1">
                    <div class="form-group mb-2">
                        <label>{{ __('Sort by') }}</label>
                        <select class="form-control sortSelect"
                                data-minimum-results-for-search="Infinity" name="sort_by"
                                onchange="filter()">
                            <option value=""
                                    @isset($sort_by) @if ($sort_by == '') selected @endif @endisset>{{ __('Newest') }}</option>
                            <option value="2"
                                    @isset($sort_by) @if ($sort_by == '2') selected @endif @endisset>{{ __('Oldest') }}</option>
                            <option value="3"
                                    @isset($sort_by) @if ($sort_by == '3') selected @endif @endisset>{{ __('Price low to high') }}</option>
                            <option value="4"
                                    @isset($sort_by) @if ($sort_by == '4') selected @endif @endisset>{{ __('Price high to low') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="row no-gutters">
            <div class="col-12">
                <div class="sort-by-box px-1">
                    <div class="form-group mb-2">
                        <label>{{ __('Products Per Page')}}</label>
                        <select class="form-control sortSelect"
                                data-minimum-results-for-search="Infinity" name="pageSize"
                                onchange="filter()">
                                @foreach ($pageSizeOptions as $option)
                                <option value="{{ $option }}"
                                    @isset($selectedPageSize) @if ($selectedPageSize == $option) selected @endif @endisset>{{ $option }}</option> 
                                @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>