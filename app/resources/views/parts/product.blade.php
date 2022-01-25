<div class="product card">
    <div class="card-header product-name">{{ $product->name }}</div>
    <div class="card-body">
        <div class="product-img" style="background-image: url('{{ $product->img }}')"></div>
        <span>{{ $product->desc }}</span><br>
        <span><strong>{{ $product->price }} UAH</strong></span><br>

            <button type="button"
                    @if($product->availability == 0 || $product->qty == 0)
                        disabled
                    @endif
                    class="btn btn-primary">{{ __('Buy') }}
            </button>

    </div>
</div>
