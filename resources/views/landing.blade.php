<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Позначаємо, що JS доступний: анімації появи вмикаються лише тоді --}}
    <script>document.documentElement.classList.add('js')</script>

    <title>Банні набори IDI_V_BANYU__ — подарунковий набір для лазні в дерев’яній скриньці</title>
    <meta name="description" content="Готові подарункові банні набори для чоловіків і жінок: шапка, кілт, віник, аромаолії в дерев’яній скриньці з гравіюванням. Доставка по Україні 1–3 дні. Не знаєш що подарувати? Вуаля!">
    <meta name="keywords" content="банний набір, набір для лазні, набір для сауни, подарунковий банний набір, подарунок чоловіку, подарунок жінці, банний набір купити, іди в баню">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="IDI_V_BANYU__">
    <meta property="og:title" content="IDI_V_BANYU__ — банні набори в дерев’яній скриньці">
    <meta property="og:description" content="Не знаєш що подарувати? Вуаля! Готовий банний набір з гравіюванням — турбота, увага, повага. Доставка по Україні 1–3 дні.">
    <meta property="og:image" content="{{ asset('images/og.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:alt" content="Не знаєш що подарувати? Вуаля! Банний набір IDI_V_BANYU__">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:locale" content="uk_UA">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="IDI_V_BANYU__ — банні набори в дерев’яній скриньці">
    <meta name="twitter:description" content="Не знаєш що подарувати? Вуаля! Готовий банний набір з гравіюванням.">
    <meta name="twitter:image" content="{{ asset('images/og.jpg') }}">

    @if (config('services.google.site_verification'))
        <meta name="google-site-verification" content="{{ config('services.google.site_verification') }}">
    @endif

    <meta name="theme-color" content="#060608">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="canonical" href="{{ url('/') }}">

    <link rel="preload" href="{{ asset('fonts/inter-900italic.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/manrope-800-cyrillic.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/rubik-v31-cyrillic_latin-regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ filemtime(public_path('css/landing.css')) }}">

    <script type="application/ld+json">{!! $siteJsonLd !!}</script>
    <script type="application/ld+json">{!! $productsJsonLd !!}</script>
    <script type="application/ld+json">{!! $faqJsonLd !!}</script>
</head>
<body>

