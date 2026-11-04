<section class="mb-4">
    <div class="container">
        <form id="search-form" action="{{ route('search') }}" method="GET">
            <div class="row align-items-baseline">
                <div class="col">
                    <span>{{ __('New Arrival Products') }}</span>
                </div>
            </div>
            <div class="row align-items-baseline">
                <div class="col-4 d-flex justify-content-between align-items-end ">
                    <div class="sort-by-box flex-grow-1">
                        <div class="form-group">
                            <label>Search</label>
                            <div class="search-widget">
                                <input class="form-control input-lg" type="text" name="q" placeholder="Search products">
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

                <div class="col-2">
                    <div class="row no-gutters">
                        <div class="col-12">
                            <div class="sort-by-box px-1">
                                <div class="form-group">
                                    <label>{{ __('Filter by category') }}</label>
                                    <select class="form-control sortSelect"
                                            data-minimum-results-for-search="Infinity" name="category"
                                            onchange="filter()">
                                        <option value="">{{ __('All Categories') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->slug }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-2">
                    <div class="row no-gutters">
                        <div class="col-12">
                            <div class="sort-by-box px-1">
                                <div class="form-group">
                                    <label>{{ __('Filter by brands') }}</label>
                                    <select class="form-control sortSelect"
                                            data-minimum-results-for-search="Infinity" name="brand_ids[]"
                                            onchange="filter()">
                                        <option value="">{{ __('All Brands') }}</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-2">
                    <div class="row no-gutters">
                        <div class="col-12">
                            <div class="sort-by-box px-1">
                                <div class="form-group">
                                    <label>{{ __('Sort by') }}</label>
                                    <select class="form-control sortSelect"
                                            data-minimum-results-for-search="Infinity" name="sort_by"
                                            onchange="filter()">
                                        <option value="1">{{ __('Newest') }}</option>
                                        <option value="2">{{ __('Oldest') }}</option>
                                        <option value="3">{{ __('Price low to high') }}</option>
                                        <option value="4">{{ __('Price high to low') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-2">
                    <div class="row no-gutters">
                        <div class="col-12">
                            <div class="sort-by-box px-1">
                                <div class="form-group">
                                    <label>{{ __('Display') }}</label>
                                    <select class="form-control sortSelect"
                                            data-minimum-results-for-search="Infinity" name="size"
                                            onchange="filter()">
                                        <option value="20">{{ __('20') }}</option>
                                        <option value="30">{{ __('30') }}</option>
                                        <option value="40">{{ __('40') }}</option>
                                        <option value="50">{{ __('50') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@section('script')
    <script type="text/javascript">
        function filter() {
            $('#search-form').submit();
        }
    </script>
@endsection