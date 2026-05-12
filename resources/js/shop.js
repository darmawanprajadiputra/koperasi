// Shop Page JavaScript

// ─── Cart State ───────────────────────────────────────────────────────────────
const cart = {}; // { productId: { name, price, qty } }

// ─── Utility ──────────────────────────────────────────────────────────────────
function formatCurrency(value) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(value);
}

// ─── Product Card ─────────────────────────────────────────────────────────────
function createProductCard(product) {
  return `
    <div class="product-card bg-surface-container-lowest rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-[0_12px_32px_rgba(25,28,27,0.06)] flex flex-col"
         data-id="${product.id}"
         data-name="${product.name_product}"
         data-price="${product.price}"
         data-stock="${product.stock}"
         data-image="${product.image || ''}">
      <div class="relative h-64 overflow-hidden bg-surface-container-low">
        <img src="${product.image || '/assets/pictures/produk.jpg'}"
             alt="${product.name_product}"
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
             onerror="this.src='/assets/pictures/produk.jpg'" />
        <div class="absolute top-4 left-4">
          <span class="bg-secondary text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">${product.category || 'Produk'}</span>
        </div>
      </div>
      <div class="p-6 flex flex-col flex-1">
        <div class="flex justify-between items-start mb-2">
          <h3 class="font-manrope font-bold text-xl text-primary product-name">${product.name_product}</h3>
          <span class="font-manrope font-extrabold text-lg text-secondary">${formatCurrency(product.price)}</span>
        </div>
        <p class="text-sm text-on-surface-variant mb-6 line-clamp-2">${product.description || ''}</p>
        <div class="mt-auto">
          <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-semibold text-secondary flex items-center gap-1">
              <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1">check_circle</span>
              Stok: ${product.stock}
            </span>
            <div class="quantity-selector flex items-center bg-surface-container-low rounded-full px-2 py-1">
              <button class="qty-minus p-1 hover:text-primary transition-colors" type="button">
                <span class="material-symbols-outlined text-sm">remove</span>
              </button>
              <span class="qty-display px-3 text-sm font-bold">1</span>
              <button class="qty-plus p-1 hover:text-primary transition-colors" type="button">
                <span class="material-symbols-outlined text-sm">add</span>
              </button>
            </div>
          </div>
          <button class="add-to-cart w-full py-3 bg-gradient-to-br from-[#00342b] to-[#004d40] text-white rounded-full font-bold text-sm flex items-center justify-center gap-2 group-hover:opacity-90 transition-all active:scale-95" type="button">
            <span class="material-symbols-outlined text-lg">shopping_basket</span>
            Tambah ke Keranjang
          </button>
        </div>
      </div>
    </div>
  `;
}

// ─── Load Products ────────────────────────────────────────────────────────────
async function loadShopProducts() {
  const loadingSpinner    = document.getElementById('loadingSpinner');
  const emptyState        = document.getElementById('emptyState');
  const productsContainer = document.getElementById('productsContainer');

  try {
    const response = await fetch('/api/get_products');
    const data     = await response.json();

    loadingSpinner?.classList.add('hidden');

    if (data.success && data.products.length > 0) {
      productsContainer.innerHTML = data.products.map(createProductCard).join('');
      productsContainer.classList.remove('hidden');
      emptyState?.classList.add('hidden');

      buildCategoryFilter(data.products);
      setupQuantityHandlers();
      setupAddToCartHandlers();
    } else {
      emptyState?.classList.remove('hidden');
    }
  } catch (error) {
    console.error('[SHOP] Failed to load products:', error);
    loadingSpinner?.classList.add('hidden');
    emptyState?.classList.remove('hidden');
  }
}

// ─── Category Filter ──────────────────────────────────────────────────────────
function buildCategoryFilter(products) {
  const filterEl   = document.getElementById('categoryFilter');
  const categories = [...new Set(products.map(p => p.category).filter(Boolean))];

  // Keep "Semua Produk" badge, add others
  categories.forEach(cat => {
    const span = document.createElement('span');
    span.className   = 'category-badge px-6 py-2 bg-surface-container-high text-on-surface-variant rounded-full text-sm font-medium cursor-pointer hover:bg-surface-container-highest transition-colors';
    span.dataset.category = cat;
    span.textContent = cat;
    filterEl.appendChild(span);
  });

  setupCategoryHandlers();
}