{{-- SVG-спрайт: бігунець з логотипа, використовується через <use> --}}
<svg xmlns="http://www.w3.org/2000/svg" style="display:none" aria-hidden="true">
    <symbol id="i-runner" viewBox="0 0 504 430">
        <path d="M295.705 94.5L278.705 100.5V98L279.705 93.5L280.705 89.5L281.205 85.5L281.705 82.5V80V74V68.5L281.205 65V61L280.705 57V56V51.5L281.205 47.5L281.705 44L282.705 41L283.205 39.5L284.205 38L284.705 36L286.705 33L288.705 30L292.205 26.5L294.205 24.5L297.705 22L298.705 21H299.205L299.705 20.5V20V19.5L292.705 15L288.705 11.5L287.705 9.5L287.205 9L286.705 8V7.5V6.5V5.5L288.705 2.5L290.205 1.5L292.205 0.5H295.705H296.205H296.705L300.705 2.5L303.205 4L304.705 5.5L305.705 6.5L306.205 8.5L306.705 9L307.205 10V11.5V12V13L307.705 14.5L308.705 15.5H310.205L310.705 15L314.205 14L315.705 13.5L318.205 13L321.205 12.5H325.705H330.705L336.705 13L341.205 14L346.205 16L354.705 20L361.705 24.5L367.205 29L375.705 37.5L383.205 46.5L388.705 52.5L389.205 53.5L389.705 54V54.5V55L381.205 57.5L377.205 58.5L372.705 59.5L375.205 64.5L380.205 72.5L386.705 80L387.205 81L387.705 81.5L388.205 82V82.5V83V83.5V84L383.205 90L381.705 93.5V97.5L382.205 101L383.705 106.5V110.5V112L381.205 116L377.705 119L374.205 120.5L365.705 124.5L361.205 127.5L359.705 129L356.705 132.5L354.205 136L353.705 138.5L353.205 141.5V144V145.5L353.705 147.5L354.205 150L356.705 156L358.705 159.5L362.705 164.5L368.205 169.5L376.705 175L385.705 180L397.705 186.5L409.205 190.5H416.205H418.205L421.205 189.5L424.705 187.5L428.205 184.5L434.705 177L437.205 173.5L438.205 172.5L439.205 171L440.205 169.5L441.205 167.5L441.705 166.5L442.205 166V165.5V165V164.5L442.705 164L443.205 163.5V162.5V160V159L442.205 157L440.205 154.5L439.205 152.5L438.205 150L437.705 147.5V146V143.5L438.205 142L438.705 140L439.205 138.5L440.705 135.5L443.205 132.5L445.705 130.5L448.205 129L449.705 128.5L451.205 128L454.205 127.5H459.205L461.205 128L464.205 129L466.205 130L468.205 131.5L470.205 133L471.705 135L472.705 137L473.205 138L473.705 139L474.205 140V142V148.5L473.705 153L472.705 159.5L472.205 162.5L470.705 168L469.705 171.5L466.205 180.5L462.205 191L457.205 200.5L452.205 208.5L445.205 217.5L438.705 223.5L432.205 227.5L424.705 229.5L420.705 230H417.705H415.205H413.205L410.205 229.5L408.205 229L398.705 226.5L390.205 224L380.705 221L377.705 220L376.705 219.5H375.205L374.205 219H373.705L373.205 218.5H372.705V219L373.205 221.5L374.205 225L374.705 229L375.205 233V238.5L374.705 244.5L374.205 248.5L373.705 253L372.705 256L371.205 261.5L367.705 271L367.205 271.5L366.205 273.5L365.705 275L364.705 276L364.205 277L363.705 277.5L363.205 278L363.705 279L377.705 288L396.205 302L407.705 311L412.705 316.5L416.205 322L417.705 324L419.205 326.5L420.205 329L424.205 341L433.205 362L439.205 377L439.705 378.5L440.705 380V381L441.205 382L441.705 382.5L451.705 378L462.205 374.5L465.205 373.5L468.205 373L469.705 372.5H470.705H472.205L473.705 372H475.205L476.705 372.5H478.205L479.705 373L480.705 374L481.705 374.5L482.205 376L482.705 377V378.5V379.5L481.705 382L479.205 386L474.705 391.5L468.205 398.5L454.205 410L448.705 414L447.205 415L446.705 416H445.705V416.5L446.205 417H447.705L453.205 417.5L467.205 418.5L477.205 419.5L488.705 420.5H493.205L496.705 421H498.705L502.205 422L502.705 422.5V423.5L502.205 424L498.705 425L477.205 426.5L459.705 427L445.205 428L423.705 428.5H402.705L379.205 429H361.205L334.205 429.5L310.205 429H285.705L259.205 428.5L235.705 428L213.705 427.5H201.205L180.205 426.5L167.705 426L155.705 425H153.205L150.205 424.5L149.205 424L148.205 423.5L147.205 423L146.705 422.5L147.205 421.5L150.205 421L157.705 420L172.705 419L188.205 418L201.705 417.5L213.705 417L225.705 416.5L239.705 416H246.205H252.205L263.705 415.5H284.705H309.705H342.205H374.205L401.705 416L420.205 416.5H421.205H421.705H422.705V416L417.205 405.5L409.705 394.5L402.205 384.5L398.205 380L391.705 373.5L384.705 367.5L379.205 363L373.205 358.5L368.205 355.5L361.205 351L356.205 348.5L349.705 345.5L343.205 342.5L339.705 341L336.205 339.5L334.205 338.5L330.705 338L328.205 337L322.705 336L321.705 335.5H320.205L318.705 335H315.205L313.705 334.5L311.705 334H310.205H308.705H302.705L299.705 333.5L297.705 334L293.705 334.5H291.205H288.705L281.205 336L260.705 340.5L256.705 341.5L242.705 344.5H241.705L239.705 345H237.705L236.205 345.5H233.705H228.705L225.205 345L215.205 343.5L206.205 340.5L193.205 333.5L177.205 318.5L160.205 301L152.205 293L151.705 292L151.205 291L150.205 290.5H149.705H149.205L133.205 296.5L119.205 300.5L116.705 301L114.705 301.5H113.205H111.705H110.205H108.205L107.205 301L106.205 300.5L105.205 300L104.705 299L103.705 298L103.205 297.5V296V294L103.705 291.5L105.205 289L106.705 286.5L109.205 283.5L121.705 272.5L138.705 257.5L140.205 256.5L141.205 255.5L142.205 255L143.705 254.5L144.705 253.5L145.705 253L146.705 252.5H147.705H148.705H149.705L151.205 253L152.205 253.5L153.705 254L154.705 254.5L167.205 265.5L178.705 273.5L188.705 279L196.205 282L200.705 283L204.705 283.5L210.205 284H217.205L220.705 283.5L224.205 282.5L227.205 282L229.205 281L231.205 280L236.205 277L240.705 272L244.205 267L247.205 260.5L257.705 270.5L263.705 273L274.705 274.5L276.205 275L277.205 274.5L278.205 273.5L278.705 263.5L279.205 258.5L284.205 262.5L290.705 266L302.705 270L305.205 271L306.705 271.5H308.205L309.205 272L309.705 271.5L310.205 271L310.705 270.5L311.705 270V269.5L314.705 254L322.705 256L330.705 255.5H332.705L335.705 254.5L338.205 254H339.705H340.705L342.205 253.5L343.205 253V252V251L340.205 245.5L332.205 233L325.705 230L332.205 215.5L332.705 215L333.205 214.5V214V213L332.205 212L323.705 208.5L318.205 207.5L311.705 207L320.705 194V193.5L321.205 192.5L320.705 192L317.205 191L308.205 188L305.205 187.5H304.205H299.705H296.205L293.705 188H290.705H289.205H288.705V187.5V187L289.205 186.5V186V185.5V185V184.5L289.705 184V183L290.205 182.5V181.5V180.5L290.705 179V177.5V176.5L291.205 175.5V175L290.705 174L290.205 173.5L289.205 173L287.705 172.5H287.205L286.205 172L285.205 171.5H284.205H283.705L283.205 171H281.705H281.205L280.705 170.5H279.705H278.705H277.705H275.205H274.205H272.705L271.705 171H271.205H270.705L269.705 171.5H269.205L267.705 172L266.705 172.5L265.705 173L264.705 173.5H264.205H263.205V162.5V161.5V161L262.705 160.5L262.205 160H256.705H252.705H251.705H250.705H249.705L248.205 160.5H247.205H246.705H245.205H243.705H242.705L241.705 161H241.205L240.705 162H243.205H244.205V162.5L243.705 163L242.205 163.5H240.205L238.705 164L235.205 165L231.205 167.5L225.705 172.5L222.705 176.5L220.205 181L219.205 183.5L218.705 185L218.205 188V195.5L218.705 196.5L232.705 203L234.205 203.5L235.705 204.5L236.705 205.5L237.705 206.5L238.205 207L238.705 208V209V210L238.205 210.5L237.705 211L237.205 211.5L236.205 212H223.205V212.5L227.705 221L230.705 226.5L232.205 229.5L233.205 231.5L233.705 234L232.705 236.5L231.205 238L228.705 239.5L226.705 240.5H223.705L219.705 239.5L214.205 237L210.205 233.5L204.705 228L199.205 220L195.705 213L192.205 202.5L190.205 195L188.705 185L188.205 177.5V169.5L188.705 162L190.705 154.5L192.705 146.5L195.705 140.5L199.205 135L204.705 129.5L208.705 126.5L214.705 124L221.205 122L227.205 121L232.205 120.5L242.705 120H248.705L260.705 121.5L270.705 123.5L284.205 127.5L301.205 134L307.205 136.5L311.705 138L311.205 133L310.705 129.5L309.705 125.5L307.205 119L301.705 107L295.705 94.5Z" fill="currentColor"/>
        <path d="M133.205 156L127.705 148V147.5L128.705 146.5H133.705L136.205 146H139.205H142.705L146.705 146.5L151.705 147.5L156.205 149.5L160.705 153L163.205 157L165.205 161V163L164.205 164.5L162.205 165L157.205 166L152.705 167L143.705 166L137.705 163L133.205 156Z" fill="currentColor"/>
        <path d="M104.705 244L110.205 232.5L108.205 232H106.205H103.705C103.305 232 101.872 231.667 101.205 231.5H98.2051L95.2051 231H91.7051H89.7051H88.2051L86.2051 231.5L84.7051 232L81.7051 234.5L79.2051 236.5L77.2051 238.5L76.2051 241C75.7051 241.667 74.6051 243.1 74.2051 243.5C73.8051 243.9 73.3717 244.667 73.2051 245L72.7051 246.5L72.2051 248.5L71.2051 249.5V251L71.7051 252L72.2051 253L81.7051 253.5L86.7051 254L94.7051 253.5L98.2051 252.5L100.705 250L104.705 244Z" fill="currentColor"/>
        <path d="M35.7051 206.5H32.2051H27.7051L24.7051 205.5L21.7051 204L18.2051 201.5L14.2051 198L9.20508 191L6.70508 187.5L3.70508 185.5L2.20508 184.5L1.20508 184L0.705078 183L5.20508 181.5L8.70508 180.5L15.2051 179H22.2051L27.7051 180L31.7051 181.5L36.7051 185L40.7051 188.5L44.7051 194L46.7051 199L47.2051 200.5V202.5L46.7051 203L45.2051 204L43.7051 204.5L40.2051 205.5L35.7051 206.5Z" fill="currentColor"/>
    </symbol>
