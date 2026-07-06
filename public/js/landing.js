/* IDI_V_BANYU__ — модалка замовлення, анімації, дрібна інтерактивність */
(() => {
    'use strict';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    /* ---------- Шапка: тінь при скролі ---------- */
    const header = document.getElementById('site-header');
    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    /* ---------- Поява елементів при скролі ---------- */
    const revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-in');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });

        revealEls.forEach((el) => io.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('is-in'));
    }

    /* ---------- «Прожектор» тексту: абзац видно, поки він у центральній
       смузі екрана; вище/нижче — тьмяніє (працює в обидва боки скролу) ---------- */
    const aboutParas = document.querySelectorAll('.about-text p');
    if (aboutParas.length && 'IntersectionObserver' in window) {
        const spotlight = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                entry.target.classList.toggle('is-read', entry.isIntersecting);
            });
        }, { rootMargin: '-12% 0px -18% 0px', threshold: 0 });

        aboutParas.forEach((p) => spotlight.observe(p));
    } else {
        aboutParas.forEach((p) => p.classList.add('is-read'));
    }

    /* ---------- Cookie-повідомлення ---------- */
    const cookieBar = document.getElementById('cookie-bar');
    const cookieAccept = document.getElementById('cookie-accept');
    if (cookieBar && cookieAccept) {
        // localStorage може бути недоступний (приватний режим) — тоді
        // показуємо панель щоразу, але сторінку не ламаємо
        let cookieSeen = false;
        try { cookieSeen = localStorage.getItem('cookie-ok') === '1'; } catch (e) { /* ignore */ }

        if (!cookieSeen) cookieBar.hidden = false;

        cookieAccept.addEventListener('click', () => {
            cookieBar.hidden = true;
            try { localStorage.setItem('cookie-ok', '1'); } catch (e) { /* ignore */ }
        });
    }

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

    const openModal = (productId, productName) => {
        lastFocused = document.activeElement;
        productIdField.value = productId;
        productNameEl.textContent = productName;
        formView.hidden = false;
        successView.hidden = true;
        clearErrors();
        modal.hidden = false;
        lockScroll();
        setTimeout(() => document.getElementById('field-name').focus(), 60);
    };

    const closeModal = () => {
        clearTimeout(successTimer);
        successTimer = null;
        modal.hidden = true;
        unlockScroll();
        form.reset();
        clearErrors();
        lastFocused?.focus?.();
    };

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
                    comment: form.elements.comment.value.trim() || null,
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
