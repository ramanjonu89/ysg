<div class="col-xl-3 side-filter d-xl-block">
    <div class="filter-overlay filter-close"></div>
    <div class="filter-wrapper c-scrollbar" style="max-height: 70vh; overflow-y: scroll">
        <div class="filter-title d-flex d-xl-none justify-content-between pb-3 align-items-center">
            <h3 class="h6">Filters</h3>
            <button type="button" class="close filter-close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="bg-white sidebar-box">
            <!-- Category Filter -->
            @include('frontend.css_product_list.side_filter.category')
            <!-- END -->

            <!-- Brand Filter -->
            @include('frontend.css_product_list.side_filter.brand')
            <!-- END -->

            <!-- Seller Filter -->
            @include('frontend.css_product_list.side_filter.seller')
            <!-- END -->

            <!-- Price Filter -->
            @include('frontend.css_product_list.side_filter.price')
            <!-- END -->

            <!-- Color Filter -->
            @include('frontend.css_product_list.side_filter.color')
            <!-- END -->
        </div>

        {{-- <button type="submit" class="btn btn-styled btn-block btn-base-4">Apply filter</button> --}}
    </div>
</div>
