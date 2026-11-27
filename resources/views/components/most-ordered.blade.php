<div id="mas-pedidos" data-product-rail data-catalog-url="{{ route('catalog.index') }}">
    <span class="sr-only">Los más pedidos</span>
    @foreach ($railProducts as $product)
        <span data-most-ordered-product="{{ $product['slug'] }}" class="hidden"></span>
        <a href="{{ $product['url'] }}" class="sr-only">{{ $product['name'] }}</a>
    @endforeach
    @foreach ($categories as $category)
        <a href="{{ route('catalog.categories.show', ['category' => $category->slug]) }}" class="sr-only">{{ $category->name }}</a>
    @endforeach
    <script type="application/json" data-product-rail-items>@json($railProducts)</script>
</div>
