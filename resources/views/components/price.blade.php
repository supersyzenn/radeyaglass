@props(['product'])
<div class="price-group">
    @if($product->discount_price !== null)<del>{{ $product->formattedPrice(true) }}</del>@endif
    <span class="price">{{ $product->formattedPrice() }}</span>
</div>
