/* IDI_V_BANYU__ — модалка замовлення, анімації, дрібна інтерактивність */
(() => {
    'use strict';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    /* Якщо body лишився залоченим після модалки/старого JS — відпускаємо скрол */
    (() => {
        const b = document.body;
        if (b && (b.style.position === 'fixed' || b.style.overflow === 'hidden')) {
            ['position', 'top', 'left', 'right', 'overflow', 'width'].forEach((p) => {
                b.style[p] = '';
            });
        }
    })();

    /* ---------- Хедер fixed: спейсер = повна висота (НІКОЛИ не стискати), floats — visual ---------- */
    (() => {
        const siteHeader = document.getElementById('site-header');
        const spacer = document.getElementById('site-header-spacer');
        if (!siteHeader) return;

        let fullHeaderH = 90;

        const applyFullSpacer = () => {
            fullHeaderH = Math.max(fullHeaderH, 90);
            document.documentElement.style.setProperty('--header-full', `${fullHeaderH}px`);
            if (spacer) {
                spacer.style.setProperty('height', `${fullHeaderH}px`, 'important');
                spacer.style.setProperty('min-height', `${fullHeaderH}px`, 'important');
            }
        };

        const measureFull = () => {
            /* Повну висоту міряємо тільки коли шапка розгорнута + після transition */
            if (!document.body.classList.contains('is-scrolled')) {
                const h = Math.ceil(siteHeader.getBoundingClientRect().height) || 90;
                /* Тільки збільшуємо — ніколи не зменшуємо (інакше mid-transition дає 40px) */
                if (h > fullHeaderH) fullHeaderH = h;
                fullHeaderH = Math.max(fullHeaderH, 90);
                applyFullSpacer();
            }
            const visualH = document.body.classList.contains('is-scrolled')
                ? Math.max(10, Math.ceil(siteHeader.getBoundingClientRect().height) || 12)
                : fullHeaderH;
            document.documentElement.style.setProperty('--header-offset', `${visualH}px`);
        };

        const pin = () => {
            const cs = window.getComputedStyle(siteHeader);
            if (cs.position !== 'fixed') {
                siteHeader.style.setProperty('position', 'fixed', 'important');
                siteHeader.style.setProperty('top', '0', 'important');
                siteHeader.style.setProperty('left', '0', 'important');
                siteHeader.style.setProperty('right', '0', 'important');
                siteHeader.style.setProperty('z-index', '1000', 'important');
            }
            measureFull();
        };

        applyFullSpacer();
        pin();

        const remount = () => {
            if (!document.body.classList.contains('is-scrolled')) {
                fullHeaderH = 90;
            }
            pin();
        };
        window.addEventListener('resize', remount, { passive: true });
        window.addEventListener('orientationchange', () => {
            setTimeout(remount, 150);
            setTimeout(remount, 400); /* iOS після повороту */
        }, { passive: true });
        /* iOS Safari: адресний рядок / safe-area */
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', () => {
                requestAnimationFrame(measureFull);
            }, { passive: true });
        }
        if (document.fonts?.ready) document.fonts.ready.then(remount);

        /* Після анімації розгортання шапки — фінальний замір */
        siteHeader.addEventListener('transitionend', (e) => {
            if (e.target !== siteHeader && !e.target.classList?.contains('header-inner')) return;
            if (!document.body.classList.contains('is-scrolled')) {
                fullHeaderH = 90;
                measureFull();
            }
        });

        window.__headerPinCheck = () => {
            const r = siteHeader.getBoundingClientRect();
            const info = {
                position: getComputedStyle(siteHeader).position,
                top: r.top,
                headerH: r.height,
                spacerH: spacer ? spacer.getBoundingClientRect().height : null,
                fullHeaderH,
                scrollY: window.scrollY,
                stuck: r.top === 0,
                isScrolled: document.body.classList.contains('is-scrolled'),
            };
            console.log('[header-pin]', info);
            return info;
        };

        window.__headerRemeasure = measureFull;
    })();

    /* ---------- Floats після скролу: бігунок зліва + Підтримка справа ---------- */
    (() => {
        const brandFloat = document.getElementById('brand-float');
        const supportFloat = document.getElementById('support-float');
        const floats = [brandFloat, supportFloat].filter(Boolean);
        if (!floats.length) return;

        const OPEN_AT = 64;
        const CLOSE_AT = 12;
        let open = false;
        let ticking = false;

        const setOpen = (next) => {
            if (next === open) return;
            open = next;

            /*
             * Спочатку клас is-scrolled (тонка смуга + CSS top для floats),
             * потім is-open (fade-in). Так floats одразу в правильному місці,
             * без «спочатку внизу → через секунду вгорі».
             */
            document.body.classList.toggle('is-scrolled', open);

            /* Синхронно: offset = тонка смуга (не чекаємо rAF/transition) */
            if (open) {
                document.documentElement.style.setProperty('--header-offset', '10px');
            }

            floats.forEach((el) => {
                el.classList.toggle('is-open', open);
                el.setAttribute('aria-hidden', open ? 'false' : 'true');
                el.tabIndex = open ? 0 : -1;
            });

            /* Після paint — уточнити заміри (спейсер не чіпаємо) */
            requestAnimationFrame(() => {
                if (typeof window.__headerRemeasure === 'function') {
                    window.__headerRemeasure();
                }
            });
        };

        const onScroll = () => {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(() => {
                const y = window.scrollY || document.documentElement.scrollTop || 0;
                if (!open && y >= OPEN_AT) setOpen(true);
                else if (open && y <= CLOSE_AT) setOpen(false);
                ticking = false;
            });
        };

        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    })();

    /* ---------- About: градієнт + підказка «Гортайте» ---------- */
    const aboutPanel = document.getElementById('about-panel');
    const aboutScroll = document.getElementById('about-scroll');

    const updateAboutScroll = () => {
        if (!aboutPanel || !aboutScroll) return;

        const { scrollTop, scrollHeight, clientHeight } = aboutScroll;
        const overflow = scrollHeight > clientHeight + 2;
        const atTop = scrollTop < 6;
        const atEnd = scrollTop + clientHeight >= scrollHeight - 6;

        aboutPanel.classList.toggle('is-overflowing', overflow);
        aboutPanel.classList.toggle('is-at-top', atTop);
        aboutPanel.classList.toggle('is-at-end', atEnd);
        aboutPanel.classList.toggle('is-scrolled', !atTop);
    };

    if (aboutScroll) {
        updateAboutScroll();
        aboutScroll.addEventListener('scroll', updateAboutScroll, { passive: true });
        window.addEventListener('resize', updateAboutScroll, { passive: true });
    }

    /* ---------- Cookie-повідомлення ---------- */
    const cookieBar = document.getElementById('cookie-bar');
    const cookieAccept = document.getElementById('cookie-accept');

    const updateCookieOffset = () => {
        const offset = cookieBar && !cookieBar.hidden ? `${cookieBar.offsetHeight}px` : '0px';
        document.documentElement.style.setProperty('--cookie-bar-offset', offset);
    };

    if (cookieBar && cookieAccept) {
        // localStorage може бути недоступний (приватний режим) — тоді
        // показуємо панель щоразу, але сторінку не ламаємо
        let cookieSeen = false;
        try { cookieSeen = localStorage.getItem('cookie-ok') === '1'; } catch (e) { /* ignore */ }

        if (!cookieSeen) cookieBar.hidden = false;
        updateCookieOffset();

        cookieAccept.addEventListener('click', () => {
            cookieBar.hidden = true;
            updateCookieOffset();
            try { localStorage.setItem('cookie-ok', '1'); } catch (e) { /* ignore */ }
        });

        window.addEventListener('resize', updateCookieOffset, { passive: true });
    }

    /* ---------- Карусель фото на картках наборів ---------- */
    const setCardSlide = (card, index) => {
        const slides = [...card.querySelectorAll('.pcard-slide')];
        if (!slides.length) return;

        const total = slides.length;
        const next = ((index % total) + total) % total;

        slides.forEach((slide, i) => {
            const on = i === next;
            slide.classList.toggle('is-active', on);
            // Підвантажити зображення активного + сусідніх слайдів
            if (on || i === (next + 1) % total || i === (next - 1 + total) % total) {
                slide.querySelectorAll('img').forEach((img) => {
                    if (img.dataset.src && !img.getAttribute('src')) {
                        img.src = img.dataset.src;
                    }
                    img.loading = 'eager';
                });
            }
        });

        card.dataset.slideIndex = String(next);

        card.querySelectorAll('.pcard-arrow').forEach((arrow) => {
            arrow.disabled = total < 2;
            arrow.setAttribute('aria-disabled', total < 2 ? 'true' : 'false');
        });
    };

    document.querySelectorAll('.product-card').forEach((card) => {
        const slides = card.querySelectorAll('.pcard-slide');
        if (!slides.length) return;

        card.dataset.slideIndex = '0';
        setCardSlide(card, 0);

        if (slides.length < 2) return;

        card.querySelectorAll('.pcard-arrow').forEach((arrow) => {
            arrow.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const current = Number(card.dataset.slideIndex ?? 0);
                const step = arrow.classList.contains('pcard-arrow--next') ? 1 : -1;
                setCardSlide(card, current + step);
            });
        });

        const carousel = card.querySelector('.pcard-carousel');
        let touchStartX = 0;

        carousel?.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0]?.clientX ?? 0;
        }, { passive: true });

        carousel?.addEventListener('touchend', (e) => {
            const delta = (e.changedTouches[0]?.clientX ?? 0) - touchStartX;
            if (Math.abs(delta) < 40) return;

            const current = Number(card.dataset.slideIndex ?? 0);
            setCardSlide(card, current + (delta < 0 ? 1 : -1));
        }, { passive: true });
    });

    /* ---------- FAQ-акордеон ---------- */
    document.querySelectorAll('.faq-question').forEach((btn) => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.faq-item');
            const wasOpen = item.classList.contains('is-open');

            // Одночасно відкрите лише одне питання
            document.querySelectorAll('.faq-item.is-open').forEach((open) => {
                open.classList.remove('is-open');
                open.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            });

            if (!wasOpen) {
                item.classList.add('is-open');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });

    /* ---------- Модалка ---------- */
    const modal = document.getElementById('order-modal');
    const modalPanel = modal?.querySelector('.modal-panel');
    const formView = document.getElementById('modal-form-view');
    const form = document.getElementById('order-form');
    const formFields = document.getElementById('order-form-fields');
    const successBlock = document.getElementById('order-success');
    const successBarFill = document.getElementById('order-success-bar-fill');
    const submitBtn = document.getElementById('order-submit');
    const productIdField = document.getElementById('field-product-id');
    const productNameEl = document.getElementById('modal-product-name');
    const submitLabel = submitBtn.querySelector('.t-display');
    const defaultSubmitText = submitLabel.textContent;
    const SUCCESS_AUTO_CLOSE_MS = 3000;

    let lastFocused = null;
    let successTimer = null;
    let keyboardTimer = null;

    const resetFormUi = () => {
        form?.reset();
        clearErrors();
        if (formFields) formFields.hidden = false;
        if (successBlock) successBlock.hidden = true;
        if (successBarFill) {
            successBarFill.style.animation = 'none';
            // force reflow so next open restarts animation
            void successBarFill.offsetWidth;
            successBarFill.style.animation = '';
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitLabel.textContent = defaultSubmitText;
        }
    };

    const clearErrors = () => {
        modal.querySelectorAll('.field-error').forEach((el) => {
            el.textContent = '';
            el.classList.remove('is-visible');
        });
        modal.querySelectorAll('.form-group.has-error').forEach((el) => el.classList.remove('has-error'));
    };

    const showError = (field, message) => {
        // Помилка поля, для якого немає місця у формі, іде в загальний блок
        const el = modal.querySelector(`[data-error="${field}"]`)
            ?? modal.querySelector('[data-error="global"]');
        if (!el) return;
        el.textContent = message;
        el.classList.add('is-visible');
        el.closest('.form-group')?.classList.add('has-error');
    };

    /* Скрол-лок, який тримає і iOS Safari (overflow: hidden там не працює) */
    let lockedScrollY = 0;
    const lockScroll = () => {
        lockedScrollY = window.scrollY;
        Object.assign(document.body.style, {
            position: 'fixed',
            top: `-${lockedScrollY}px`,
            left: '0',
            right: '0',
            overflow: 'hidden',
        });
    };
    const unlockScroll = () => {
        ['position', 'top', 'left', 'right', 'overflow'].forEach((p) => (document.body.style[p] = ''));
        window.scrollTo(0, lockedScrollY);
    };

    /* iOS: клавіатура зменшує visualViewport — піднімаємо панель і скролимо поле */
    const resetModalKeyboard = () => {
        clearTimeout(keyboardTimer);
        if (modalPanel) modalPanel.style.marginBottom = '';
    };

    const adjustModalForKeyboard = () => {
        if (modal.hidden || !modalPanel || !window.visualViewport) return;

        const vv = window.visualViewport;
        const obscured = window.innerHeight - vv.height - vv.offsetTop;
        modalPanel.style.marginBottom = obscured > 0 ? `${Math.ceil(obscured)}px` : '';
    };

    const focusFieldInModal = (el) => {
        if (!el || !modalPanel?.contains(el)) return;

        clearTimeout(keyboardTimer);
        keyboardTimer = setTimeout(() => {
            el.scrollIntoView({ block: 'center', behavior: 'smooth' });
            adjustModalForKeyboard();
        }, 320);
    };

    const openModal = (productId, productName) => {
        lastFocused = document.activeElement;
        clearTimeout(successTimer);
        successTimer = null;
        resetFormUi();
        productIdField.value = productId;
        productNameEl.textContent = productName;
        if (formView) formView.hidden = false;
        modal.hidden = false;
        document.body.classList.add('is-modal-open');
        lockScroll();
        setTimeout(() => {
            const nameField = document.getElementById('field-name');
            nameField?.focus({ preventScroll: true });
            focusFieldInModal(nameField);
        }, 60);
    };

    const closeModal = () => {
        clearTimeout(successTimer);
        successTimer = null;
        resetModalKeyboard();
        modal.hidden = true;
        document.body.classList.remove('is-modal-open');
        unlockScroll();
        resetFormUi();
        lastFocused?.focus?.();
    };

    const showInlineSuccess = () => {
        form.reset();
        clearErrors();
        if (formFields) formFields.hidden = true;
        if (successBlock) successBlock.hidden = false;
        if (successBarFill) {
            successBarFill.style.animation = 'none';
            void successBarFill.offsetWidth;
            successBarFill.style.animation = `order-success-countdown ${SUCCESS_AUTO_CLOSE_MS}ms linear forwards`;
        }
        clearTimeout(successTimer);
        successTimer = setTimeout(closeModal, SUCCESS_AUTO_CLOSE_MS);
    };

    modal.addEventListener('focusin', (e) => focusFieldInModal(e.target));

    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', adjustModalForKeyboard);
        window.visualViewport.addEventListener('scroll', adjustModalForKeyboard);
    }

    /* Focus trap: Tab не випускає фокус за межі відкритої модалки */
    modal.addEventListener('keydown', (e) => {
        if (e.key !== 'Tab') return;
        const focusables = [...modal.querySelectorAll(
            'button, input, textarea, [href]',
        )].filter((el) => !el.disabled && el.offsetParent !== null);
        if (!focusables.length) return;

        const first = focusables[0];
        const last = focusables[focusables.length - 1];

        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    });

    document.querySelectorAll('.btn-order[data-product-id]').forEach((btn) => {
        btn.addEventListener('click', () => openModal(btn.dataset.productId, btn.dataset.productName));
    });

    /* ПІДТРИМКА (хедер + float): телефон → tel:; планшет/десктоп → форма заявки.
     * Планшет рахуємо як десктоп (той самий брейкпоінт, що в CSS): інакше
     * iPad у портреті (820px) відкривав tel:, а після повороту (1180px) —
     * модалку. Плюс на iPad tel: і так лише пропонує FaceTime, а не дзвінок. */
    const TABLET_MQ = '(min-width: 700px) and (max-width: 1024px) and (min-height: 600px)';
    const isPhoneSupport = () =>
        window.matchMedia('(max-width: 1024px)').matches && !window.matchMedia(TABLET_MQ).matches;
    const onSupportClick = (e) => {
        if (isPhoneSupport()) {
            // href="tel:..." — нативний дзвінок
            return;
        }
        e.preventDefault();
        openModal('', 'Зв’язок з підтримкою');
    };
    ['support-pill', 'support-float'].forEach((id) => {
        document.getElementById(id)?.addEventListener('click', onSupportClick);
    });

    modal.querySelectorAll('[data-close]').forEach((el) => el.addEventListener('click', closeModal));

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.hidden) closeModal();
    });

    /* ---------- Відправка заявки ---------- */
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors();

        submitBtn.disabled = true;
        submitLabel.textContent = 'Хвилинку…';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productIdField.value || null,
                    name: form.elements.name.value.trim(),
                    phone: form.elements.phone.value.trim(),
                    website: form.elements.website.value, // honeypot
                }),
            });

            if (response.status === 422) {
                const data = await response.json();
                Object.entries(data.errors ?? {}).forEach(([field, messages]) => {
                    showError(field, messages[0]);
                });
                return;
            }

            if (response.status === 429) {
                showError('global', 'Забагато спроб — зачекайте хвилинку і спробуйте ще раз.');
                return;
            }

            if (response.status === 419) {
                showError('global', 'Сторінка відкрита надто довго. Оновіть її та надішліть заявку ще раз.');
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            // Успіх: поля зникають, жовтий «Дякуємо…» у тій самій модалці, через ~4с закриється
            showInlineSuccess();
        } catch (err) {
            showError('global', 'Щось пішло не так. Спробуйте ще раз або зателефонуйте нам.');
            submitBtn.disabled = false;
            submitLabel.textContent = defaultSubmitText;
        } finally {
            // На success кнопка вже hidden — не повертаємо стан, щоб не миготіло
            if (formFields && !formFields.hidden) {
                submitBtn.disabled = false;
                submitLabel.textContent = defaultSubmitText;
            }
        }
    });
})();