</svg>

<header class="site-header" id="site-header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="IDI_V_BANYU__ — на головну">
            <svg class="brand-mark" aria-hidden="true"><use href="#i-runner"/></svg>
            <span class="brand-name t-display">IDI_V_BANYU__</span>
        </a>

        <a class="support-pill" href="tel:+{{ $supportPhone }}">
            <span class="support-label">Підтримка</span>
            <span class="support-phone">{{ $supportPhone }}</span>
        </a>
    </div>
</header>

<main>
    {{-- Єдиний h1 сторінки: логотип у шапці — картинка, тому h1 прихований візуально --}}
    <h1 class="visually-hidden">Подарункові банні набори IDI_V_BANYU__ — дерев’яна скринька з гравіюванням для чоловіків і жінок</h1>

    {{-- Банери --}}
    <section class="banners" aria-label="Акційні банери">
        <div class="container">
            <div class="banners-grid">
                <figure class="banner-card reveal">
                    @if ($banners[0])
                        <img src="{{ asset('storage/'.$banners[0]) }}"
                             alt="Не знаєш що подарувати? Вуаля! Банний набір у дерев’яній скриньці"
                             width="820" height="380" fetchpriority="high" decoding="async">
                    @else
                        <picture>
                            <source srcset="{{ asset('images/banner-gift.webp') }}" type="image/webp">
                            <img src="{{ asset('images/banner-gift.png') }}"
                                 alt="Не знаєш що подарувати? Вуаля! Банний набір у дерев’яній скриньці"
                                 width="820" height="380" fetchpriority="high" decoding="async">
                        </picture>
                    @endif
                </figure>
                <figure class="banner-card reveal" style="--reveal-delay:.08s">
                    @if ($banners[1])
                        <img src="{{ asset('storage/'.$banners[1]) }}"
                             alt="Турбота, увага, повага — банні набори з гравіюванням"
                             width="820" height="380" decoding="async">
                    @else
                        <picture>
                            <source srcset="{{ asset('images/banner-boxes.webp') }}" type="image/webp">
                            <img src="{{ asset('images/banner-boxes.png') }}"
                                 alt="Турбота, увага, повага — банні набори з гравіюванням"
                                 width="820" height="380" decoding="async">
                        </picture>
                    @endif
                </figure>
            </div>
        </div>
    </section>

    {{-- Чоловічі набори --}}
    <section class="catalog" id="men">
        <div class="container">
            <h2 class="section-title reveal">
                <svg class="section-tri" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                    <path d="M6.5 10.5 H41.5 L24 39.5 Z" stroke="currentColor" stroke-width="5" stroke-linejoin="round"/>
                </svg>
                <span class="t-display">Чоловічій набір</span>
            </h2>

            <div class="products-grid">
                @foreach ($maleProducts as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>

    {{-- Жіночі набори --}}
    <section class="catalog" id="women">
        <div class="container">
            <h2 class="section-title reveal">
                <svg class="section-tri" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                    <path d="M24 8.5 L41.5 37.5 H6.5 Z" stroke="currentColor" stroke-width="5" stroke-linejoin="round"/>
                </svg>
                <span class="t-display">Жіночій набір</span>
            </h2>

            <div class="products-grid">
                @foreach ($femaleProducts as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>

    {{-- Запитання та відповіді --}}
    <section class="catalog faq" id="faq">
        <div class="container">
            <h2 class="section-title reveal">
                <svg class="section-tri" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                    <path d="M24 5.5 30.18 18.02 44 20.04 34 29.78 36.36 43.54 24 37.04 11.64 43.54 14 29.78 4 20.04 17.82 18.02 24 5.5Z" stroke="currentColor" stroke-width="4.5" stroke-linejoin="round"/>
                </svg>
                <span class="t-display">Запитання та відповіді</span>
            </h2>

            <div class="faq-panel reveal">
                @foreach ($faq as $i => $item)
                    <div class="faq-item">
                        <button type="button"
                                class="faq-question"
                                id="faq-q-{{ $i }}"
                                aria-expanded="false"
                                aria-controls="faq-a-{{ $i }}">
                            <span class="faq-q-text">{{ $item['q'] }}</span>
                            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m6 9.5 6 6 6-6" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer" id="faq-a-{{ $i }}" role="region" aria-labelledby="faq-q-{{ $i }}">
                            <div class="faq-answer-inner">
                                <div class="faq-answer-content">
                                    @foreach ($item['a'] as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Не знаєш, що подарувати? — текст у скрол-боксі (як на Beton) --}}
    <section class="catalog about" id="about">
        <div class="container">
            <h2 class="section-title reveal">
                <svg class="section-tri" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                    <circle cx="24" cy="24" r="17.5" stroke="currentColor" stroke-width="5"/>
                </svg>
                <span class="t-display">Не знаєш, що подарувати?</span>
            </h2>

            <div class="about-scroll reveal" tabindex="0" role="region" aria-label="Про подарунковий банний набір">
                @foreach ($aboutParagraphs as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    </section>

</main>

{{-- Підвал за макетом: соцмережі зліва, копірайт справа — одна смуга --}}
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-socials">
            <p class="socials-label">Ми в соціальних мережах:</p>
            <div class="socials-row">
                @foreach ($socials as $social)
                    <a class="social-link"
                       href="{{ $social['url'] }}"
                       target="_blank"
                       rel="noopener"
                       aria-label="{{ $social['name'] }}">
                        @switch($social['icon'])
                            @case('instagram')
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3" y="3" width="18" height="18" rx="5.2" stroke="currentColor" stroke-width="1.9"/>
                                    <circle cx="12" cy="12" r="4.1" stroke="currentColor" stroke-width="1.9"/>
                                    <circle cx="17.35" cy="6.65" r="1.35" fill="currentColor"/>
                                </svg>
                                @break
                            @case('tiktok')
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M16.6 5.82A4.28 4.28 0 0 1 15.54 3h-3.09v12.4a2.59 2.59 0 1 1-2.59-2.59c.27 0 .53.04.78.12V9.77a5.76 5.76 0 0 0-.78-.05 5.66 5.66 0 1 0 5.66 5.66V9.01a7.35 7.35 0 0 0 4.3 1.38V7.3a4.28 4.28 0 0 1-3.22-1.48Z" fill="currentColor"/>
                                </svg>
                                @break
                            @case('telegram')
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="m21.62 4.52-3.06 14.44c-.23 1.02-.83 1.27-1.69.79l-4.66-3.44-2.25 2.17c-.25.25-.46.46-.94.46l.33-4.75 8.65-7.82c.38-.33-.08-.52-.58-.19L6.72 12.9l-4.6-1.44c-1-.31-1.02-1 .21-1.48l17.96-6.92c.83-.31 1.56.19 1.33 1.46Z" fill="currentColor"/>
                                </svg>
                                @break
                        @endswitch
                    </a>
                @endforeach
            </div>
        </div>

        <p class="copyright">{{ date('Y') }} IDI_V_BANYU__ · Всі права захищені</p>
    </div>
</footer>

{{-- Cookie-повідомлення: ховається назавжди після «Зрозуміло» --}}
<div class="cookie-bar" id="cookie-bar" role="region" aria-label="Повідомлення про використання cookie" hidden>
    <div class="container cookie-inner">
        <p class="cookie-text">Цей сайт використовує файли <span class="cookie-hl">cookie</span></p>
        <button type="button" class="cookie-accept" id="cookie-accept">Зрозуміло</button>
    </div>
</div>

{{-- Модалка замовлення --}}
<div class="modal" id="order-modal" hidden>
    <div class="modal-backdrop" data-close></div>

    <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <button type="button" class="modal-close" data-close aria-label="Закрити вікно">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
            </svg>
        </button>

        <div class="modal-view" id="modal-form-view">
            <svg class="modal-logo" aria-hidden="true"><use href="#i-runner"/></svg>

            {{-- Заголовок і набір лишаються для скрін-рідерів, візуально їх немає (за макетом) --}}
            <h3 class="modal-title t-display visually-hidden" id="modal-title">Замовити набір</h3>
            <p class="visually-hidden">Набір: <b id="modal-product-name">—</b></p>

            <form id="order-form" action="{{ route('order.store') }}" method="post" novalidate>
                <input type="hidden" name="product_id" id="field-product-id">
                {{-- Honeypot: людина цього поля не бачить --}}
                <input class="hp-field" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">

                <div class="form-group">
                    <label class="visually-hidden" for="field-name">Ваше ім’я</label>
                    <input type="text" id="field-name" name="name" placeholder="Напишіть ваше ім’я" autocomplete="name" required>
                    <p class="field-error" data-error="name"></p>
                </div>

                <div class="form-group">
                    <label class="visually-hidden" for="field-phone">Телефон</label>
                    <input type="tel" id="field-phone" name="phone" placeholder="Введіть номер телефону" inputmode="tel" autocomplete="tel" required>
                    <p class="field-error" data-error="phone"></p>
                </div>

                <p class="field-error field-error--global" data-error="global" role="alert" aria-live="assertive"></p>

                <button type="submit" class="btn btn--violet btn--block" id="order-submit">
                    <span class="t-display">Відправити</span>
                </button>
            </form>
        </div>

        <div class="modal-view modal-success" id="modal-success-view" role="status" aria-live="polite" hidden>
            <svg class="success-bolt" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                <path d="M36 4 14 36h12l-4 24 22-32H32l4-24Z" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
            <h3 class="modal-title t-display">Заявку прийнято!</h3>
            <p class="success-text" id="success-message">Дякуємо! Ми з вами зв’яжемося найближчим часом.</p>
            <button type="button" class="btn btn--lime btn--block" data-close>
                <span class="t-display">Супер</span>
            </button>
            {{-- Смужка-таймер: вікно закриється саме через кілька секунд --}}
            <div class="success-progress" aria-hidden="true"></div>
        </div>
    </div>
</div>

<script src="{{ asset('js/landing.js') }}?v={{ filemtime(public_path('js/landing.js')) }}" defer></script>
</body>
</html>
