<article class="product-card reveal">
    <div class="pcard-media">
        @if ($product->photo)
            <img src="{{ asset('storage/'.$product->photo) }}"
                 alt="{{ $product->name }} — банний набір у дерев’яній скриньці"
                 width="773" height="678" loading="lazy" decoding="async">
        @else
            <picture>
                <source srcset="{{ asset($product->image.'.webp') }}" type="image/webp">
                <img src="{{ asset($product->image.'.jpg') }}"
                     alt="{{ $product->name }} — банний набір у дерев’яній скриньці"
                     width="773" height="678" loading="lazy" decoding="async">
            </picture>
        @endif

        @if ($product->badge)
            <span class="pcard-badge">{{ $product->badge }}</span>
        @endif

        <button type="button" class="pcard-fav" data-fav="{{ $product->id }}"
                aria-label="Додати в обране" aria-pressed="false">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path class="fav-heart" d="M12 20.2S4.2 15.6 2.9 10.8C1.9 7.2 4.5 4.5 7.3 4.5c1.9 0 3.6 1.1 4.7 2.7 1.1-1.6 2.8-2.7 4.7-2.7 2.8 0 5.4 2.7 4.4 6.3C19.8 15.6 12 20.2 12 20.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            </svg>
        </button>

        <button type="button" class="pcard-arrow pcard-arrow--prev" aria-label="Попередній варіант кольору">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="m14.5 6-6 6 6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <button type="button" class="pcard-arrow pcard-arrow--next" aria-label="Наступний варіант кольору">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="m9.5 6 6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <div class="pcard-swatches" role="group" aria-label="Колір боксу">
            <button type="button" class="pcard-swatch pcard-swatch--dark is-active"
                    data-color="темний" aria-label="Темний бокс" aria-pressed="true"></button>
            <button type="button" class="pcard-swatch pcard-swatch--light"
                    data-color="світлий" aria-label="Світлий бокс" aria-pressed="false"></button>
        </div>
    </div>

    <div class="pcard-body">
        <h3 class="pcard-name">{{ $product->name }}</h3>
        <p class="pcard-subtitle">{{ $product->tagline }}</p>

        <div class="pcard-meta">
            <div class="pcard-price">
                <span class="pcard-price-now">{{ number_format($product->price, 0, ',', ' ') }} <small>грн</small></span>
                @if ($product->old_price)
                    <s class="pcard-price-old">{{ number_format($product->old_price, 0, ',', ' ') }} грн</s>
                @endif
            </div>

            <div class="pcard-box-info">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3 4 7v10l8 4 8-4V7l-8-4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="m4 7 8 4 8-4M12 11v10" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                </svg>
                <span>Дерев’яний бокс<br>2 кольори</span>
            </div>
        </div>

        <ul class="pcard-features">
            @foreach ($cardFeatures as $feature)
                <li @class(['is-lime' => $feature['lime'] ?? false])>
                    @switch($feature['icon'])
                        @case('leaf')
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 19C5 9.5 11.5 4.5 20 4.5c0 8.5-4.5 14.5-15 14.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M5 19c3.2-5.8 6.8-8.8 11-10.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('gift')
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="4" y="8.5" width="16" height="12" rx="1.8" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M4 13h16M12 8.5v12" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M12 8.5c-3.8 0-5-4.6-1.6-4.9 2.2-.2 1.6 4.9 1.6 4.9Zm0 0c3.8 0 5-4.6 1.6-4.9-2.2-.2-1.6 4.9-1.6 4.9Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                            @break
                        @case('spa')
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 21V9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M12 13c-4.5 0-7-2.6-7-6.5C9 6.5 12 8.5 12 13Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M12 10.5c.6-3.8 3-5.8 7-5.5-.3 3.6-2.6 5.8-7 5.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                            @break
                        @case('heart')
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 20.2S4.2 15.6 2.9 10.8C1.9 7.2 4.5 4.5 7.3 4.5c1.9 0 3.6 1.1 4.7 2.7 1.1-1.6 2.8-2.7 4.7-2.7 2.8 0 5.4 2.7 4.4 6.3C19.8 15.6 12 20.2 12 20.2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                            @break
                    @endswitch
                    <span>{{ $feature['label'] }}</span>
                </li>
            @endforeach
        </ul>

        <button type="button"
                class="btn-order pcard-cta"
                data-product-id="{{ $product->id }}"
                data-product-name="{{ $product->name }}">
            <span class="t-display">Замовити</span>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 12h15M13.5 5.5 20 12l-6.5 6.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <p class="pcard-note">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke="currentColor" stroke-width="1.7"/>
                <path d="M8.2 10.5V8a3.8 3.8 0 0 1 7.6 0v2.5" stroke="currentColor" stroke-width="1.7"/>
            </svg>
            Безпечна оплата
            <span class="pcard-note-sep">|</span>
            Швидка доставка по Україні
        </p>
    </div>
</article>
