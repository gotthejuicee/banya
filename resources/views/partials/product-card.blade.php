@php($slides = $product->cardSlides())
<article class="product-card @if (count($slides) > 1) has-multiple-slides @endif">
    <div class="pcard-media">
        <button type="button" class="pcard-arrow pcard-arrow--prev" aria-label="Попереднє фото">
            <svg viewBox="0 0 12.5 25" fill="none" aria-hidden="true">
                <path d="M10 2.5 2.5 12.5 10 22.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <div class="pcard-box">
            <div class="pcard-carousel" data-slide-count="{{ count($slides) }}">
                @foreach ($slides as $index => $slide)
                    <div class="pcard-slide @if ($index === 0) is-active @endif" data-index="{{ $index }}">
                        <picture>
                            @if ($slide['webp'])
                                <source srcset="{{ $slide['webp'] }}" type="image/webp">
                            @endif
                            <img src="{{ $slide['fallback'] }}"
                                 alt="{{ $slide['alt'] }}"
                                 loading="lazy" decoding="async">
                        </picture>
                    </div>
                @endforeach
            </div>
            <div class="pcard-ground" aria-hidden="true"></div>
        </div>

        <button type="button" class="pcard-arrow pcard-arrow--next" aria-label="Наступне фото">
            <svg viewBox="0 0 12.5 25" fill="none" aria-hidden="true">
                <path d="M2.5 2.5 10 12.5 2.5 22.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
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