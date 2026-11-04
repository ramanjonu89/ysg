<div class="row sm-no-gutters gutters-5">
    @foreach ($products as $key => $product)
        @include('frontend.inc.product_list.item', ['product' => $product])
    @endforeach
</div>
