<div class="col-xl-8">
    <div class="row align-items-center">
        <div class="col-sm-1 ml-2 mb-1 p-0 d-flex d-xl-block justify-content-between align-items-end">
            <span>{{ __('Sort by') }}</span>
            <div class="d-xl-none">
                <button type="button" class="btn p-1 btn-sm" id="side-filter">
                    <i class="la la-filter la-2x"></i>
                </button>
            </div>
        </div>
        <div class="col-sm-2 mr-2 mb-1 p-0 @isset($sort_by) @if ($sort_by === '2') bg-orange @endif @endisset bg-white text-center rounded">
            <button name="sort_by" value="2" class="sort-by-condition" onchange="filter()"> {{ __('Latest') }}</button>
        </div>
        <div class="col-sm-2 mr-2 mb-1 p-0 @isset($sort_by) @if ($sort_by === '1') bg-orange @endif @endisset bg-white text-center rounded">
            <button  name="sort_by" value="1" class="sort-by-condition" onchange="filter()">{{ __('Top Sales') }}</button>
        </div>
        <div class="col-sm-4 mr-2 mb-1 p-0">
            <div class="form-group mb-0 bg-white choose-sort-by position-relative">
                <div class="form-control bg-white d-flex align-items-baseline justify-content-between">
                    <span class="mb-0  @if ($sort_by === '3' || $sort_by === '4') c-sort-by @endif">
                        @if ($sort_by === '3')
                        {{ __('Price: Low to high') }}
                        @elseif($sort_by === '4')
                        {{ __('Price: High to low') }}
                        @else
                        {{ __('Price') }}
                        @endif
                    </span>
                    <i class="fa fa-angle-down"></i>
                </div>
                <div class="form-group mb-0 bg-white choose-sort-by-items position-absolute">
                    <button name="sort_by" value="3" 
                            class="sort-by-condition-sub d-flex align-items-baseline justify-content-between bg-white
                            @if ($sort_by === '3') c-sort-by @endif"
                            onchange="filter()">{{ __('Price: Low to high') }}
                            <i class="mb-0 
                                @if ($sort_by === '3') d-block @else d-none @endif fa fa-check">
                            </i>
                    </button>
                    <button name="sort_by" value="4" 
                        class="sort-by-condition-sub d-flex align-items-baseline justify-content-between bg-white
                        @if ($sort_by === '4') c-sort-by @endif" 
                        onchange="filter()">{{ __('Price: High to low') }}
                        <i class="@if ($sort_by === '4') d-block @else  d-none @endif fa fa-check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-xl-4">
    <div class="row align-items-center">
        <div class="col-7">
            <div class="row justify-content-end">
                <div class="col-sm-8 p-0">
                    <div class="sort-by-box border-0">
                        <div class="form-group mb-0 border-0 " id="pageSize">
                            <select class="form-control sortSelect" data-minimum-results-for-search="Infinity" name="pageSize" onchange="filter()">
                                @foreach ($pageSizeOptions as $option)
                                <option value="{{ $option }}" @if ($selectedPageSize == $option) selected @endif>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-2 pl-1">
            <div class="sort-by-box px-1 text-right">
                <span class="page-sort-by c-sort-by">{{ $products->currentPage() }}</span>/<span>{{ $products->lastPage() }}</span>
            </div>
        </div>
        <div class="col-3 pl-0">
            <ul class="pagination justify-content-end">
                <li class="page-item @if($products->currentPage() === 1) disabled @endif">
                    <a class="page-link bg-white pagination-simple" href="{{ $products->previousPageUrl() }}" aria-label="Previous">
                        <span aria-hidden="true">&lt;</span>    
                        <span class="sr-only ">Previous</span>
                    </a>
                </li>
                <li class="page-item @if($products->currentPage() === $products->lastPage()) disabled @endif">
                    <a class="page-link bg-white pagination-simple" href="{{ $products->nextPageUrl() }}" aria-label="Next">
                        <span aria-hidden="true">&gt;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>