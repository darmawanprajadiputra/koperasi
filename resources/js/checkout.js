(function () {
    // ── Helpers ───────────────────────────────────────────────────────────────
    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(value);
    }

    // ── Cart ──────────────────────────────────────────────────────────────────
    function loadCartFromSession() {
        let cart = {};
        try {
            const raw = sessionStorage.getItem('pendingCart');
            if (raw) cart = JSON.parse(raw);
        } catch (e) {
            console.warn('[CHECKOUT] Could not parse cart from sessionStorage');
        }
        return cart;
    }

    // ── Render ringkasan pesanan ──────────────────────────────────────────────
    function renderOrderSummary(cart) {
        const list       = document.getElementById('orderItemsList');
        const emptyMsg   = document.getElementById('cartEmptyMsg');
        const subtotalEl = document.getElementById('summarySubtotal');
        const totalEl    = document.getElementById('summaryTotal');
        const totalInput = document.getElementById('totalAmountInput');
        const cartInput  = document.getElementById('cartItemsInput');
        const submitBtn  = document.getElementById('submitBtn');

        const items = Object.entries(cart);

        if (items.length === 0) {
            emptyMsg?.classList.remove('hidden');
            submitBtn?.setAttribute('disabled', 'true');
            return;
        }

        emptyMsg?.classList.add('hidden');

        let total = 0;

        const html = items.map(([id, item]) => {
            const lineTotal = item.price * item.qty;
            total += lineTotal;
            return `
      <div class="flex items-start gap-3">
        ${item.image
            ? `<img src="${item.image}" alt="${item.name}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0 bg-surface-container" />`
            : `<div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center flex-shrink-0">
                 <span class="material-symbols-outlined text-on-surface-variant text-lg">inventory_2</span>
               </div>`
        }
        <div class="flex-1 min-w-0">
          <p class="text-sm font-bold text-on-surface truncate">${item.name}</p>
          <p class="text-xs text-on-surface-variant">${item.qty} × ${formatRp(item.price)}</p>
        </div>
        <span class="text-sm font-bold text-on-surface flex-shrink-0">${formatRp(lineTotal)}</span>
      </div>
    `;
        }).join('');

        list.innerHTML        = html;
        subtotalEl.textContent = formatRp(total);
        totalEl.textContent    = formatRp(total);
        totalInput.value       = total;
        cartInput.value        = JSON.stringify(cart);
    }

    // ── Setup kartu pembayaran ────────────────────────────────────────────────
    function setupPaymentCards() {
        const cards = document.querySelectorAll('.payment-card');

        cards.forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            const check = card.querySelector('.check-icon');

            // Sync initial state
            if (radio.checked) {
                card.classList.add('ring-2', 'ring-primary', 'bg-surface-container-lowest');
                card.classList.remove('bg-surface-container-highest/50');
                check?.classList.remove('hidden');
            }

            card.addEventListener('click', () => {
                // Reset semua kartu
                cards.forEach(c => {
                    c.classList.remove('ring-2', 'ring-primary', 'bg-surface-container-lowest');
                    c.classList.add('bg-surface-container-highest/50');
                    c.querySelector('.check-icon')?.classList.add('hidden');
                    c.querySelector('input[type="radio"]').checked = false;

                    c.querySelectorAll('.font-bold').forEach(el => {
                        el.classList.remove('text-on-surface');
                        el.classList.add('text-on-surface-variant');
                    });
                    c.querySelector('.material-symbols-outlined:first-child')?.classList
                        .replace('text-primary', 'text-on-surface-variant');
                });

                // Aktifkan kartu yang diklik
                radio.checked = true;
                card.classList.add('ring-2', 'ring-primary', 'bg-surface-container-lowest');
                card.classList.remove('bg-surface-container-highest/50');
                check?.classList.remove('hidden');
                card.querySelectorAll('.font-bold').forEach(el => {
                    el.classList.add('text-on-surface');
                    el.classList.remove('text-on-surface-variant');
                });
            });
        });
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const cart = loadCartFromSession();
        renderOrderSummary(cart);
        setupPaymentCards();
    });
})();