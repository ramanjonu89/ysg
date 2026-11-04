<div class="sort-by-bar row no-gutters bg-white mb-3 px-3 pt-2">
    <div class="col-xl-7 d-flex d-xl-block justify-content-between align-items-end ">
        <div class="sort-by-box flex-grow-1">
            <div class="form-group">
                <label>{{ __('Search') }}</label>
                <div class="search-widget">
                    <input class="form-control input-lg" type="text" name="q"
                           placeholder="{{ __('Search products') }}"
                           value="{{ $requestQuery['q']??'' }}" >
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
                    <div class="form-group">
                        <label>{{ __('Sort by') }}</label>
                        <select class="form-control sortSelect"
                                data-minimum-results-for-search="Infinity" name="sort_by"
                                onchange="filter()">
                            @foreach($sortTypes as $value => $title)
                                <option value="{{ $value }}" {{ isset($requestQuery['sort_by'])?($requestQuery['sort_by']==$value)?'selected':'':'' }}>{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
