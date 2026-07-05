@php($photo = $product->cardPhoto())
<article class="product-card reveal">
    <div class="pcard-media">
        <picture>
            @if ($photo['webp'])
                <source srcset="{{ $photo['webp'] }}" type="image/webp">
            @endif
            <img src="{{ $photo['fallback'] }}"
                 alt="{{ $product->name }} — подарунковий банний набір у дерев’яному боксі"
                 loading="lazy" decoding="async">
        </picture>
    </div>

    <p class="pcard-subtitle">{{ $product->tagline }}</p>

    <p class="pcard-price">{{ $product->price }} грн</p>

    <button type="button"
            class="btn-order pcard-cta"
            data-product-id="{{ $product->id }}"
            data-product-name="{{ $product->name }}">
        Замовити
    </button>
</article>
