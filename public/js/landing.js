/* IDI_V_BANYU__ — модалка замовлення, анімації, дрібна інтерактивність */
(() => {
    'use strict';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    /* ---------- Шапка: тінь при скролі ---------- */
    const header = document.getElementById('site-header');
    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

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
            slide.classList.toggle('is-active', i === next);
        });

        card.dataset.slideIndex = String(next);
    };

    document.querySelectorAll('.product-card').forEach((card) => {
        const slides = card.querySelectorAll('.pcard-slide');
        if (!slides.length) return;

        card.dataset.slideIndex = '0';

        card.querySelectorAll('.pcard-arrow').forEach((arrow) => {
            arrow.addEventListener('click', (e) => {
                e.stopPropagation();
                const current = Number(card.dataset.slideIndex ?? 0);
                const step = arrow.classList.contains('pcard-arrow--next') ? 1 : -1;
                setCardSlide(card, current + step);
            });
        });

        if (slides.length < 2) return;

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
    const successView = document.getElementById('modal-success-view');
    const form = document.getElementById('order-form');
    const submitBtn = document.getElementById('order-submit');
    const productIdField = document.getElementById('field-product-id');
    const productNameEl = document.getElementById('modal-product-name');
    const successMessage = document.getElementById('success-message');
    const submitLabel = submitBtn.querySelector('.t-display');
    const defaultSubmitText = submitLabel.textContent;

    let lastFocused = null;
    let successTimer = null;
    let keyboardTimer = null;

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
        productIdField.value = productId;
        productNameEl.textContent = productName;
        formView.hidden = false;
        successView.hidden = true;
        clearErrors();
        modal.hidden = false;
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
        unlockScroll();
        form.reset();
        clearErrors();
        lastFocused?.focus?.();
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

            const data = await response.json();
            if (data.message) successMessage.textContent = data.message;
            formView.hidden = true;
            successView.hidden = false;
            successView.querySelector('button')?.focus();

            // Підтвердження закривається саме — кнопка лише для нетерплячих
            clearTimeout(successTimer);
            successTimer = setTimeout(closeModal, 4000);
        } catch (err) {
            showError('global', 'Щось пішло не так. Спробуйте ще раз або зателефонуйте нам.');
        } finally {
            submitBtn.disabled = false;
            submitLabel.textContent = defaultSubmitText;
        }
    });
})();
