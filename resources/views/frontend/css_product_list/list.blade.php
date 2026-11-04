<!-- <hr class=""> -->
<div class="products-box-bar p-3 bg-white">
    <div class="row sm-no-gutters gutters-5">
        @foreach ($products as $key => $product)
            @include('frontend.css_product_list.item')
        @endforeach
    </div>
</div>
<div class="products-pagination bg-white p-3">
    <nav aria-label="Center aligned pagination">
        <ul class="pagination justify-content-center">
            {{ $products->links() }}
        </ul>
    </nav>
</div>
<!-- </div> -->
