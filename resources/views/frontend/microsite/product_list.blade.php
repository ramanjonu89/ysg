<section class="mb-4">
    <div class="container">
        <div class="row sm-no-gutters gutters-5">
            @foreach ($products as $product)
                <div class="col-xxl-2 col-xl-2 col-lg-3 col-md-4 col-6">
                    @include('frontend/microsite/product_item')
                </div>
            @endforeach
        </div>

        <div class="products-pagination p-3">
            <nav aria-label="Center aligned pagination">
                <ul class="pagination justify-content-center">
                    {{ $products->links() }}
                </ul>
            </nav>
        </div>
    </div>
</section>