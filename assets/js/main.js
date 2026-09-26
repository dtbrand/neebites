/**
 * Neebites Main JavaScript
 * Ultra-Fast Modern Vanilla ES6+ (Zero jQuery Dependency)
 *
 * @package Neebites
 * @version 1.1.0
 */

document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const ajaxConfig = Object.assign({
        ajaxurl: '',
        nonce: '',
        freeShippingThreshold: 500,
        shopUrl: '/',
        cartUrl: '/',
        checkoutUrl: '',
        currencySymbol: '₹',
        strings: {}
    }, window.neebitesAjax || {});

    // Unescape any pre-encoded HTML entities (prevents double-encoding like &amp;amp;)
    const unesc = (val) => {
        if (!val) return '';
        const txt = document.createElement('textarea');
        txt.innerHTML = String(val);
        const once = txt.value;
        txt.innerHTML = once;
        return txt.value;
    };

    // Escape untrusted text before injecting into innerHTML (prevents DOM XSS).
    const esc = (val) => String(val == null ? '' : val)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');

    const formatMoney = (value) => {
        const amount = Number(value);
        const symbol = ajaxConfig.currencySymbol || '₹';
        return `${symbol}${Number.isFinite(amount) ? amount.toFixed(2) : '0.00'}`;
    };

    const postAjax = async (action, data = {}) => {
        if (!ajaxConfig.ajaxurl) {
            throw new Error('AJAX endpoint unavailable');
        }

        const body = new URLSearchParams({ action, ...data });
        if (ajaxConfig.nonce) body.set('nonce', ajaxConfig.nonce);

        const response = await fetch(ajaxConfig.ajaxurl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body.toString()
        });

        if (!response.ok) {
            throw new Error(`AJAX request failed (${response.status})`);
        }

        const payload = await response.json();
        if (!payload.success) {
            throw new Error(payload.data?.message || 'Request failed');
        }
        return payload;
    };

    // Keep the no-WooCommerce/demo storefront basket available to every drawer
    // handler, including handlers registered before the cart section below.
    const demoCart = [];

    // =========================================================================
    // 1. Mobile Menu Drawer
    // =========================================================================
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const mobilePanel  = document.getElementById('mobile-menu-panel');
    const mobileClose   = document.querySelector('.mobile-menu-close');
    const mobileOverlay = document.getElementById('mobile-menu-overlay');

    function openMobileMenu() {
        if (!mobilePanel || !mobileOverlay) return;
        mobilePanel.hidden = false;
        mobileOverlay.hidden = false;
        requestAnimationFrame(() => {
            mobilePanel.classList.add('is-open');
            if (mobileToggle) mobileToggle.setAttribute('aria-expanded', 'true');
        });
        document.body.style.overflow = 'hidden';
        document.body.classList.add('mobile-menu-open');
    }

    function closeMobileMenu() {
        if (!mobilePanel || !mobileOverlay) return;
        mobilePanel.classList.remove('is-open');
        if (mobileToggle) mobileToggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('mobile-menu-open');
        setTimeout(() => {
            mobilePanel.hidden = true;
            mobileOverlay.hidden = true;
            document.body.style.overflow = '';
        }, 300);
    }

    if (mobileToggle) mobileToggle.addEventListener('click', openMobileMenu);
    if (mobileClose)  mobileClose.addEventListener('click', closeMobileMenu);
    if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobileMenu);

    // =========================================================================
    // 2. Live AJAX Product Search
    // =========================================================================
    const searchToggles     = document.querySelectorAll('.header-search-toggle, .dock-search-trigger');
    const searchOverlay     = document.getElementById('header-search');
    const searchClose       = document.querySelector('.header-search-close');
    const searchInput       = document.getElementById('header-search-input');
    const searchResults     = document.getElementById('header-search-results');
    const searchSuggestions = document.getElementById('search-suggestions-box');
    const searchTagPills    = document.querySelectorAll('.search-tag-pill');
    let searchTimer         = null;

    function openSearch() {
        if (!searchOverlay) return;
        searchOverlay.hidden = false;
        searchToggles.forEach(btn => btn.setAttribute('aria-expanded', 'true'));
        document.body.classList.add('search-modal-open');
        if (searchSuggestions) searchSuggestions.style.display = 'block';
        if (searchResults) searchResults.hidden = true;
        setTimeout(() => {
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }, 120);
    }

    function closeSearch() {
        if (!searchOverlay) return;
        searchOverlay.hidden = true;
        searchToggles.forEach(btn => btn.setAttribute('aria-expanded', 'false'));
        document.body.classList.remove('search-modal-open');
        if (searchResults) searchResults.hidden = true;
    }

    searchToggles.forEach(btn => btn.addEventListener('click', openSearch));
    if (searchClose) searchClose.addEventListener('click', closeSearch);

    // Close when clicking dark frosted backdrop
    if (searchOverlay) {
        searchOverlay.addEventListener('click', (e) => {
            if (e.target === searchOverlay) closeSearch();
        });
    }

    // Keyboard support: Escape closes search
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchOverlay && !searchOverlay.hidden) {
            closeSearch();
        }
    });

    // Trending Search Pills Click Handlers
    searchTagPills.forEach(pill => {
        pill.addEventListener('click', () => {
            const query = pill.getAttribute('data-query');
            if (searchInput && query) {
                searchInput.value = query;
                triggerSearch(query);
            }
        });
    });

    function triggerSearch(query) {
        if (!searchInput || !searchResults) return;
        const trimmed = query.trim();

        if (trimmed.length < 2) {
            searchResults.innerHTML = '';
            searchResults.hidden = true;
            if (searchSuggestions) searchSuggestions.style.display = 'block';
            return;
        }

        if (searchSuggestions) searchSuggestions.style.display = 'none';
        searchResults.hidden = false;
        searchResults.innerHTML = `
            <div class="search-loading-state" style="padding: 24px; text-align: center; color: #3D2314; font-weight: 500;">
                <span style="display:inline-block; animation: pulseGlow 1s infinite;">🍫</span> Searching chocolate catalog...
            </div>
        `;

        clearTimeout(searchTimer);
        searchTimer = setTimeout(async () => {
            try {
                if (!ajaxConfig.ajaxurl) throw new Error('AJAX endpoint unavailable');
                const url = new URL(ajaxConfig.ajaxurl, window.location.href);
                url.searchParams.set('action', 'neebites_search_products');
                url.searchParams.set('term', trimmed);
                if (ajaxConfig.nonce) url.searchParams.set('nonce', ajaxConfig.nonce);
                const res = await fetch(url.toString(), { credentials: 'same-origin' });
                if (!res.ok) throw new Error(`Search failed (${res.status})`);
                const data = await res.json();

                if (data.success && data.data.results && data.data.results.length > 0) {
                    let html = '<div class="search-results-list" style="display:flex; flex-direction:column; gap:4px;">';
                    data.data.results.forEach(p => {
                        const badgeClean = unesc(p.badge);
                        const catClean   = unesc(p.category);
                        const titleClean = unesc(p.title);
                        const badgeHtml  = badgeClean ? `<span class="search-result-badge">${esc(badgeClean)}</span>` : '';
                        const catHtml    = catClean ? `<span class="search-result-category">${esc(catClean)}</span>` : '';
                        html += `
                            <a href="${esc(p.url)}" class="search-result-item">
                                <div class="search-result-thumb">${p.thumb}</div>
                                <div class="search-result-info">
                                    <div class="search-result-meta">${badgeHtml} ${catHtml}</div>
                                    <div class="search-result-title">${esc(titleClean)}</div>
                                    <div class="search-result-price">${p.price}</div>
                                </div>
                                <span class="search-result-arrow" style="color:#3D2314; font-size:18px; margin-left:auto;">&rarr;</span>
                            </a>
                        `;
                    });
                    html += '</div>';

                    if (data.data.view_all) {
                        html += `<a href="${esc(data.data.view_all)}" class="search-view-all">View all ${esc(data.data.total)} confectionery results &rarr;</a>`;
                    }
                    searchResults.innerHTML = html;
                    searchResults.hidden = false;
                } else {
                    searchResults.innerHTML = `
                        <div style="padding: 32px 16px; text-align: center;">
                            <div style="font-size: 32px; margin-bottom: 8px;">🍫</div>
                            <div style="font-weight: 600; color: #241408; margin-bottom: 4px;">No confections found for "${esc(trimmed)}"</div>
                            <div style="font-size: 13px; color: #6B4226;">Try searching for <strong>Dark Chocolate Almond</strong>, <strong>Kiwi Almond</strong>, or <strong>Milk Chocolate</strong>.</div>
                        </div>
                    `;
                    searchResults.hidden = false;
                }
            } catch (err) {
                console.error('Search error:', err);
                searchResults.innerHTML = `<div style="padding:16px; text-align:center; color:#c53030;">Search temporarily unavailable. Press Enter to view catalog.</div>`;
            }
        }, 220);
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            triggerSearch(e.target.value);
        });
    }

    // =========================================================================
    // 3. Slide-out Mini-Cart Drawer & Wishlist Drawer
    // =========================================================================
    const cartDrawer     = document.getElementById('neebites-cart-drawer');
    const wishlistDrawer = document.getElementById('neebites-wishlist-drawer');
    const cartBackdrop   = document.getElementById('neebites-drawer-backdrop');
    const cartTriggers   = document.querySelectorAll('.header-cart-trigger');
    const drawerClose    = document.querySelector('.btn-drawer-close');
    const wishlistTriggers = document.querySelectorAll('#header-wishlist-trigger, .dock-wishlist, #account-card-wishlist');
    const wishlistClose  = document.querySelector('.btn-wishlist-drawer-close');

    async function openCartDrawer() {
        if (!cartDrawer || !cartBackdrop) return;
        if (wishlistDrawer) {
            wishlistDrawer.classList.remove('is-open');
            wishlistDrawer.hidden = true;
        }
        cartDrawer.hidden = false;
        cartBackdrop.hidden = false;
        cartBackdrop.classList.add('is-active');
        document.body.classList.add('cart-drawer-open');
        requestAnimationFrame(() => {
            cartDrawer.classList.add('is-open');
        });
        document.body.style.overflow = 'hidden';

        // Asynchronously refresh cart drawer content to guarantee latest items
        try {
            if (ajaxConfig.ajaxurl && typeof demoCart !== 'undefined' && demoCart.length === 0) {
                const data = await postAjax('neebites_get_mini_cart');
                if (data.data && data.data.html) {
                    const bodyContainer = cartDrawer.querySelector('.cart-drawer-body');
                    if (bodyContainer) {
                        bodyContainer.innerHTML = data.data.html;
                    }
                    if (typeof data.data.cart_count !== 'undefined') {
                        document.querySelectorAll('.cart-count').forEach(el => {
                            el.textContent = data.data.cart_count;
                        });
                    }
                }
            }
        } catch (e) {}
    }

    function closeCartDrawer() {
        if (!cartDrawer || !cartBackdrop) return;
        cartDrawer.classList.remove('is-open');
        document.body.classList.remove('cart-drawer-open');
        setTimeout(() => {
            cartDrawer.hidden = true;
            if (!wishlistDrawer || !wishlistDrawer.classList.contains('is-open')) {
                cartBackdrop.classList.remove('is-active');
                cartBackdrop.hidden = true;
                document.body.style.overflow = '';
            }
        }, 300);
    }

    async function openWishlistDrawer() {
        if (!wishlistDrawer || !cartBackdrop) return;
        if (cartDrawer) {
            cartDrawer.classList.remove('is-open');
            cartDrawer.hidden = true;
        }
        wishlistDrawer.hidden = false;
        cartBackdrop.hidden = false;
        cartBackdrop.classList.add('is-active');
        document.body.classList.add('wishlist-drawer-open');
        requestAnimationFrame(() => {
            wishlistDrawer.classList.add('is-open');
        });
        document.body.style.overflow = 'hidden';

        // Asynchronously refresh drawer content to ensure up-to-date wishlist items
        try {
            if (ajaxConfig.ajaxurl) {
                const data = await postAjax('neebites_get_wishlist_drawer');
                if (data.data && data.data.html) {
                    const bodyEl = wishlistDrawer.querySelector('.cart-drawer-body');
                    if (bodyEl) {
                        bodyEl.innerHTML = data.data.html;
                    }
                }
            }
        } catch (e) {}
    }

    function closeWishlistDrawer() {
        if (!wishlistDrawer || !cartBackdrop) return;
        wishlistDrawer.classList.remove('is-open');
        document.body.classList.remove('wishlist-drawer-open');
        setTimeout(() => {
            wishlistDrawer.hidden = true;
            if (!cartDrawer || !cartDrawer.classList.contains('is-open')) {
                cartBackdrop.classList.remove('is-active');
                cartBackdrop.hidden = true;
                document.body.style.overflow = '';
            }
        }, 300);
    }

    cartTriggers.forEach(btn => btn.addEventListener('click', openCartDrawer));
    if (drawerClose) drawerClose.addEventListener('click', closeCartDrawer);

    wishlistTriggers.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openWishlistDrawer();
        });
    });
    if (wishlistClose) wishlistClose.addEventListener('click', closeWishlistDrawer);

    if (cartBackdrop) {
        cartBackdrop.addEventListener('click', () => {
            closeCartDrawer();
            closeWishlistDrawer();
        });
    }

    // Mini-Cart Quantity & Item Removal Controller
    if (cartDrawer) {
        cartDrawer.addEventListener('click', async (e) => {
            const plusBtn     = e.target.closest('.btn-qty-plus');
            const minusBtn    = e.target.closest('.btn-qty-minus');
            const removeBtn   = e.target.closest('.btn-remove-item, .remove_from_cart_button');
            const crossAddBtn = e.target.closest('.btn-cross-sell-add, .btn-starter-add');

            if (plusBtn || minusBtn) {
                e.preventDefault();
                const btn = plusBtn || minusBtn;
                const key = btn.dataset.key;
                const input = btn.parentElement.querySelector('.mini-cart-qty-input');
                let currentQty = parseInt(input.value, 10) || 1;
                let newQty = plusBtn ? currentQty + 1 : currentQty - 1;

                await updateCartQuantity(key, newQty);
            }

            if (removeBtn) {
                e.preventDefault();
                const key = removeBtn.dataset.cart_item_key;
                if (key) {
                    await updateCartQuantity(key, 0);
                }
            }

            if (crossAddBtn) {
                // When adding from cross-sells or starter drops inside cart drawer, refresh after AJAX add completes
                setTimeout(() => {
                    openCartDrawer();
                }, 400);
            }
        });
    }

    async function updateCartQuantity(cartItemKey, quantity) {
        try {
            const data = await postAjax('neebites_update_cart_quantity', {
                cart_item_key: cartItemKey,
                quantity: quantity
            });
            // Update Mini Cart Body
            const bodyContainer = cartDrawer.querySelector('.cart-drawer-body');
            if (bodyContainer) {
                bodyContainer.innerHTML = data.data.html;
            }
            // Update Header Badges
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = data.data.cart_count;
            });
        } catch (err) {
            console.error('Cart update failed:', err);
        }
    }

    // Listen for jQuery WooCommerce added_to_cart event if jQuery exists
    if (window.jQuery) {
        jQuery(document.body).on('added_to_cart', () => {
            openCartDrawer();
        });
    }

    // =========================================================================
    // 4. Quick View Modal Dialog
    // =========================================================================
    const qvModal    = document.getElementById('neebites-quickview-modal');
    const qvContent  = document.getElementById('neebites-quickview-content');
    const qvClose    = document.querySelector('.neebites-modal-close');
    const qvBackdrop = document.querySelector('.neebites-modal-backdrop');

    function openQuickView() {
        if (!qvModal) return;
        qvModal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function closeQuickView() {
        if (!qvModal) return;
        qvModal.hidden = true;
        document.body.style.overflow = '';
        if (qvContent) qvContent.innerHTML = '<div class="neebites-loading-spinner" aria-hidden="true"></div>';
    }

    if (qvClose)    qvClose.addEventListener('click', closeQuickView);
    if (qvBackdrop) qvBackdrop.addEventListener('click', closeQuickView);

    document.addEventListener('click', async (e) => {
        const qvBtn = e.target.closest('.btn-quickview');
        if (!qvBtn) return;

        e.preventDefault();
        const productId = qvBtn.dataset.productId;
        const card = qvBtn.closest('.neebites-product-card');

        openQuickView();

        try {
            const data = await postAjax('neebites_quick_view', {
                product_id: productId || ''
            });
            if (data.data?.html && qvContent) {
                qvContent.innerHTML = data.data.html;
                initQuickViewEvents();
                return;
            }
        } catch (err) {
            console.log('Quick view remote fetch fallback to client renderer');
        }

        // Client-side fallback for demo products
        if (card && qvContent) {
            const name = card.dataset.name || card.querySelector('.product-card-title a')?.textContent || 'Artisan Chocolate';
            const price = card.dataset.price || '₹349.00';
            const rawPrice = parseFloat(card.dataset.rawPrice) || 349.00;
            const image = card.dataset.image || card.querySelector('.product-card-image-link img')?.src || '';
            const category = card.dataset.category || 'Chocolates & Confectionery';
            const light = card.dataset.light || 'Bright Indirect';
            const water = card.dataset.water || 'Weekly';
            const diff = card.dataset.difficulty || 'Easy Care';

            qvContent.innerHTML = `
                <div class="quickview-wrapper" style="display:grid;grid-template-columns:1fr 1.2fr;gap:28px;align-items:center;">
                    <div class="quickview-gallery" style="background:#FAF6F0;border:1px solid #EAE0D5;border-radius:12px;padding:24px;text-align:center;">
                        <img src="${esc(image)}" alt="${esc(name)}" style="max-height:280px;width:auto;margin:0 auto;display:block;" />
                    </div>
                    <div class="quickview-details">
                        <span style="color:#6B4226;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:1.5px;">${esc(category)}</span>
                        <h2 style="margin:6px 0 10px;font-size:1.75rem;color:#3D2314;">${esc(name)}</h2>
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                            <span style="font-size:22px;font-weight:700;color:#3D2314;">${esc(price)}</span>
                            <span style="color:#C59B27;font-size:14px;">★★★★★ (48 reviews)</span>
                        </div>
                        <p style="color:#5C3D2E;font-size:14px;line-height:1.6;margin-bottom:16px;">
                            Slow-roasted California nonpareil almonds enveloped in silky Belgian chocolate and signature glazes. Handcrafted in micro batches with zero palm oil.
                        </p>
                        <div style="background:#FAF6F0;border:1px solid #EAE0D5;border-radius:8px;padding:12px;margin-bottom:20px;display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;color:#3D2314;">
                            <div>🍫 <strong>Cocoa:</strong> 100% Pure Butter</div>
                            <div>🥜 <strong>Almond:</strong> Slow-Roasted</div>
                            <div>✨ <strong>Dietary:</strong> 100% Vegetarian</div>
                            <div>❄️ <strong>Delivery:</strong> Cold-Pack Fresh</div>
                        </div>
                        <div style="display:flex;gap:12px;align-items:center;">
                            <button type="button" class="btn-quick-add demo-add-to-basket" 
                                data-product-id="${esc(productId)}"
                                data-name="${esc(name)}" 
                                data-price="${esc(price)}"
                                data-raw-price="${rawPrice}"
                                data-image="${esc(image)}"
                                style="flex:1;padding:12px 24px;border-radius:30px;background:#3D2314;color:#ffffff;font-weight:700;border:none;cursor:pointer;">
                                🍫 Add to Basket
                            </button>
                            <button type="button" class="btn-card-action btn-wishlist" data-product-id="${esc(productId)}" style="width:46px;height:46px;border-radius:50%;border:1px solid #EAE0D5;background:#FAF6F0;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            initQuickViewEvents();
        }
    });

    function initQuickViewEvents() {
        if (!qvContent) return;
        const mainImg = qvContent.querySelector('.quickview-main-image img');
        const thumbs = qvContent.querySelectorAll('.thumb-item');
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', () => {
                thumbs.forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
                const newSrc = thumb.dataset.image;
                if (mainImg && newSrc) mainImg.src = newSrc;
            });
        });
    }

    // =========================================================================
    // 5. Wishlist 1-Click Toggle
    // =========================================================================
    document.addEventListener('click', async (e) => {
        const wishBtn = e.target.closest('.btn-wishlist');
        if (!wishBtn) return;

        e.preventDefault();
        const productId = wishBtn.dataset.productId;
        if (!productId) return;

        wishBtn.classList.toggle('active');
        const isActive = wishBtn.classList.contains('active');
        const svg = wishBtn.querySelector('svg');
        if (svg) {
            svg.setAttribute('fill', isActive ? 'currentColor' : 'none');
            svg.style.color = isActive ? '#e53935' : '';
        }

        document.querySelectorAll('.wishlist-count').forEach(el => {
            let count = parseInt(el.textContent, 10) || 0;
            el.textContent = isActive ? count + 1 : Math.max(0, count - 1);
        });

        showBotanicalToast(isActive ? '❤️ Added to your confectionery wishlist! 🍫' : 'Removed from wishlist');

        try {
            const data = await postAjax('neebites_toggle_wishlist', { product_id: productId });
            if (typeof data.data?.count !== 'undefined') {
                document.querySelectorAll('.wishlist-count').forEach(el => {
                    el.textContent = data.data.count;
                });
            }
        } catch (err) {
            // Restore the previous state when the server rejects the toggle.
            wishBtn.classList.toggle('active', !isActive);
            if (svg) {
                svg.setAttribute('fill', isActive ? 'none' : 'currentColor');
                svg.style.color = isActive ? '' : '#e53935';
            }
            document.querySelectorAll('.wishlist-count').forEach(el => {
                let count = parseInt(el.textContent, 10) || 0;
                el.textContent = isActive ? Math.max(0, count - 1) : count + 1;
            });
            showBotanicalToast('Wishlist could not be updated. Please try again.');
        }
    });

    // =========================================================================
    // 5b. Botanical Toast & Interactive Demo Basket Manager
    // =========================================================================
    function showBotanicalToast(message) {
        let toast = document.getElementById('neebites-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'neebites-toast';
            toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#3D2314;color:#FAF6F0;padding:14px 24px;border-radius:30px;font-size:14px;font-weight:600;box-shadow:0 8px 24px rgba(61,35,20,0.3);z-index:99999;display:flex;align-items:center;gap:10px;transform:translateY(80px);opacity:0;transition:all 0.3s cubic-bezier(0.16,1,0.3,1);';
            document.body.appendChild(toast);
        }
        toast.textContent = String(message).replace(/<[^>]*>/g, '');
        requestAnimationFrame(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        });
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => {
            toast.style.transform = 'translateY(80px)';
            toast.style.opacity = '0';
        }, 3200);
    }

    function renderDemoCart() {
        const container = document.getElementById('demo-cart-items');
        const footer = document.getElementById('demo-cart-footer');
        const subtotalEl = document.getElementById('demo-subtotal-amount');
        const shippingFill = document.getElementById('shipping-progress-fill');
        const shippingMsg = document.querySelector('#shipping-goal-text .shipping-msg');

        if (!container) return;

        if (demoCart.length === 0) {
            container.innerHTML = `
                <div class="mini-cart-empty" style="text-align:center;padding:48px 20px;">
                    <span style="font-size:48px;display:block;margin-bottom:12px;">🍫</span>
                    <h4 style="margin:0 0 8px;color:#3D2314;font-size:18px">Your chocolate basket is empty</h4>
                    <p style="color:#6B4226;font-size:14px;margin:0 0 20px">Explore our artisan confectionery collection and treat yourself to gourmet chocolate coated almonds.</p>
                    <a href="${esc(ajaxConfig.shopUrl || '/')}" class="btn btn-primary" style="display:inline-block;padding:10px 24px;border-radius:30px;background:#3D2314;color:#FAF6F0;text-decoration:none;font-size:14px;font-weight:600">Shop All Confections &rarr;</a>
                </div>
            `;
            if (footer) footer.style.display = 'none';
            if (shippingFill) shippingFill.style.width = '0%';
            if (shippingMsg) shippingMsg.innerHTML = `Add <strong>${formatMoney(ajaxConfig.freeShippingThreshold)}</strong> more to unlock FREE cold-pack delivery!`;
            return;
        }

        let total = 0;
        let html = '<div style="display:flex;flex-direction:column;gap:16px;padding:16px 0;">';

        demoCart.forEach((item, index) => {
            const itemTotal = item.rawPrice * item.qty;
            total += itemTotal;
            html += `
                <div class="mini-cart-item" style="display:flex;gap:14px;align-items:center;padding-bottom:14px;border-bottom:1px solid #EAE0D5;">
                    <div style="width:64px;height:64px;border-radius:8px;background:#FAF6F0;overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                        <img src="${esc(item.image)}" alt="${esc(item.name)}" style="max-height:56px;width:auto;" />
                    </div>
                    <div style="flex:1;min-width:0;">
                        <h4 style="margin:0 0 4px;font-size:14px;color:#3D2314;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(item.name)}</h4>
                        <div style="font-size:13px;color:#6B4226;font-weight:700;">${formatMoney(itemTotal)}</div>
                        <div style="display:flex;align-items:center;gap:8px;margin-top:6px;">
                            <button type="button" class="btn-demo-qty" data-action="minus" data-index="${index}" style="width:24px;height:24px;border-radius:50%;border:1px solid #ccc;background:#fff;cursor:pointer;font-weight:bold;line-height:1;">-</button>
                            <span style="font-size:13px;font-weight:600;min-width:18px;text-align:center;">${item.qty}</span>
                            <button type="button" class="btn-demo-qty" data-action="plus" data-index="${index}" style="width:24px;height:24px;border-radius:50%;border:1px solid #ccc;background:#fff;cursor:pointer;font-weight:bold;line-height:1;">+</button>
                        </div>
                    </div>
                    <button type="button" class="btn-demo-remove" data-index="${index}" aria-label="Remove item" style="background:none;border:none;color:#999;font-size:18px;cursor:pointer;padding:4px 8px;">✕</button>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;

        if (footer) footer.style.display = 'block';
        if (subtotalEl) subtotalEl.textContent = formatMoney(total);

        // Free Shipping calculation (threshold $50)
        const threshold = Number(ajaxConfig.freeShippingThreshold) || 500;
        const diff = threshold - total;
        const percent = Math.min(100, Math.round((total / threshold) * 100));

        if (shippingFill) shippingFill.style.width = `${percent}%`;
        if (shippingMsg) {
            if (diff <= 0) {
                shippingMsg.innerHTML = '🎉 <strong>Congratulations! You unlocked FREE nursery shipping!</strong>';
            } else {
                shippingMsg.innerHTML = `Add <strong>${formatMoney(diff)}</strong> more to unlock FREE shipping!`;
            }
        }
    }

    // Add to basket click handler
    document.addEventListener('click', (e) => {
        const addBtn = e.target.closest('.demo-add-to-basket');
        if (!addBtn) return;
        e.preventDefault();

        const id = addBtn.dataset.productId;
        if (!id) return;
        const name = addBtn.dataset.name || 'Artisan Chocolate';
        const price = addBtn.dataset.price || `${ajaxConfig.currencySymbol || '₹'}349.00`;
        const rawPrice = parseFloat(addBtn.dataset.rawPrice) || 349.00;
        const image = addBtn.dataset.image || '';

        const originalHtml = addBtn.innerHTML;
        addBtn.innerHTML = '<span>Added! 🍫</span>';
        addBtn.style.background = '#6B4226';

        // Add or increment in demoCart
        const existing = demoCart.find(item => item.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            demoCart.push({ id, name, price, rawPrice, image, qty: 1 });
        }

        // Update badge counts
        const totalCount = demoCart.reduce((sum, item) => sum + item.qty, 0);
        document.querySelectorAll('.cart-count').forEach(el => {
            el.textContent = totalCount;
        });

        renderDemoCart();
        showBotanicalToast(`🍫 <strong>${name}</strong> added to your basket!`);

        // Close Quick View if open
        closeQuickView();

        // Slide open mini-cart drawer to show customer the updated basket
        setTimeout(() => {
            openCartDrawer();
        }, 300);

        setTimeout(() => {
            addBtn.innerHTML = originalHtml;
            addBtn.style.background = '';
        }, 1800);
    });

    // Handle cart quantity controls & removal
    document.addEventListener('click', (e) => {
        const qtyBtn = e.target.closest('.btn-demo-qty');
        const removeBtn = e.target.closest('.btn-demo-remove');

        if (qtyBtn) {
            const index = parseInt(qtyBtn.dataset.index, 10);
            const action = qtyBtn.dataset.action;
            if (demoCart[index]) {
                if (action === 'plus') {
                    demoCart[index].qty += 1;
                } else if (action === 'minus') {
                    demoCart[index].qty -= 1;
                    if (demoCart[index].qty <= 0) {
                        demoCart.splice(index, 1);
                    }
                }
                const totalCount = demoCart.reduce((sum, item) => sum + item.qty, 0);
                document.querySelectorAll('.cart-count').forEach(el => el.textContent = totalCount);
                renderDemoCart();
            }
        }

        if (removeBtn) {
            const index = parseInt(removeBtn.dataset.index, 10);
            if (demoCart[index]) {
                demoCart.splice(index, 1);
                const totalCount = demoCart.reduce((sum, item) => sum + item.qty, 0);
                document.querySelectorAll('.cart-count').forEach(el => el.textContent = totalCount);
                renderDemoCart();
                showBotanicalToast('Item removed from basket');
            }
        }
    });

    // =========================================================================
    // 6. Sticky Header & Single Product Sticky Add-to-Cart Bar
    // =========================================================================
    const header = document.getElementById('masthead');
    const stickyAddBar = document.getElementById('neebites-sticky-bar');
    const mainAddToCartBtn = document.querySelector('.single-product form.cart .single_add_to_cart_button');

    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;

        // Sticky Header shrink
        if (header) {
            header.classList.toggle('is-sticky', scrollY > 40);
        }

        // Sticky Product Bar
        if (stickyAddBar && mainAddToCartBtn) {
            const rect = mainAddToCartBtn.getBoundingClientRect();
            const isScrolledPast = rect.bottom < 0;
            stickyAddBar.classList.toggle('is-visible', isScrolledPast);
            stickyAddBar.setAttribute('aria-hidden', !isScrolledPast);
            document.body.classList.toggle('sticky-add-to-cart-visible', isScrolledPast);
        }
    }, { passive: true });

    // =========================================================================
    // 7. Wishlist Drawer Item Actions (Remove, Move All & Cross-Sells)
    // =========================================================================
    if (wishlistDrawer) {
        wishlistDrawer.addEventListener('click', async (e) => {
            const removeBtn  = e.target.closest('.btn-wishlist-remove');
            const moveBtn    = e.target.closest('.btn-move-to-cart');
            const moveAllBtn = e.target.closest('.btn-move-all-wishlist');
            const crossAdd   = e.target.closest('.btn-cross-sell-add');

            // 1. Remove Item with Smooth Transition
            if (removeBtn) {
                e.preventDefault();
                const item = removeBtn.closest('.wishlist-item');
                const pid = removeBtn.dataset.productId;
                if (item) {
                    item.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(30px) scale(0.96)';
                    setTimeout(() => {
                        item.remove();
                        const remaining = wishlistDrawer.querySelectorAll('.wishlist-item');
                        if (remaining.length === 0) {
                            // If empty, fetch empty state markup
                            if (ajaxConfig.ajaxurl) {
                                postAjax('neebites_get_wishlist_drawer')
                                    .then(d => {
                                        if (d.data && d.data.html) {
                                            const bodyEl = wishlistDrawer.querySelector('.cart-drawer-body');
                                            if (bodyEl) bodyEl.innerHTML = d.data.html;
                                        }
                                    }).catch(() => {});
                            }
                        } else {
                            const subheadCount = wishlistDrawer.querySelector('.subhead-count');
                            if (subheadCount) {
                                subheadCount.innerHTML = `<span class="subhead-icon">🍫</span> ${remaining.length} ${remaining.length === 1 ? 'chocolate' : 'chocolates'} saved`;
                            }
                        }
                    }, 300);
                }
                document.querySelectorAll('.wishlist-count').forEach(el => {
                    let count = parseInt(el.textContent, 10) || 0;
                    el.textContent = Math.max(0, count - 1);
                });
                showBotanicalToast('Removed from your confectionery wishlist');

                if (pid && ajaxConfig.ajaxurl) {
                    try {
                        await postAjax('neebites_toggle_wishlist', { product_id: pid });
                    } catch (err) {}
                }
            }

            // 2. Move All Items to Basket
            if (moveAllBtn) {
                e.preventDefault();
                const addBtns = wishlistDrawer.querySelectorAll('.wishlist-item .btn-move-to-cart');
                if (addBtns.length === 0) return;

                moveAllBtn.disabled = true;
                const originalHtml = moveAllBtn.innerHTML;
                moveAllBtn.innerHTML = '<span>Adding Chocolates to Basket...</span>';

                for (const btn of addBtns) {
                    btn.click();
                    await new Promise(r => setTimeout(r, 180));
                }

                showBotanicalToast('🍫 All saved chocolates added to your basket!');
                setTimeout(() => {
                    closeWishlistDrawer();
                    setTimeout(() => openCartDrawer(), 350);
                    moveAllBtn.disabled = false;
                    moveAllBtn.innerHTML = originalHtml;
                }, 500);
            }

            // 3. Single Add to Basket or Cross-sell Add -> Smooth transition to cart drawer
            if (moveBtn || crossAdd) {
                setTimeout(() => {
                    closeWishlistDrawer();
                    setTimeout(() => openCartDrawer(), 350);
                }, 400);
            }
        });
    }

    // =========================================================================
    // 8. Next-Level Artisan Confection Filter Tabs (Real-Time Smooth Filtering)
    // =========================================================================
    function initConfectionFilterSection(container, tabsWrap) {
        if (!container) return;
        const trendTabs = (tabsWrap || container).querySelectorAll('.trending-filter-tabs .tab-pill');
        if (!trendTabs.length) return;
        const emptyState = container.querySelector('.trending-filter-empty');
        const resetBtn = container.querySelector('.btn-reset-confection-filter');
        let isAnimating = false;

        // Helper to test if a confection card matches given filter tokens
        function confectionMatchesTokens(card, li, tokens) {
            if (tokens.includes('all')) return true;

            const flavor = (card.dataset.flavor || '').toLowerCase();
            const tags = (card.dataset.filterTags || '').toLowerCase();
            const cat = (card.dataset.category || '').toLowerCase();
            const name = (card.dataset.name || card.querySelector('.product-title-brand, h3, h2, a')?.textContent || '').toLowerCase();
            const classes = (li ? li.className : card.className).toLowerCase();

            for (const rawTok of tokens) {
                const tok = rawTok.trim().toLowerCase();
                if (!tok) continue;

                // Direct flavor attribute match
                if (flavor && flavor === tok) return true;

                // 1. Dark Chocolate
                if (tok === 'dark') {
                    if (flavor === 'dark' || classes.includes('flavor-dark') || tags.includes('dark') || name.includes('dark') || name.includes('70%') || name.includes('belgian')) {
                        return true;
                    }
                }

                // 2. Kiwi Signature Fruit
                if (tok === 'kiwi') {
                    if (flavor === 'kiwi' || classes.includes('flavor-kiwi') || tags.includes('kiwi') || name.includes('kiwi') || name.includes('fruit')) {
                        return true;
                    }
                }

                // 3. Milk & White Confections
                if (tok === 'milk' || tok === 'white' || tok === 'milk-white') {
                    if (flavor === 'milk' || flavor === 'white' || classes.includes('flavor-milk') || classes.includes('flavor-white') || classes.includes('flavor-milk-white') || tags.includes('milk') || tags.includes('white') || name.includes('milk') || name.includes('white') || name.includes('vanilla') || name.includes('swiss') || name.includes('38%')) {
                        return true;
                    }
                }

                // 4. Gourmet Flavours (Matcha, Caramel, Cinnamon, Ruby)
                if (tok === 'other' || tok === 'gourmet') {
                    if (flavor === 'other' || classes.includes('flavor-other') || classes.includes('flavor-gourmet') || tags.includes('other') || tags.includes('gourmet') || tags.includes('matcha') || tags.includes('caramel') || tags.includes('cinnamon') || tags.includes('ruby') || name.includes('matcha') || name.includes('caramel') || name.includes('cinnamon') || name.includes('ruby') || name.includes('honey')) {
                        return true;
                    }
                }

                // Fallback substring checks
                if (tags.includes(tok) || cat.includes(tok) || classes.includes(tok) || name.includes(tok)) {
                    return true;
                }
            }

            return false;
        }

        // Get all cards in the container
        function getConfectionCards() {
            return container.querySelectorAll('.neebites-product-card');
        }

        // Compute and display real-time count badges on each tab
        function updateTabCounts() {
            const cards = getConfectionCards();
            if (!cards.length) return;

            trendTabs.forEach(tab => {
                const filterStr = tab.dataset.filter || 'all';
                const tokens = filterStr.split(',').map(s => s.trim());
                let matchCount = 0;

                cards.forEach(card => {
                    const li = card.closest('li.product') || card;
                    if (confectionMatchesTokens(card, li, tokens)) {
                        matchCount++;
                    }
                });

                let countBadge = tab.querySelector('.tab-count');
                if (!countBadge) {
                    countBadge = document.createElement('span');
                    countBadge.className = 'tab-count';
                    tab.appendChild(countBadge);
                }
                countBadge.textContent = matchCount;
            });
        }

        // Execute smooth filtering with transition animation
        function applyConfectionFilter(activeTab, immediate = false) {
            const filterStr = activeTab.dataset.filter || 'all';
            const tokens = filterStr.split(',').map(s => s.trim().toLowerCase());
            const cards = getConfectionCards();
            if (!cards.length) return;

            // Update Tab states
            trendTabs.forEach(t => {
                const isActive = t === activeTab;
                t.classList.toggle('active', isActive);
                t.setAttribute('aria-selected', isActive ? 'true' : 'false');
                t.setAttribute('tabindex', isActive ? '0' : '-1');
            });

            let visibleCount = 0;
            const toHide = [];
            const toShow = [];

            cards.forEach(card => {
                const li = card.closest('li.product') || card;
                const matches = confectionMatchesTokens(card, li, tokens);
                if (matches) {
                    visibleCount++;
                    toShow.push({ card, li });
                } else {
                    toHide.push({ card, li });
                }
            });

            if (immediate) {
                // Instant update without delay (e.g. on page load)
                toHide.forEach(({ li }) => {
                    li.classList.add('is-filtered-out');
                    li.classList.remove('confection-card-exit', 'confection-card-enter');
                    li.style.setProperty('display', 'none', 'important');
                });
                toShow.forEach(({ li }) => {
                    li.classList.remove('is-filtered-out', 'confection-card-exit', 'confection-card-enter');
                    li.style.removeProperty('display');
                });
                if (emptyState) {
                    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
                }
                return;
            }

            // Next-Level 2-stage animated filter
            toHide.forEach(({ li }) => {
                li.classList.remove('confection-card-enter');
                li.classList.add('confection-card-exit');
            });

            setTimeout(() => {
                toHide.forEach(({ li }) => {
                    li.classList.add('is-filtered-out');
                    li.classList.remove('confection-card-exit');
                    li.style.setProperty('display', 'none', 'important');
                });

                toShow.forEach(({ li }, idx) => {
                    li.classList.remove('is-filtered-out', 'confection-card-exit');
                    li.style.removeProperty('display');
                    li.classList.add('confection-card-enter');
                    li.style.animationDelay = `${Math.min(idx * 40, 200)}ms`;
                });

                if (emptyState) {
                    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
                }

                setTimeout(() => {
                    toShow.forEach(({ li }) => {
                        li.classList.remove('confection-card-enter');
                        li.style.removeProperty('animation-delay');
                    });
                    isAnimating = false;
                }, 400);
            }, 180);
        }

        // Attach Click & Keyboard Event Handlers
        if (trendTabs.length > 0) {
            trendTabs.forEach((tab, index) => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (isAnimating && tab.classList.contains('active')) return;
                    isAnimating = true;
                    applyConfectionFilter(tab);
                });

                // Keyboard arrow navigation
                tab.addEventListener('keydown', (e) => {
                    let targetIndex = null;
                    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                        targetIndex = (index + 1) % trendTabs.length;
                    } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                        targetIndex = (index - 1 + trendTabs.length) % trendTabs.length;
                    }
                    if (targetIndex !== null) {
                        e.preventDefault();
                        trendTabs[targetIndex].focus();
                        trendTabs[targetIndex].click();
                    }
                });
            });

            // Empty state reset button
            if (resetBtn) {
                resetBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const allTab = (tabsWrap || container).querySelector('.trending-filter-tabs .tab-pill[data-filter="all"]');
                    if (allTab) allTab.click();
                });
            }

            // Initialize counts and check URL hash
            updateTabCounts();

            // Hash detection for deep linking (#dark, #kiwi, #milk, #gourmet)
            const currentHash = (window.location.hash || '').replace('#', '').toLowerCase();
            if (currentHash) {
                const matchingTab = Array.from(trendTabs).find(t => {
                    const f = (t.dataset.filter || '').toLowerCase();
                    return f === currentHash || f.split(',').includes(currentHash);
                });
                if (matchingTab) {
                    applyConfectionFilter(matchingTab, true);
                }
            }
        }
    }

    // 1. Homepage Trending Drops
    const trendingSection = document.getElementById('trending-confections') || document.querySelector('.sanctuary-trending-section');
    if (trendingSection) {
        initConfectionFilterSection(trendingSection, trendingSection);
    }

    // 2. Shop Page & Category Archive
    const shopLayout = document.querySelector('.shop-inner-layout') || document.querySelector('.shop-wrapper') || document.querySelector('.woocommerce-shop') || document.querySelector('.tax-product_cat');
    const shopTabsWrap = document.querySelector('.shop-filter-tabs') || document.querySelector('.neebites-shop-hero-banner');
    if (shopLayout && shopTabsWrap) {
        initConfectionFilterSection(shopLayout, shopTabsWrap);
    }

    // =========================================================================
    // 9. Single Product Planter Pairing & Botanical Accordions
    // =========================================================================
    const planterCheckbox = document.getElementById('neebites-planter-pair-checkbox');
    if (planterCheckbox) {
        planterCheckbox.addEventListener('change', () => {
            const priceEl = document.querySelector('.single-product .summary .price, .single-product .product-price');
            if (priceEl && !priceEl._baseHtml) {
                priceEl._baseHtml = priceEl.innerHTML;
                const match = priceEl.textContent.match(/[\$£€](\d+(\.\d+)?)/);
                if (match) {
                    priceEl._baseVal = parseFloat(match[1]);
                }
            }

            if (priceEl && priceEl._baseVal) {
                if (planterCheckbox.checked) {
                    const upgraded = (priceEl._baseVal + 149.00).toFixed(2);
                    priceEl.innerHTML = `<ins>₹${upgraded}</ins> <small style="font-size:13px;color:#C59B27;font-weight:600;">(Includes Luxury Gift Box)</small>`;
                    showBotanicalToast('🎁 Added Luxury Velvet Gift Box & Satin Ribbon to selection');
                } else {
                    priceEl.innerHTML = priceEl._baseHtml;
                }
            }
        });
    }

    // Planter color swatch selection (single product pairing card)
    document.addEventListener('click', (e) => {
        const swatch = e.target.closest('.swatch-btn');
        if (!swatch) return;
        const group = swatch.closest('.color-swatches');
        if (!group) return;
        group.querySelectorAll('.swatch-btn').forEach(s => {
            s.classList.remove('active');
            s.setAttribute('aria-pressed', 'false');
        });
        swatch.classList.add('active');
        swatch.setAttribute('aria-pressed', 'true');
    });

    // =========================================================================
    // 10. Mini-Cart Cross-Sell Quick Add
    // =========================================================================
    document.addEventListener('click', (e) => {
        const addAccBtn = e.target.closest('.btn-cross-sell-add');
        if (!addAccBtn) return;
        e.preventDefault();

        const id = addAccBtn.dataset.id || 'acc-item';
        const name = addAccBtn.dataset.name || 'Care Item';
        const price = addAccBtn.dataset.price || `${ajaxConfig.currencySymbol || '₹'}12.00`;
        const rawPrice = parseFloat(addAccBtn.dataset.rawPrice) || 12.00;

        addAccBtn.textContent = '✓ Added';
        addAccBtn.style.background = '#3D2314';
        addAccBtn.style.color = '#ffffff';

        const existing = demoCart.find(i => i.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            demoCart.push({ id, name, price, rawPrice, image: '', qty: 1 });
        }
        const totalCount = demoCart.reduce((sum, item) => sum + item.qty, 0);
        document.querySelectorAll('.cart-count').forEach(el => el.textContent = totalCount);
        renderDemoCart();
        showBotanicalToast(`🍫 <strong>${name}</strong> added to your basket!`);

        setTimeout(() => {
            addAccBtn.textContent = '+ Add';
            addAccBtn.style.background = '';
            addAccBtn.style.color = '';
        }, 1800);
    });

    // =========================================================================
    // 11. Customer Auth (Sign In / Register) Dual Tab Switcher
    // =========================================================================
    const authTabs = document.querySelectorAll('.auth-tab-btn');
    if (authTabs.length > 0) {
        authTabs.forEach(btn => {
            btn.addEventListener('click', () => {
                authTabs.forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                btn.classList.add('active');
                btn.setAttribute('aria-selected', 'true');

                const targetSelector = btn.dataset.target || '';
                const col1 = document.querySelector('#customer_login .u-column1');
                const col2 = document.querySelector('#customer_login .u-column2');

                if (col1 && col2) {
                    if (targetSelector.includes('u-column1')) {
                        col1.style.display = 'block';
                        col2.style.display = 'none';
                    } else {
                        col1.style.display = 'none';
                        col2.style.display = 'block';
                    }
                }
            });
        });
    }

    // =========================================================================
    // 12. Shop Archive Smart Filter Engine (sidebar facets, chips & drawer)
    // =========================================================================
    const shopSidebar = document.getElementById('shop-secondary');
    const shopMain = document.getElementById('shop-main-content');
    const shopCards = shopMain ? Array.from(shopMain.querySelectorAll('.neebites-product-card')) : [];

    if (shopSidebar && shopCards.length > 0) {
        const gridLi = (card) => card.closest('li.product') || card;

        const meta = shopCards.map((card) => {
            const lightRaw = (card.dataset.light || '').toLowerCase();
            const tags = (card.dataset.filterTags || '').toLowerCase().split(/\s+/).filter(Boolean);
            return {
                item: gridLi(card),
                tags: tags,
                price: parseFloat(card.dataset.rawPrice || '') || 0,
                lightRaw: lightRaw
            };
        });

        const FACET_KEYS = ['dark', 'kiwi', 'milk', 'white', 'other', 'beginner', 'intermediate', 'rare', 'petfriendly'];
        const state = { light: 'all', pets: false, difficulty: new Set(), maxPrice: null };

        const hasToken = (entry, token) => entry.tags.indexOf(token) !== -1;

        // A card without any care data is never hidden by the light facet so
        // that unclassified products do not silently disappear.
        const matchLight = (entry) => {
            if (state.light === 'all') return true;
            if (hasToken(entry, state.light)) return true;
            return entry.lightRaw === '';
        };
        const matchPets = (entry) => !state.pets || hasToken(entry, 'petfriendly');
        const matchDifficulty = (entry) => {
            if (state.difficulty.size === 0) return true;
            let hit = false;
            state.difficulty.forEach((token) => {
                if (hasToken(entry, token)) hit = true;
            });
            return hit;
        };
        const matchPrice = (entry) => state.maxPrice === null || entry.price <= state.maxPrice;

        const matches = (entry, exclude) => {
            if (exclude !== 'light' && !matchLight(entry)) return false;
            if (exclude !== 'pets' && !matchPets(entry)) return false;
            if (exclude !== 'difficulty' && !matchDifficulty(entry)) return false;
            if (exclude !== 'price' && !matchPrice(entry)) return false;
            return true;
        };

        const firstCard = shopCards[0];
        const gridList = firstCard.closest('ul.products');
        const emptyState = document.createElement('div');
        emptyState.className = 'neebites-no-results';
        emptyState.hidden = true;
        emptyState.style.display = 'none';
        const noResTitle = (ajaxConfig.strings || {}).noResultsTitle || 'No confections match these filters';
        const noResText = (ajaxConfig.strings || {}).noResultsText || 'Try widening your cocoa profile, flavour variant or price range.';
        const resetLabel = (ajaxConfig.strings || {}).resetFilters || 'Reset All Filters';
        emptyState.innerHTML =
            '<span class="nr-emoji" aria-hidden="true">🍫</span>' +
            '<h3>' + esc(noResTitle) + '</h3>' +
            '<p>' + esc(noResText) + '</p>' +
            '<button type="button" class="btn-reset-filters">' + esc(resetLabel) + '</button>';
        if (gridList && gridList.parentNode) {
            gridList.parentNode.insertBefore(emptyState, gridList);
        }

        const lightButtons = Array.from(shopSidebar.querySelectorAll('[data-light]'));
        const petsToggle = shopSidebar.querySelector('input[data-filter="pets"]');
        const diffBoxes = Array.from(shopSidebar.querySelectorAll('input[data-filter="difficulty"]'));
        const priceSlider = shopSidebar.querySelector('.price-slider-input');
        const priceCurrent = shopSidebar.querySelector('#price-current-val');
        const priceFill = (el) => {
            const min = parseFloat(el.min) || 0;
            const max = parseFloat(el.max) || 100;
            const val = parseFloat(el.value);
            const pct = max > min ? ((val - min) / (max - min)) * 100 : 100;
            el.style.setProperty('--price-fill', pct + '%');
        };

        const currency = ajaxConfig.currencySymbol || '₹';
        const resultsCountEl = shopMain.querySelector('.woocommerce-result-count');
        const baseResultsTemplate = resultsCountEl ? resultsCountEl.textContent : '';
        const resultsTemplate = (ajaxConfig.strings || {}).resultsCount || 'Showing %1$s of %2$s confections';

        const activeWrap = shopSidebar.querySelector('.shop-active-filters');
        const activeChips = shopSidebar.querySelector('.active-filter-chips');
        const openBtn = document.querySelector('.btn-open-filters');
        const badge = openBtn ? openBtn.querySelector('.filter-count-badge') : null;
        const applyCounts = Array.from(shopSidebar.querySelectorAll('.apply-count'));

        // Label sources for the chips are read from the sidebar controls so
        // every chip speaks the exact same language as its facet.
        const lightLabels = {};
        lightButtons.forEach((btn) => {
            lightLabels[btn.dataset.light] = btn.textContent.trim();
        });
        const diffLabels = {};
        diffBoxes.forEach((box) => {
            const label = box.closest('label');
            const text = label ? label.querySelector('.label-text') : null;
            diffLabels[box.value] = text ? text.textContent.trim().replace(/\s*\(.*\)$/, '') : box.value;
        });
        const petsLabelEl = shopSidebar.querySelector('.pet-friendly-toggle .toggle-label');
        const petsLabel = petsLabelEl ? petsLabelEl.textContent.trim() : 'Pet Friendly';

        let lastFocused = null;

        const collectChips = () => {
            const chips = [];
            if (state.light !== 'all') {
                chips.push({ group: 'light', value: state.light, label: lightLabels[state.light] || state.light });
            }
            if (state.pets) {
                chips.push({ group: 'pets', value: 'petfriendly', label: petsLabel });
            }
            state.difficulty.forEach((value) => {
                chips.push({ group: 'difficulty', value: value, label: diffLabels[value] || value });
            });
            if (state.maxPrice !== null) {
                chips.push({ group: 'price', value: String(state.maxPrice), label: currency + state.maxPrice + ' & under' });
            }
            return chips;
        };

        const renderActiveChips = () => {
            if (!activeWrap || !activeChips) return;
            const chips = collectChips();
            activeWrap.hidden = chips.length === 0;
            activeChips.innerHTML = chips.map((chip) =>
                '<button type="button" class="active-filter-chip" data-group="' + esc(chip.group) + '" data-value="' + esc(chip.value) + '">' +
                esc(chip.label) +
                '<span class="chip-x" aria-hidden="true">&times;</span></button>'
            ).join('');
        };

        // Only facet counts are computed here. Category counts stay exactly as
        // PHP rendered them, because they describe the whole catalogue rather
        // than the products of the current page.
        const renderFacetCounts = () => {
            shopSidebar.querySelectorAll('[data-count-for]').forEach((el) => {
                const key = el.dataset.countFor;
                if (FACET_KEYS.indexOf(key) === -1) return;
                let count = 0;
                meta.forEach((entry) => {
                    const ok = key === 'petfriendly'
                        ? hasToken(entry, 'petfriendly') && matches(entry, 'pets')
                        : hasToken(entry, key) && matches(entry, 'difficulty');
                    if (ok) count += 1;
                });
                el.textContent = String(count);
            });
        };

        const renderResultsCount = (visible) => {
            if (!resultsCountEl) return;
            const total = meta.length;
            if (visible === total) {
                resultsCountEl.textContent = baseResultsTemplate;
                return;
            }
            resultsCountEl.textContent = resultsTemplate.replace('%1$s', String(visible)).replace('%2$s', String(total));
        };

        const syncBadge = (chips) => {
            if (!badge) return;
            const n = chips.length;
            badge.textContent = String(n);
            badge.classList.toggle('is-visible', n > 0);
            if (openBtn) {
                openBtn.classList.toggle('has-filters', n > 0);
            }
        };

        const applyFilters = () => {
            let visible = 0;
            meta.forEach((entry) => {
                const ok = matches(entry, null);
                entry.item.classList.toggle('is-filtered-out', !ok);
                if (ok) visible += 1;
            });

            const chips = collectChips();
            if (visible === 0) {
                emptyState.hidden = false;
                emptyState.style.display = 'flex';
                if (gridList) gridList.style.display = 'none';
            } else {
                emptyState.hidden = true;
                emptyState.style.display = 'none';
                if (gridList) gridList.style.display = '';
            }
            renderActiveChips();
            renderFacetCounts();
            renderResultsCount(visible);
            syncBadge(chips);
            applyCounts.forEach((el) => {
                el.textContent = String(visible);
            });
        };

        const resetFilters = () => {
            state.light = 'all';
            state.pets = false;
            state.difficulty.clear();
            state.maxPrice = null;

            lightButtons.forEach((btn) => {
                btn.classList.toggle('active', btn.dataset.light === 'all');
            });
            if (petsToggle) petsToggle.checked = false;
            diffBoxes.forEach((box) => { box.checked = false; });
            if (priceSlider) {
                priceSlider.value = priceSlider.max;
                priceFill(priceSlider);
            }
            if (priceCurrent) priceCurrent.textContent = 'All Prices';
            applyFilters();
        };

        // Initialise the price slider bounds from the real catalogue prices.
        if (priceSlider) {
            const prices = meta.map((entry) => entry.price).filter((p) => p > 0);
            const maxPrice = prices.length ? Math.ceil(Math.max.apply(null, prices)) : 100;
            priceSlider.min = '0';
            priceSlider.max = String(maxPrice);
            priceSlider.value = String(maxPrice);
            priceFill(priceSlider);
            if (priceCurrent) {
                priceCurrent.textContent = currency + '0 – ' + currency + maxPrice;
            }
        }

        lightButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                state.light = btn.dataset.light || 'all';
                lightButtons.forEach((other) => {
                    other.classList.toggle('active', other === btn);
                });
                applyFilters();
            });
        });

        if (petsToggle) {
            petsToggle.addEventListener('change', () => {
                state.pets = petsToggle.checked;
                applyFilters();
            });
        }

        diffBoxes.forEach((box) => {
            box.addEventListener('change', () => {
                if (box.checked) {
                    state.difficulty.add(box.value);
                } else {
                    state.difficulty.delete(box.value);
                }
                applyFilters();
            });
        });

        if (priceSlider) {
            priceSlider.addEventListener('input', () => {
                const max = parseFloat(priceSlider.max) || 100;
                const val = parseFloat(priceSlider.value);
                state.maxPrice = val >= max ? null : val;
                priceFill(priceSlider);
                if (priceCurrent) {
                    priceCurrent.textContent = state.maxPrice === null
                        ? 'All Prices'
                        : 'Under ' + currency + val;
                }
                applyFilters();
            });
        }

        if (activeChips) {
            activeChips.addEventListener('click', (e) => {
                const chip = e.target.closest('.active-filter-chip');
                if (!chip) return;
                const group = chip.dataset.group;
                if (group === 'light') {
                    state.light = 'all';
                    lightButtons.forEach((btn) => {
                        btn.classList.toggle('active', btn.dataset.light === 'all');
                    });
                } else if (group === 'pets') {
                    state.pets = false;
                    if (petsToggle) petsToggle.checked = false;
                } else if (group === 'difficulty') {
                    state.difficulty.delete(chip.dataset.value);
                    diffBoxes.forEach((box) => {
                        if (box.value === chip.dataset.value) box.checked = false;
                    });
                } else if (group === 'price') {
                    state.maxPrice = null;
                    if (priceSlider) {
                        priceSlider.value = priceSlider.max;
                        priceFill(priceSlider);
                    }
                    if (priceCurrent) priceCurrent.textContent = 'All Prices';
                }
                applyFilters();
            });
        }

        shopSidebar.querySelectorAll('.btn-clear-all-filters').forEach((btn) => {
            btn.addEventListener('click', resetFilters);
        });

        const resetBtn = emptyState.querySelector('.btn-reset-filters');
        if (resetBtn) {
            resetBtn.addEventListener('click', resetFilters);
        }

        // Off-canvas drawer (tablet & mobile)
        const backdrop = document.createElement('div');
        backdrop.className = 'shop-filter-backdrop';
        document.body.appendChild(backdrop);

        const closeFilterDrawer = () => {
            shopSidebar.classList.remove('is-open');
            backdrop.classList.remove('is-visible');
            document.body.classList.remove('shop-filters-open');
            if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
            if (openBtn && lastFocused === openBtn) openBtn.focus();
            lastFocused = null;
        };

        const openFilterDrawer = () => {
            lastFocused = document.activeElement;
            shopSidebar.classList.add('is-open');
            backdrop.classList.add('is-visible');
            document.body.classList.add('shop-filters-open');
            if (openBtn) openBtn.setAttribute('aria-expanded', 'true');
            const closeBtn = shopSidebar.querySelector('.btn-close-filter-drawer');
            if (closeBtn) closeBtn.focus();
        };

        if (openBtn) {
            openBtn.setAttribute('aria-expanded', 'false');
            openBtn.addEventListener('click', openFilterDrawer);
        }

        shopSidebar.querySelectorAll('.btn-close-filter-drawer, .btn-apply-filters').forEach((btn) => {
            btn.addEventListener('click', closeFilterDrawer);
        });

        backdrop.addEventListener('click', closeFilterDrawer);

        shopSidebar.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && shopSidebar.classList.contains('is-open')) {
                closeFilterDrawer();
            }
        });

        applyFilters();
    }

    // =========================================================================
    // Connoisseur Reviews 1-Line Scrolling Carousel & Interactive Dots
    // =========================================================================
    const reviewsTrack = document.getElementById('connoisseur-reviews-track');
    if (reviewsTrack) {
        const prevBtn = document.querySelector('.btn-reviews-prev');
        const nextBtn = document.querySelector('.btn-reviews-next');
        const dots    = document.querySelectorAll('.review-dot');
        const cards   = reviewsTrack.querySelectorAll('.review-card');

        function getReviewStep() {
            const firstCard = cards[0];
            const gap = parseInt(window.getComputedStyle(reviewsTrack).gap) || 20;
            return (firstCard ? firstCard.offsetWidth : 300) + gap;
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                reviewsTrack.scrollBy({ left: -getReviewStep(), behavior: 'smooth' });
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                reviewsTrack.scrollBy({ left: getReviewStep(), behavior: 'smooth' });
            });
        }

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                const idx = parseInt(dot.getAttribute('data-index'), 10);
                if (cards[idx]) {
                    const cardLeft = cards[idx].offsetLeft - reviewsTrack.offsetLeft;
                    reviewsTrack.scrollTo({ left: cardLeft, behavior: 'smooth' });
                }
            });
        });

        let isReviewTicking = false;
        function updateReviewCarouselState() {
            const scrollLeft = reviewsTrack.scrollLeft;
            const maxScroll  = reviewsTrack.scrollWidth - reviewsTrack.clientWidth;

            if (prevBtn) {
                prevBtn.disabled = scrollLeft <= 4;
            }
            if (nextBtn) {
                nextBtn.disabled = scrollLeft >= maxScroll - 6;
            }

            let activeIdx = 0;
            let minDiff = Infinity;
            const trackCenter = scrollLeft + (reviewsTrack.clientWidth / 2);

            cards.forEach((card, idx) => {
                const cardCenter = (card.offsetLeft - reviewsTrack.offsetLeft) + (card.offsetWidth / 2);
                const diff = Math.abs(cardCenter - trackCenter);
                if (diff < minDiff) {
                    minDiff = diff;
                    activeIdx = idx;
                }
            });

            dots.forEach((dot, idx) => {
                const isActive = idx === activeIdx;
                dot.classList.toggle('active', isActive);
                dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            isReviewTicking = false;
        }

        reviewsTrack.addEventListener('scroll', () => {
            if (!isReviewTicking) {
                window.requestAnimationFrame(updateReviewCarouselState);
                isReviewTicking = true;
            }
        }, { passive: true });

        // Initial check and resize handler
        updateReviewCarouselState();
        window.addEventListener('resize', updateReviewCarouselState, { passive: true });
    }

    // =========================================================================
    // Next-Level Footer Accordion Options & Sub-Options Controller
    // =========================================================================
    function initFooterAccordions() {
        // Progressive enhancement: If dynamic sidebar widget has no toggle button, wrap its title on mobile
        const sidebarCols = document.querySelectorAll('.footer-col:not(.footer-col-brand)');
        sidebarCols.forEach(col => {
            if (!col.querySelector('.footer-accordion-toggle')) {
                const title = col.querySelector('.widget-title');
                const content = col.querySelector('ul, .textwidget, form, div');
                if (title && content) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'footer-accordion-toggle';
                    btn.setAttribute('aria-expanded', 'false');
                    
                    const titleSpan = document.createElement('span');
                    titleSpan.className = 'widget-title';
                    titleSpan.textContent = title.textContent;
                    
                    const chevron = document.createElement('span');
                    chevron.className = 'accordion-chevron';
                    chevron.setAttribute('aria-hidden', 'true');
                    chevron.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="6 9 12 15 18 9"></polyline></svg>';
                    
                    btn.appendChild(titleSpan);
                    btn.appendChild(chevron);
                    
                    const wrap = document.createElement('div');
                    wrap.className = 'footer-accordion-content';
                    content.parentNode.insertBefore(wrap, content);
                    wrap.appendChild(content);
                    
                    title.replaceWith(btn);
                    col.classList.add('footer-accordion-col');
                }
            }
        });

        // Query all accordion toggles
        const allToggles = document.querySelectorAll('.footer-accordion-toggle');
        allToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                // Only activate accordion collapse/expand behavior on mobile / tablet (<= 768px)
                if (window.innerWidth > 768) return;
                
                e.preventDefault();
                const col = toggle.closest('.footer-accordion-col');
                const content = col ? col.querySelector('.footer-accordion-content') : null;
                const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

                // Close other open accordion columns for ultra-clean mobile UX
                allToggles.forEach(otherToggle => {
                    if (otherToggle !== toggle) {
                        otherToggle.setAttribute('aria-expanded', 'false');
                        const otherCol = otherToggle.closest('.footer-accordion-col');
                        if (otherCol) {
                            otherCol.classList.remove('is-open');
                            const otherContent = otherCol.querySelector('.footer-accordion-content');
                            if (otherContent) otherContent.classList.remove('is-open');
                        }
                    }
                });

                // Toggle targeted option
                if (isExpanded) {
                    toggle.setAttribute('aria-expanded', 'false');
                    if (col) col.classList.remove('is-open');
                    if (content) content.classList.remove('is-open');
                } else {
                    toggle.setAttribute('aria-expanded', 'true');
                    if (col) col.classList.add('is-open');
                    if (content) content.classList.add('is-open');
                }
            });
        });
    }
    initFooterAccordions();

    // =========================================================================
    // Mobile Navigation Menu Nested Sub-Options Accordion
    // =========================================================================
    function initMobileMenuAccordions() {
        const menuItemsWithChildren = document.querySelectorAll('.mobile-menu li.menu-item-has-children');
        menuItemsWithChildren.forEach(item => {
            const parentLink = item.querySelector(':scope > a');
            const subMenu = item.querySelector(':scope > .sub-menu');
            if (parentLink && subMenu && !item.querySelector(':scope > .mobile-menu-item-row')) {
                const row = document.createElement('div');
                row.className = 'mobile-menu-item-row';
                
                const toggleBtn = document.createElement('button');
                toggleBtn.type = 'button';
                toggleBtn.className = 'mobile-submenu-toggle';
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.setAttribute('aria-label', 'Toggle submenu options');
                toggleBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="6 9 12 15 18 9"></polyline></svg>';

                item.insertBefore(row, parentLink);
                row.appendChild(parentLink);
                row.appendChild(toggleBtn);

                toggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const isOpen = toggleBtn.classList.contains('is-open');
                    if (isOpen) {
                        toggleBtn.classList.remove('is-open');
                        toggleBtn.setAttribute('aria-expanded', 'false');
                        subMenu.classList.remove('is-open');
                    } else {
                        toggleBtn.classList.add('is-open');
                        toggleBtn.setAttribute('aria-expanded', 'true');
                        subMenu.classList.add('is-open');
                    }
                });
            }
        });
    }
    initMobileMenuAccordions();

    // =========================================================================
    // Top Announcement / Shipping Ticker 1-Line Auto-Slider with Smart Effects
    // =========================================================================
    function initAnnouncementSlider() {
        const wrap = document.querySelector('.announcement-slider-wrap');
        const viewport = document.getElementById('announcement-slider-viewport');
        if (!wrap || !viewport) return;

        const slides = Array.from(viewport.querySelectorAll('.announcement-slide'));
        if (slides.length <= 1) return;

        const prevBtn = wrap.querySelector('.btn-announcement-prev');
        const nextBtn = wrap.querySelector('.btn-announcement-next');

        let currentIndex = 0;
        let isTransitioning = false;
        let autoPlayTimer = null;
        let isHoverPaused = false;
        let isTouchPaused = false;
        const intervalDuration = 3800; // 3.8 seconds

        // Identify current active slide
        const activeSlide = viewport.querySelector('.announcement-slide.is-active');
        if (activeSlide) {
            const foundIdx = slides.indexOf(activeSlide);
            if (foundIdx !== -1) currentIndex = foundIdx;
        } else {
            slides[0].classList.add('is-active');
        }

        function goToSlide(targetIndex, direction = 'next') {
            if (isTransitioning) return;
            if (targetIndex === currentIndex) return;

            // Cyclic index wrapping
            if (targetIndex >= slides.length) targetIndex = 0;
            if (targetIndex < 0) targetIndex = slides.length - 1;

            isTransitioning = true;
            const currentSlide = slides[currentIndex];
            const nextSlide = slides[targetIndex];

            // Reset other slides
            slides.forEach(s => {
                if (s !== currentSlide && s !== nextSlide) {
                    s.className = 'announcement-slide';
                }
            });

            if (direction === 'next') {
                nextSlide.className = 'announcement-slide is-entering-up';
                void nextSlide.offsetHeight; // Force reflow
                currentSlide.className = 'announcement-slide is-exiting-up';
                nextSlide.className = 'announcement-slide is-active';
            } else {
                nextSlide.className = 'announcement-slide is-entering-down';
                void nextSlide.offsetHeight; // Force reflow
                currentSlide.className = 'announcement-slide is-exiting-down';
                nextSlide.className = 'announcement-slide is-active';
            }

            currentIndex = targetIndex;

            setTimeout(() => {
                currentSlide.className = 'announcement-slide';
                isTransitioning = false;
            }, 460);
        }

        function slideNext() {
            goToSlide(currentIndex + 1, 'next');
        }

        function slidePrev() {
            goToSlide(currentIndex - 1, 'prev');
        }

        function startAutoPlay() {
            stopAutoPlay();
            if (isHoverPaused || isTouchPaused || document.hidden) return;
            autoPlayTimer = setInterval(slideNext, intervalDuration);
        }

        function stopAutoPlay() {
            if (autoPlayTimer) {
                clearInterval(autoPlayTimer);
                autoPlayTimer = null;
            }
        }

        function resetAutoPlay() {
            stopAutoPlay();
            startAutoPlay();
        }

        // Nav Buttons
        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                slidePrev();
                resetAutoPlay();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                slideNext();
                resetAutoPlay();
            });
        }

        // Smart Effect 1: Mouse Hover Pause
        const bar = document.querySelector('.neebites-announcement-bar');
        if (bar) {
            bar.addEventListener('mouseenter', () => {
                isHoverPaused = true;
                stopAutoPlay();
            });
            bar.addEventListener('mouseleave', () => {
                isHoverPaused = false;
                startAutoPlay();
            });
        }

        // Smart Effect 2: Touch Pause & Swipe Detection
        let touchStartX = 0;
        let touchStartY = 0;
        let touchEndX = 0;
        let touchEndY = 0;
        let touchResumeTimeout = null;

        viewport.addEventListener('touchstart', (e) => {
            isTouchPaused = true;
            stopAutoPlay();
            if (touchResumeTimeout) clearTimeout(touchResumeTimeout);
            if (e.changedTouches && e.changedTouches[0]) {
                touchStartX = e.changedTouches[0].screenX;
                touchStartY = e.changedTouches[0].screenY;
            }
        }, { passive: true });

        viewport.addEventListener('touchend', (e) => {
            if (e.changedTouches && e.changedTouches[0]) {
                touchEndX = e.changedTouches[0].screenX;
                touchEndY = e.changedTouches[0].screenY;
                handleTouchSwipe();
            }
            touchResumeTimeout = setTimeout(() => {
                isTouchPaused = false;
                startAutoPlay();
            }, 2500);
        }, { passive: true });

        function handleTouchSwipe() {
            const diffX = touchEndX - touchStartX;
            const diffY = touchEndY - touchStartY;
            if (Math.abs(diffX) > 35 && Math.abs(diffX) > Math.abs(diffY)) {
                if (diffX < 0) {
                    slideNext();
                } else {
                    slidePrev();
                }
            }
        }

        // Smart Effect 3: Page Visibility
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoPlay();
            } else {
                startAutoPlay();
            }
        });

        // Launch Auto-Play
        startAutoPlay();
    }
    initAnnouncementSlider();

    // =========================================================================
    // 14. Myntra Single Product (PDP) Interactions
    // =========================================================================
    // Pack Size selector dynamic auto-populated controller & WooCommerce Variations Sync
    const sizeChips = document.querySelectorAll('.myntra-size-chip');
    
    function applyPackSizeSelection(chip) {
        if (!chip || chip.classList.contains('out-of-stock') || chip.disabled) return;

        // Visual active state
        sizeChips.forEach(c => {
            c.classList.remove('active');
            c.setAttribute('aria-checked', 'false');
        });
        chip.classList.add('active');
        chip.setAttribute('aria-checked', 'true');

        const attrName = chip.dataset.attributeName;
        const attrVal  = chip.dataset.attributeVal;
        const varId    = chip.dataset.variationId;
        const price    = chip.dataset.price;
        const mrp      = chip.dataset.mrp;
        const off      = chip.dataset.off;

        // 1. Sync with native WooCommerce variable form
        const varForm = document.querySelector('.single-product form.variations_form');
        if (varForm && attrVal) {
            let select = null;
            if (attrName) {
                select = varForm.querySelector(`select[name="${attrName}"]`);
            }
            if (!select) {
                select = varForm.querySelector('select#pa_weight, select[name*="weight"], select[data-attribute_name*="weight"]');
            }
            if (!select) {
                select = varForm.querySelector('select');
            }

            if (select) {
                select.value = attrVal;
                // Dispatch native change event
                select.dispatchEvent(new Event('change', { bubbles: true }));
                // Dispatch jQuery change event for WooCommerce core add-to-cart-variation.js
                if (window.jQuery) {
                    window.jQuery(select).val(attrVal).trigger('change');
                }
            }

            // Sync hidden variation_id input
            const hiddenVarId = varForm.querySelector('input[name="variation_id"]');
            if (hiddenVarId && varId) {
                hiddenVarId.value = varId;
            }
        }

        // 2. Sync target product/variation ID on all Buy Now & Add To Bag buttons
        if (varId) {
            document.querySelectorAll('.btn-buy-now, .myntra-btn-buy-now, .btn-sticky-buy-now, .btn-sticky-add-bag').forEach(btn => {
                btn.dataset.productId = varId;
            });
        }

        // 3. Update main PDP price display
        const priceCurr = document.querySelector('.myntra-price-current');
        const priceMrp  = document.querySelector('.myntra-price-mrp del');
        const priceOff  = document.querySelector('.myntra-price-off');

        if (priceCurr && price) priceCurr.textContent = '₹' + price;
        if (priceMrp && mrp) priceMrp.textContent = '₹' + mrp;
        if (priceOff && off) priceOff.textContent = `(${off}% OFF)`;

        // 4. Update sticky footer bar price display
        const stickyPriceCurr = document.querySelector('.sticky-price-current');
        const stickyPriceMrp  = document.querySelector('.sticky-price-mrp');
        const stickyPriceOff  = document.querySelector('.sticky-price-off');

        if (stickyPriceCurr && price) stickyPriceCurr.textContent = '₹' + price;
        if (stickyPriceMrp && mrp) stickyPriceMrp.textContent = 'MRP ₹' + mrp;
        if (stickyPriceOff && off) stickyPriceOff.textContent = `(${off}% OFF)`;

        // 5. Update Options Modal selection and header price
        const optionsModal = document.getElementById('neebites-options-modal');
        if (optionsModal && varId) {
            optionsModal.querySelectorAll('.options-modal-chip-card').forEach(mc => {
                if (mc.dataset.variationId === String(varId)) {
                    mc.classList.add('active');
                } else {
                    mc.classList.remove('active');
                }
            });
            const modalPrice = optionsModal.querySelector('.options-modal-price');
            const modalMrp   = optionsModal.querySelector('.options-modal-mrp');
            const modalOff   = optionsModal.querySelector('.options-modal-off');
            if (modalPrice && price) modalPrice.textContent = '₹' + price;
            if (modalMrp && mrp) modalMrp.textContent = 'MRP ₹' + mrp;
            if (modalOff && off) modalOff.textContent = `(${off}% OFF)`;
        }
    }

    if (sizeChips.length) {
        // Strict user directive: "SELECT PACK SIZE why auto selted only maully selted"
        // Do NOT auto-select on page load! Customer selects pack size manually.
        sizeChips.forEach(chip => {
            chip.addEventListener('click', (e) => {
                e.preventDefault();
                applyPackSizeSelection(chip);
            });
        });
    }

    // Helper: Check if a pack size is selected on variable products
    function isPackSizeSelected() {
        const chips = document.querySelectorAll('.myntra-size-chip');
        if (!chips.length) {
            return true; // Not a variable product with size chips
        }
        const activeChip = document.querySelector('.myntra-size-chip.active');
        const varIdInput = document.querySelector('.single-product form.variations_form input[name="variation_id"]');
        const hasValidVarId = varIdInput && parseInt(varIdInput.value, 10) > 0;
        return Boolean(activeChip && hasValidVarId);
    }

    // Next-Level Luxury Pack Size Selection Options Modal Controller
    function initPackSizeOptionsModal() {
        const optionsModal = document.getElementById('neebites-options-modal');
        if (!optionsModal) return;

        // Teleport to document.body to ensure top-level stacking context
        if (optionsModal.parentNode !== document.body) {
            document.body.appendChild(optionsModal);
        }

        let currentTargetAction = 'add_to_bag'; // 'add_to_bag' or 'buy_now'

        window.neebitesOpenOptionsModal = function(action) {
            currentTargetAction = action || 'add_to_bag';
            optionsModal.dataset.targetAction = currentTargetAction;

            const submitBtn = optionsModal.querySelector('.btn-modal-confirm-action');
            const btnText   = submitBtn ? submitBtn.querySelector('.btn-modal-text') : null;
            const activeCard = optionsModal.querySelector('.options-modal-chip-card.active');

            if (submitBtn) {
                submitBtn.classList.remove('mode-add-bag', 'mode-buy-now');
                if (currentTargetAction === 'buy_now') {
                    submitBtn.classList.add('mode-buy-now');
                    if (btnText) {
                        btnText.textContent = activeCard 
                            ? `⚡ BUY NOW (${activeCard.dataset.size})`
                            : 'SELECT PACK SIZE TO BUY';
                    }
                } else {
                    submitBtn.classList.add('mode-add-bag');
                    if (btnText) {
                        btnText.textContent = activeCard 
                            ? `ADD TO BAG (${activeCard.dataset.size})`
                            : 'SELECT PACK SIZE';
                    }
                }
            }

            optionsModal.classList.add('active');
            document.body.classList.add('neebites-options-modal-open');
        };

        function closeOptionsModal() {
            optionsModal.classList.remove('active');
            document.body.classList.remove('neebites-options-modal-open');
        }

        // Close on backdrop or close button
        optionsModal.addEventListener('click', (e) => {
            if (e.target.closest('.options-modal-close') || e.target.classList.contains('options-modal-backdrop')) {
                e.preventDefault();
                closeOptionsModal();
            }
        });

        // ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && optionsModal.classList.contains('active')) {
                closeOptionsModal();
            }
        });

        // Chip card selection inside modal
        const modalCards = optionsModal.querySelectorAll('.options-modal-chip-card');
        modalCards.forEach(card => {
            card.addEventListener('click', (e) => {
                e.preventDefault();
                if (card.classList.contains('out-of-stock')) return;

                modalCards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');

                // Update modal header price
                const modalPrice = optionsModal.querySelector('.options-modal-price');
                const modalMrp   = optionsModal.querySelector('.options-modal-mrp');
                const modalOff   = optionsModal.querySelector('.options-modal-off');
                if (modalPrice && card.dataset.price) modalPrice.textContent = '₹' + card.dataset.price;
                if (modalMrp && card.dataset.mrp) modalMrp.textContent = 'MRP ₹' + card.dataset.mrp;
                if (modalOff && card.dataset.off) modalOff.textContent = `(${card.dataset.off}% OFF)`;

                // Update submit button text
                const submitBtn = optionsModal.querySelector('.btn-modal-confirm-action');
                const btnText   = submitBtn ? submitBtn.querySelector('.btn-modal-text') : null;
                if (btnText) {
                    if (currentTargetAction === 'buy_now') {
                        btnText.textContent = `⚡ BUY NOW (${card.dataset.size})`;
                    } else {
                        btnText.textContent = `ADD TO BAG (${card.dataset.size})`;
                    }
                }

                // Synchronize with main PDP chips
                const vId = card.dataset.variationId;
                const pageChip = document.querySelector(`.myntra-size-chip[data-variation-id="${vId}"]`);
                if (pageChip) {
                    applyPackSizeSelection(pageChip);
                }
            });
        });

        // Submit button inside modal
        const submitBtn = optionsModal.querySelector('.btn-modal-confirm-action');
        if (submitBtn) {
            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const activeCard = optionsModal.querySelector('.options-modal-chip-card.active');
                if (!activeCard) {
                    const grid = optionsModal.querySelector('.options-modal-chips-grid');
                    if (grid) {
                        grid.classList.add('shake-warning');
                        setTimeout(() => grid.classList.remove('shake-warning'), 600);
                    }
                    return;
                }

                const vId = activeCard.dataset.variationId;
                const pageChip = document.querySelector(`.myntra-size-chip[data-variation-id="${vId}"]`);
                if (pageChip) {
                    applyPackSizeSelection(pageChip);
                }

                closeOptionsModal();

                // Get quantity
                const sQtyInput = document.querySelector('.sticky-qty-input');
                const mQtyInput = document.querySelector('.single-product form.cart input.qty, .single-product form.cart input[name="quantity"]');
                let qty = 1;
                if (sQtyInput) qty = parseInt(sQtyInput.value, 10) || 1;
                else if (mQtyInput) qty = parseInt(mQtyInput.value, 10) || 1;

                // Execute selected action
                if (currentTargetAction === 'buy_now') {
                    const baseCheckout = ajaxConfig.checkoutUrl || '/';
                    const sep = baseCheckout.includes('?') ? '&' : '?';

                    if (typeof showBotanicalToast === 'function') {
                        showBotanicalToast('Taking you directly to Checkout! ⚡');
                    }
                    window.location.href = `${baseCheckout}${sep}add-to-cart=${encodeURIComponent(vId)}&quantity=${encodeURIComponent(qty)}`;
                } else {
                    const mainAddBtn = document.querySelector('.single-product form.cart .single_add_to_cart_button');
                    if (mainAddBtn) {
                        if (sQtyInput && mQtyInput) {
                            mQtyInput.value = sQtyInput.value;
                        }
                        mainAddBtn.click();
                        if (typeof showBotanicalToast === 'function') {
                            showBotanicalToast(`Added ${activeCard.dataset.size} to your bag! 🛍️`);
                        }
                    }
                }
            });
        }
    }
    initPackSizeOptionsModal();

    // Two-way sync with WooCommerce variations events
    if (window.jQuery) {
        window.jQuery(document).on('found_variation', '.single-product form.variations_form', function(e, variation) {
            if (!variation) return;
            const vId = String(variation.variation_id);
            const matchingChip = document.querySelector(`.myntra-size-chip[data-variation-id="${vId}"]`);
            if (matchingChip && !matchingChip.classList.contains('active')) {
                applyPackSizeSelection(matchingChip);
            }
        });
    }

    // Pincode Validator
    const pincodeInput = document.getElementById('myntra-pincode-input');
    const pincodeBtn   = document.getElementById('myntra-pincode-check-btn');
    const pincodeStatus = document.getElementById('myntra-pincode-status');

    if (pincodeBtn && pincodeInput && pincodeStatus) {
        const checkPin = () => {
            const pin = pincodeInput.value.trim();
            if (/^\d{6}$/.test(pin)) {
                pincodeStatus.innerHTML = `
                    <div class="pincode-valid-msg" style="color: #03a685; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; margin: 6px 0 10px;">
                        <span style="display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; border-radius:50%; background:#e6f7f3; color:#03a685; font-size:11px;">✓</span>
                        <span>Delivering to <strong>${pin}</strong>: Express Cold-Chain Available!</span>
                    </div>
                `;
            } else {
                pincodeStatus.innerHTML = `
                    <div class="pincode-error-msg" style="color: #e53935; font-size: 12.5px; font-weight: 600; margin: 6px 0 10px;">
                        ⚠️ Please enter a valid 6-digit Indian PIN code.
                    </div>
                `;
            }
        };

        pincodeBtn.addEventListener('click', checkPin);
        pincodeInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                checkPin();
            }
        });
    }

    // Coupon Code Copy
    const copyCouponBtn = document.getElementById('btn-copy-coupon');
    const couponCodeEl  = document.getElementById('myntra-coupon-code');
    if (copyCouponBtn && couponCodeEl) {
        copyCouponBtn.addEventListener('click', () => {
            const code = couponCodeEl.textContent.trim();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(code).then(() => {
                    const origText = copyCouponBtn.textContent;
                    copyCouponBtn.textContent = 'COPIED!';
                    copyCouponBtn.style.background = '#03a685';
                    if (typeof showBotanicalToast === 'function') {
                        showBotanicalToast(`Coupon ${code} copied! Flat 10% OFF applied`);
                    }
                    setTimeout(() => {
                        copyCouponBtn.textContent = origText;
                        copyCouponBtn.style.background = '';
                    }, 2500);
                }).catch(() => {
                    if (typeof showBotanicalToast === 'function') {
                        showBotanicalToast(`Use coupon ${code} at checkout!`);
                    }
                });
            }
        });
    }

    // Rating Chip smooth scroll to reviews
    const ratingTrigger = document.getElementById('myntra-rating-trigger');
    if (ratingTrigger) {
        ratingTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            const reviewsTab = document.querySelector('.reviews_tab a, a[href="#tab-reviews"]');
            if (reviewsTab) {
                reviewsTab.click();
            }
            const tabsContainer = document.querySelector('.woocommerce-tabs');
            if (tabsContainer) {
                tabsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // =========================================================================
    // 15. Single Product Sticky Footer Bar, Quantity Stepper & Instant Buy Now
    // =========================================================================
    const stickyQtyInput = document.querySelector('.sticky-qty-input');
    const mainQtyInput   = document.querySelector('.single-product form.cart input.qty, .single-product form.cart input[name="quantity"]');
    const btnStickyMinus = document.querySelector('.btn-sticky-minus');
    const btnStickyPlus  = document.querySelector('.btn-sticky-plus');
    const btnStickyAddBag = document.querySelector('.btn-sticky-add-bag');

    // Setup Main PDP Quantity Stepper (Minus & Plus buttons)
    const pdpQtyContainer = document.querySelector('.single-product form.cart .quantity');
    if (pdpQtyContainer && mainQtyInput) {
        if (!pdpQtyContainer.querySelector('.btn-pdp-minus')) {
            const minusBtn = document.createElement('button');
            minusBtn.type = 'button';
            minusBtn.className = 'btn-pdp-qty btn-pdp-minus';
            minusBtn.setAttribute('aria-label', 'Decrease quantity');
            minusBtn.setAttribute('title', 'Decrease quantity');
            minusBtn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line></svg>';
            pdpQtyContainer.insertBefore(minusBtn, mainQtyInput);
        }
        if (!pdpQtyContainer.querySelector('.btn-pdp-plus')) {
            const plusBtn = document.createElement('button');
            plusBtn.type = 'button';
            plusBtn.className = 'btn-pdp-qty btn-pdp-plus';
            plusBtn.setAttribute('aria-label', 'Increase quantity');
            plusBtn.setAttribute('title', 'Increase quantity');
            plusBtn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>';
            pdpQtyContainer.appendChild(plusBtn);
        }

        // Stepper click handlers
        pdpQtyContainer.addEventListener('click', (e) => {
            const minus = e.target.closest('.btn-pdp-minus');
            const plus  = e.target.closest('.btn-pdp-plus');
            if (minus) {
                e.preventDefault();
                let val = parseInt(mainQtyInput.value, 10) || 1;
                if (val > 1) {
                    val--;
                    mainQtyInput.value = val;
                    if (stickyQtyInput) stickyQtyInput.value = val;
                    mainQtyInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            } else if (plus) {
                e.preventDefault();
                let val = parseInt(mainQtyInput.value, 10) || 1;
                if (val < 99) {
                    val++;
                    mainQtyInput.value = val;
                    if (stickyQtyInput) stickyQtyInput.value = val;
                    mainQtyInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
    }

    // Add luxury SVG bag icon to main ADD TO BAG button
    const mainAddCartBtn = document.querySelector('.single-product form.cart .single_add_to_cart_button');
    if (mainAddCartBtn && !mainAddCartBtn.querySelector('.btn-bag-icon')) {
        const bagSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        bagSvg.setAttribute('class', 'btn-bag-icon');
        bagSvg.setAttribute('width', '18');
        bagSvg.setAttribute('height', '18');
        bagSvg.setAttribute('viewBox', '0 0 24 24');
        bagSvg.setAttribute('fill', 'none');
        bagSvg.setAttribute('stroke', 'currentColor');
        bagSvg.setAttribute('stroke-width', '2.2');
        bagSvg.setAttribute('stroke-linecap', 'round');
        bagSvg.setAttribute('stroke-linejoin', 'round');
        bagSvg.innerHTML = '<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path>';
        mainAddCartBtn.insertBefore(bagSvg, mainAddCartBtn.firstChild);
    }

    // Remove any legacy Wishlist buttons from PDP CTA row
    document.querySelectorAll('.single-product .myntra-btn-wishlist, .single-product .btn-wishlist').forEach(btn => {
        btn.remove();
    });

    // Sticky Stepper Decrement
    if (btnStickyMinus && stickyQtyInput) {
        btnStickyMinus.addEventListener('click', (e) => {
            e.preventDefault();
            let val = parseInt(stickyQtyInput.value, 10) || 1;
            if (val > 1) {
                val--;
                stickyQtyInput.value = val;
                if (mainQtyInput) {
                    mainQtyInput.value = val;
                    mainQtyInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
    }

    // Sticky Stepper Increment
    if (btnStickyPlus && stickyQtyInput) {
        btnStickyPlus.addEventListener('click', (e) => {
            e.preventDefault();
            let val = parseInt(stickyQtyInput.value, 10) || 1;
            if (val < 99) {
                val++;
                stickyQtyInput.value = val;
                if (mainQtyInput) {
                    mainQtyInput.value = val;
                    mainQtyInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
    }

    // Bidirectional Quantity Sync
    if (stickyQtyInput) {
        stickyQtyInput.addEventListener('input', () => {
            let val = parseInt(stickyQtyInput.value, 10);
            if (isNaN(val) || val < 1) val = 1;
            if (val > 99) val = 99;
            if (mainQtyInput) {
                mainQtyInput.value = val;
                mainQtyInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }

    if (mainQtyInput && stickyQtyInput) {
        mainQtyInput.addEventListener('input', () => {
            stickyQtyInput.value = mainQtyInput.value;
        });
        mainQtyInput.addEventListener('change', () => {
            stickyQtyInput.value = mainQtyInput.value;
        });
    }

    // Sticky ADD TO BAG Button Click
    if (btnStickyAddBag) {
        btnStickyAddBag.addEventListener('click', (e) => {
            e.preventDefault();
            if (!isPackSizeSelected()) {
                if (typeof window.neebitesOpenOptionsModal === 'function') {
                    window.neebitesOpenOptionsModal('add_to_bag');
                }
                return;
            }
            const mainAddBtn = document.querySelector('.single-product form.cart .single_add_to_cart_button');
            if (mainAddBtn) {
                if (stickyQtyInput && mainQtyInput) {
                    mainQtyInput.value = stickyQtyInput.value;
                }
                const originalHtml = btnStickyAddBag.innerHTML;
                btnStickyAddBag.innerHTML = '<span style="display:inline-block;animation:spin 0.8s linear infinite;">⏳</span> <span>ADDING...</span>';
                btnStickyAddBag.style.pointerEvents = 'none';
                
                mainAddBtn.click();

                setTimeout(() => {
                    btnStickyAddBag.innerHTML = originalHtml;
                    btnStickyAddBag.style.pointerEvents = '';
                }, 1000);
            }
        });
    }

    // Intercept main PDP ADD TO BAG if no pack size selected
    const pdpAddCartBtn = document.querySelector('.single-product form.cart .single_add_to_cart_button');
    if (pdpAddCartBtn) {
        pdpAddCartBtn.addEventListener('click', (e) => {
            if (!isPackSizeSelected()) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof window.neebitesOpenOptionsModal === 'function') {
                    window.neebitesOpenOptionsModal('add_to_bag');
                }
                return false;
            }
        }, true);
    }

    // Instant BUY NOW Buttons (Both Main PDP and Sticky Footer Bar)
    document.addEventListener('click', (e) => {
        const buyNowBtn = e.target.closest('.btn-buy-now, .myntra-btn-buy-now, .btn-sticky-buy-now');
        if (!buyNowBtn) return;

        if (!isPackSizeSelected()) {
            e.preventDefault();
            e.stopPropagation();
            if (typeof window.neebitesOpenOptionsModal === 'function') {
                window.neebitesOpenOptionsModal('buy_now');
            }
            return false;
        }

        let productId = buyNowBtn.dataset.productId;
        const varIdInput = document.querySelector('.single-product form.variations_form input[name="variation_id"]');
        if (varIdInput && parseInt(varIdInput.value, 10) > 0) {
            productId = varIdInput.value;
        } else {
            const activeChip = document.querySelector('.myntra-size-chip.active');
            if (activeChip && activeChip.dataset.variationId) {
                productId = activeChip.dataset.variationId;
            }
        }
        if (!productId) return;

        // Get selected quantity
        let qty = 1;
        if (buyNowBtn.classList.contains('btn-sticky-buy-now') && stickyQtyInput) {
            qty = parseInt(stickyQtyInput.value, 10) || 1;
        } else if (mainQtyInput) {
            qty = parseInt(mainQtyInput.value, 10) || 1;
        }

        // Show immediate feedback
        const origContent = buyNowBtn.innerHTML;
        buyNowBtn.innerHTML = '<span style="display:inline-block;margin-right:6px;">⚡</span><span>CHECKOUT...</span>';
        buyNowBtn.style.opacity = '0.9';
        buyNowBtn.style.pointerEvents = 'none';

        if (typeof showBotanicalToast === 'function') {
            showBotanicalToast('Taking you directly to Checkout! ⚡');
        }

        // Determine checkout destination
        const baseCheckout = ajaxConfig.checkoutUrl || buyNowBtn.dataset.checkoutUrl || '/';
        const sep = baseCheckout.includes('?') ? '&' : '?';

        // Redirect directly to checkout with auto-added product
        window.location.href = `${baseCheckout}${sep}add-to-cart=${encodeURIComponent(productId)}&quantity=${encodeURIComponent(qty)}`;
    });

    // Woodmart Checkout Quantity Stepper Handler
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.woodmart-qty-btn');
        if (!btn) return;
        e.preventDefault();

        const key = btn.dataset.key;
        const isPlus = btn.classList.contains('plus');
        const container = btn.closest('.woodmart-qty-stepper-box');
        if (!container) return;
        const valSpan = container.querySelector('.woodmart-qty-val');
        if (!valSpan) return;

        let currentQty = parseInt(valSpan.textContent.trim(), 10) || 1;
        let newQty = isPlus ? currentQty + 1 : Math.max(1, currentQty - 1);
        if (newQty === currentQty) return;

        valSpan.textContent = newQty;

        if (typeof jQuery !== 'undefined' && ajaxConfig.ajaxurl) {
            jQuery.ajax({
                url: ajaxConfig.ajaxurl,
                type: 'POST',
                data: {
                    action: 'neebites_update_checkout_qty',
                    cart_item_key: key,
                    quantity: newQty,
                    nonce: ajaxConfig.nonce
                },
                success: function() {
                    jQuery(document.body).trigger('update_checkout');
                }
            });
        }
    });

    // =========================================================================
    // 16. Myntra-Inspired Luxury Single Product Gallery Controller
    // =========================================================================
    function initMyntraProductGallery() {
        const gallery = document.querySelector('.myntra-gallery-container');
        if (!gallery) return;

        const slides = Array.from(gallery.querySelectorAll('.myntra-slide-item'));
        const thumbs = Array.from(gallery.querySelectorAll('.myntra-thumb-card'));
        const dots   = Array.from(gallery.querySelectorAll('.myntra-dot'));
        const track  = gallery.querySelector('.myntra-slides-track');
        const counterCurrent = gallery.querySelector('#myntra-counter-pill .current-slide');
        const zoomBtn = gallery.querySelector('#myntra-gallery-zoom-trigger');
        const modal   = document.getElementById('myntra-gallery-modal');
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
        const modalImg = document.getElementById('myntra-modal-img');
        const modalCounter = document.getElementById('myntra-modal-counter');
        const modalClose = document.getElementById('myntra-modal-close');
        const modalBackdrop = document.getElementById('myntra-modal-backdrop');
        const modalPrev = document.getElementById('myntra-modal-prev');
        const modalNext = document.getElementById('myntra-modal-next');

        let activeIndex = 0;
        const total = slides.length;
        if (total === 0) return;

        function goToSlide(index, smoothScroll) {
            if (smoothScroll === undefined) smoothScroll = true;
            if (index < 0) index = total - 1;
            if (index >= total) index = 0;
            activeIndex = index;

            // 1. Update Desktop Slides
            slides.forEach((slide, i) => {
                slide.classList.toggle('is-active', i === index);
            });

            // 2. Update Thumbnails
            thumbs.forEach((thumb, i) => {
                thumb.classList.toggle('is-active', i === index);
            });

            // 3. Update Dots
            dots.forEach((dot, i) => {
                dot.classList.toggle('is-active', i === index);
            });

            // 4. Update Counter Pill
            if (counterCurrent) {
                counterCurrent.textContent = index + 1;
            }

            // 5. Mobile Horizontal Scroll Snap
            if (track && window.innerWidth <= 860) {
                const targetSlide = slides[index];
                if (targetSlide) {
                    const scrollLeft = targetSlide.offsetLeft;
                    if (smoothScroll) {
                        track.scrollTo({ left: scrollLeft, behavior: 'smooth' });
                    } else {
                        track.scrollLeft = scrollLeft;
                    }
                }
            }
        }

        // Thumbnail Click & Hover (Desktop)
        thumbs.forEach((thumb, i) => {
            thumb.addEventListener('click', (e) => {
                e.preventDefault();
                goToSlide(i, true);
            });
            thumb.addEventListener('mouseenter', () => {
                if (window.innerWidth > 860) {
                    goToSlide(i, false);
                }
            });
        });

        // Dots Click (Mobile & Desktop)
        dots.forEach((dot, i) => {
            dot.addEventListener('click', (e) => {
                e.preventDefault();
                goToSlide(i, true);
            });
        });

        // Mobile Horizontal Scroll Listener (Touch Swipe)
        if (track && total > 1) {
            let isScrolling;
            track.addEventListener('scroll', () => {
                if (window.innerWidth > 860) return;
                clearTimeout(isScrolling);
                isScrolling = setTimeout(() => {
                    const scrollPos = track.scrollLeft;
                    const width = track.offsetWidth || 1;
                    const newIndex = Math.round(scrollPos / width);
                    if (newIndex !== activeIndex && newIndex >= 0 && newIndex < total) {
                        activeIndex = newIndex;
                        slides.forEach((s, i) => s.classList.toggle('is-active', i === activeIndex));
                        thumbs.forEach((t, i) => t.classList.toggle('is-active', i === activeIndex));
                        dots.forEach((d, i) => d.classList.toggle('is-active', i === activeIndex));
                        if (counterCurrent) counterCurrent.textContent = activeIndex + 1;
                    }
                }, 50);
            }, { passive: true });
        }

        // Desktop Hover Zoom (Fluid Cursor Follower)
        slides.forEach(slide => {
            const imgWrap = slide.querySelector('.myntra-img-wrap');
            const img = slide.querySelector('.myntra-gallery-img');
            if (!imgWrap || !img) return;

            imgWrap.addEventListener('mousemove', (e) => {
                if (window.innerWidth <= 860) return;
                const rect = imgWrap.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                img.style.transformOrigin = `${x}% ${y}%`;
                img.style.transform = 'scale(1.35)';
            });

            imgWrap.addEventListener('mouseleave', () => {
                img.style.transform = '';
                img.style.transformOrigin = 'center center';
            });

            // Click to Open Modal Lightbox
            imgWrap.addEventListener('click', () => {
                openLightbox(activeIndex);
            });
        });

        // Lightbox Functions
        function openLightbox(index) {
            if (!modal || !modalImg) return;
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            activeIndex = index;
            const currentSlide = slides[activeIndex];
            const fullSrc = currentSlide ? currentSlide.dataset.fullImage : '';
            if (fullSrc) modalImg.src = fullSrc;
            if (modalCounter) modalCounter.textContent = `${activeIndex + 1} / ${total}`;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('myntra-modal-open');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            if (!modal) return;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('myntra-modal-open');
            document.body.style.overflow = '';
        }

        function stepLightbox(dir) {
            let next = activeIndex + dir;
            if (next < 0) next = total - 1;
            if (next >= total) next = 0;
            openLightbox(next);
            goToSlide(next, false);
        }

        if (zoomBtn) {
            zoomBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openLightbox(activeIndex);
            });
        }
        if (modalClose) modalClose.addEventListener('click', closeLightbox);
        if (modalBackdrop) modalBackdrop.addEventListener('click', closeLightbox);
        if (modalPrev) modalPrev.addEventListener('click', () => stepLightbox(-1));
        if (modalNext) modalNext.addEventListener('click', () => stepLightbox(1));

        document.addEventListener('keydown', (e) => {
            if (!modal || !modal.classList.contains('is-open')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') stepLightbox(-1);
            if (e.key === 'ArrowRight') stepLightbox(1);
        });
    }

    initMyntraProductGallery();
});