function setupCategoryHandlers() {
  const badges    = document.querySelectorAll('.category-badge');
  const container = document.getElementById('productsContainer');

  badges.forEach(badge => {
    badge.addEventListener('click', () => {
      badges.forEach(b => {
        b.classList.remove('bg-tertiary', 'text-on-tertiary');
        b.classList.add('bg-surface-container-high', 'text-on-surface-variant');
      });
      badge.classList.add('bg-tertiary', 'text-on-tertiary');
      badge.classList.remove('bg-surface-container-high', 'text-on-surface-variant');

      const selected = badge.dataset.category;
      container.querySelectorAll('.product-card').forEach(card => {
        if (selected === 'all') {
          card.style.display = '';
        } else {
          // match category from the badge text inside the card
          const cardCat = card.querySelector('.bg-secondary')?.textContent?.trim().toLowerCase();
          card.style.display = cardCat === selected.toLowerCase() ? '' : 'none';
        }
      });
    });
  });

  // Activate "Semua Produk" by default
  badges[0]?.click();
}

// ─── Quantity Handlers ────────────────────────────────────────────────────────
function setupQuantityHandlers() {
  document.querySelectorAll('.quantity-selector').forEach(selector => {
    const minus   = selector.querySelector('.qty-minus');
    const plus    = selector.querySelector('.qty-plus');
    const display = selector.querySelector('.qty-display');
    const card    = selector.closest('.product-card');

    minus?.addEventListener('click', () => {
      let qty = parseInt(display.textContent);
      if (qty > 1) display.textContent = qty - 1;
    });

    plus?.addEventListener('click', () => {
      let qty     = parseInt(display.textContent);
      const stock = parseInt(card?.dataset.stock ?? 999);
      if (qty < stock) display.textContent = qty + 1;
    });
  });
}

// ─── Add to Cart Handlers ─────────────────────────────────────────────────────
function setupAddToCartHandlers() {
  document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', () => {
      const card  = btn.closest('.product-card');
      const id    = card.dataset.id;
      const name  = card.dataset.name;
      const price = parseFloat(card.dataset.price);
      const image = card.dataset.image || '';
      const qty   = parseInt(card.querySelector('.qty-display').textContent);

      // Update local cart state
      if (cart[id]) {
        cart[id].qty += qty;
      } else {
        cart[id] = { name, price, qty, image };
      }

      // Button feedback
      const originalHTML = btn.innerHTML;
      btn.innerHTML = '<span class="material-symbols-outlined text-lg" style="font-variation-settings: \'FILL\' 1">check_circle</span> Ditambahkan!';
      btn.classList.add('opacity-80');
      setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('opacity-80');
      }, 1800);

      updateFloatingCart();
      showToast(`${name} ditambahkan ke keranjang`);
    });
  });
}

// ─── Floating Cart ────────────────────────────────────────────────────────────
function updateFloatingCart() {
  const floatingCart = document.getElementById('floatingCart');
  const cartBadge    = document.getElementById('cartBadge');
  const cartTotal    = document.getElementById('cartTotal');

  const totalQty   = Object.values(cart).reduce((s, i) => s + i.qty, 0);
  const totalPrice = Object.values(cart).reduce((s, i) => s + i.price * i.qty, 0);

  if (totalQty > 0) {
    floatingCart.classList.remove('hidden');
    floatingCart.classList.add('flex');
    cartBadge.textContent = totalQty;
    cartTotal.textContent = formatCurrency(totalPrice);
  } else {
    floatingCart.classList.add('hidden');
    floatingCart.classList.remove('flex');
  }
}

function goToCheckout() {
  sessionStorage.setItem('pendingCart', JSON.stringify(cart));
  window.location.href = '/checkout';
}

// Expose ke global scope agar onclick="goToCheckout()" di blade bisa memanggil
window.goToCheckout = goToCheckout;

// ─── Toast ────────────────────────────────────────────────────────────────────
let toastTimeout;
function showToast(message) {
  const toast   = document.getElementById('toast');
  const msgEl   = document.getElementById('toastMessage');
  msgEl.textContent = message;
  toast.classList.remove('hidden');

  clearTimeout(toastTimeout);
  toastTimeout = setTimeout(() => toast.classList.add('hidden'), 2500);
}

// ─── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  loadShopProducts();
});