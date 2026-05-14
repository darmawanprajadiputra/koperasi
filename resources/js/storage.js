/**
 * Storage/Gudang Management Module
 * Handles inventory management page interactions
 */

const inventoryData = [
    {
        id: 1,
        name: 'Susu Sapi Segar',
        category: 'Dairy',
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDHq6SMR3OslcOzZvtot4eyoQSMo3RQ8Sljlqsg_SlW047__jz3NliJTcdpiEFOPF_G9QOyCSJPEGgVOQdOfb2ESxwQO89A63l03NySkBXtrY-bb11Vt6QTgBytY6lzUXPOPXyUrIOdH6kS4CQIc86SqxASR8DaX1hFUdmayXH9mevMLDzfrcmfjWna7-QqUr0aC4AzZKkvcnwRnwUIPUGzuZPDITJ3mzCU_TzAERHOsxwsUaJy0mCJe9n6HJQTq_-NUYpDDtuZVc6h',
        stock: 142,
        unit: 'units',
        price: 18500,
        status: 'in-stock'
    },
    {
        id: 2,
        name: 'Buah Apel Malang',
        category: 'Buah',
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCi3s2zmol5dBZaxe_cSlInuemOuXfP3hxv7iNcJ64FxRuihn4n6JjEmnX0uSxrnALAO8cCl232y1wO6UJeFUGNQaGDES_9h3p0YS2dgXTYO2aAHWZsO27_zazVsMAebHz1f3Sqbsg-s_i3PQDyNID96uAXYW3lFPjJPmSAjD8xF7Zt3YDlzb95W9Bw04SM-OaNixgGZOs5xFlRHVAMltbnW4dj8Ff370L4LYY0v4FSWNevdmw75xqFxGgwm1G_ycpPdpFf4QNilCOU',
        stock: 85,
        unit: 'kg',
        price: 24000,
        status: 'in-stock'
    },
    {
        id: 3,
        name: 'Sayur Selada Hidroponik',
        category: 'Sayur',
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA7QIx55RqP7ZUVhRW5fapIbrEkhsQo_bgeogC4i9Qj3kBbBhRZZRd-_bs5BEAebAJuoLcQmjPO7cfeojEtHDmXHoV9Mug86ymJhiM1R6aHfDFBHr67pY1hmNcYJlZWKmH7lVQO8GpXGISM_bxSBgmw7r6BXFx6qOwQjUWI-LXJ7AdRTcbDk8mQkETd6e9jVVKC9CKYVwT4JRFzVTjSXugqA5XZuv93n1P-4iA__eTbuWPcNsdWVbLvSncgII8EBX0cXiSKTlwWDO3A',
        stock: 0,
        unit: 'units',
        price: 12000,
        status: 'out-of-stock'
    },
    {
        id: 4,
        name: 'Sayur Bayam Organik',
        category: 'Sayur',
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuArZiMm-Nf9nX4g4KNj7DdHcr9tLpbkOv6HUjCL2K6rZtDV5bYHl4ro_Lwz5V8Zv2iQquBynKkYh5jqkyPYdDareX8eByUsRxfOaRyNmsiYdoAjfF3MPlyucR-OuyAXpHCBDJ2yoCT8XHhEZciaez04LFRWAPiZBa-yDGDrgWtttEx90j9sDDmN-6w1dqCWOOkqyANbvqCuBf2DBNOi5rGGT4Q9yLSZvg5uJFxHn8D1MNqM_PXVFGjdfgAn2RJGTFaaB3q5dqzx1lB-',
        stock: 50,
        unit: 'ikat',
        price: 8500,
        status: 'in-stock'
    },
    {
        id: 5,
        name: 'Wortel Organik Premium',
        category: 'Sayur',
        stock: 120,
        unit: 'kg',
        price: 15000,
        status: 'in-stock'
    },
    {
        id: 6,
        name: 'Beras Merah Organik',
        category: 'Biji-bijian',
        stock: 5,
        unit: 'karung',
        price: 45000,
        status: 'in-stock'
    },
    {
        id: 7,
        name: 'Telur Ayam Kampung',
        category: 'Protein',
        stock: 300,
        unit: 'butir',
        price: 2500,
        status: 'in-stock'
    },
    {
        id: 8,
        name: 'Madu Murni Asli',
        category: 'Produk Olahan',
        stock: 30,
        unit: 'botol',
        price: 75000,
        status: 'in-stock'
    }
];

// ─── Blade-injected config (diisi oleh storage.blade.php via window.StorageConfig) ───
// Pastikan blade meng-inject window.StorageConfig SEBELUM file ini di-load.
// Contoh di blade:
//   <script>
//     window.StorageConfig = {
//       allProducts : @json($products),
//       storageUrl  : '{{ asset("storage") }}',
//       defaultImg  : '/assets/pictures/produk.jpg',
//       editBaseUrl : '{{ url("product") }}',
//       deleteBase  : '{{ url("product") }}',
//       toggleBase  : '{{ url("product") }}',
//       csrfToken   : '{{ csrf_token() }}',
//     };
//   </script>
//   <script src="{{ asset('js/storage.js') }}"></script>

const cfg = (typeof window.StorageConfig !== 'undefined') ? window.StorageConfig : null;

// ─── Shared state ────────────────────────────────────────────────────────────
let currentPage = 1;
const itemsPerPage = 4;          // dipakai oleh modul inventoryData (dummy)
const PER_PAGE    = 10;          // dipakai oleh modul allProducts (dari Blade)
let filteredData  = [];
let activeProduct = null;

// ─── Helpers ─────────────────────────────────────────────────────────────────

/**
 * Format angka ke format Rupiah (dipakai keduanya)
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);
}

function formatRupiah(v) {
    return 'Rp ' + Number(v).toLocaleString('id-ID');
}

function getStatusBadge(isActive) {
    return isActive
        ? '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aktif</span>'
        : '<span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-semibold">Nonaktif</span>';
}

function getImageSrc(image) {
    if (!cfg) return 'https://via.placeholder.com/48';
    if (!image) return cfg.defaultImg;
    return cfg.storageUrl + '/' + image;
}

// ─── Modul A: Blade products (allProducts dari server) ───────────────────────

function initBladeProducts() {
    if (!cfg || !cfg.allProducts) return false;

    const allProducts = cfg.allProducts;
    filteredData = [...allProducts];

    const tableBody     = document.getElementById('tableBody');
    const paginationInfo = document.getElementById('paginationInfo');
    const prevBtn       = document.getElementById('prevBtn');
    const nextBtn       = document.getElementById('nextBtn');
    const emptyState    = document.getElementById('emptyState');
    const searchInput   = document.getElementById('searchInput');
    const actionMenu    = document.getElementById('actionMenu');

    if (!tableBody) return false;

    function render() {
        const total      = filteredData.length;
        const totalPages = Math.ceil(total / PER_PAGE) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const start    = (currentPage - 1) * PER_PAGE;
        const pageData = filteredData.slice(start, start + PER_PAGE);

        if (pageData.length === 0) {
            tableBody.innerHTML = '';
            if (emptyState) emptyState.style.display = 'block';
        } else {
            if (emptyState) emptyState.style.display = 'none';
            tableBody.innerHTML = pageData.map(p => `
                <tr class="hover:bg-gray-50 transition-colors" data-id="${p.id}">
                    <td class="px-4 py-3">
                        <img src="${getImageSrc(p.image)}"
                             onerror="this.src='${cfg.defaultImg}'"
                             alt="${p.name_product}"
                             class="w-12 h-12 object-cover rounded-lg border border-gray-200" />
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">${p.name_product}</td>
                    <td class="px-4 py-3 text-gray-600">${p.category ? p.category.name_categories : '-'}</td>
                    <td class="px-4 py-3 text-right text-gray-700">
                        ${p.stock}${p.unit ? ' <span class="text-gray-400 text-xs font-medium">' + p.unit.name_unit + '</span>' : ''}
                    </td>
                    <td class="px-4 py-3 text-right text-gray-700">${formatRupiah(p.price)}</td>
                    <td class="px-4 py-3">${getStatusBadge(p.is_active)}</td>
                    <td class="px-4 py-3 text-center">
                        <button class="action-btn p-1 rounded-full hover:bg-gray-100 transition-colors"
                                data-id="${p.id}" title="Aksi">
                            <span class="material-symbols-outlined text-gray-500" style="font-size:20px">more_vert</span>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        const showing = total === 0 ? '0' : `${start + 1}–${Math.min(start + PER_PAGE, total)}`;
        if (paginationInfo) paginationInfo.textContent = `Menampilkan ${showing} dari ${total} produk`;
        if (prevBtn) prevBtn.disabled = currentPage <= 1;
        if (nextBtn) nextBtn.disabled = currentPage >= totalPages || total === 0;
    }

    // Search
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.toLowerCase().trim();
            filteredData = allProducts.filter(p =>
                p.name_product.toLowerCase().includes(q) ||
                (p.category && p.category.name_categories.toLowerCase().includes(q)) ||
                (p.unit && p.unit.name_unit.toLowerCase().includes(q))
            );
            currentPage = 1;
            render();
        });
    }

    if (prevBtn) prevBtn.addEventListener('click', () => { currentPage--; render(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { currentPage++; render(); });

    // ── Action Menu ──────────────────────────────────────────────────────────
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.action-btn');

        if (btn) {
            e.stopPropagation();
            const id = parseInt(btn.dataset.id);
            activeProduct = allProducts.find(p => p.id === id);

            const toggleIcon  = document.getElementById('menuToggleIcon');
            const toggleLabel = document.getElementById('menuToggleLabel');
            if (toggleIcon)  toggleIcon.textContent  = activeProduct.is_active ? 'toggle_off' : 'toggle_on';
            if (toggleLabel) toggleLabel.textContent = activeProduct.is_active ? 'Nonaktifkan' : 'Aktifkan';

            // Posisi menu — gunakan koordinat viewport (fixed positioning, tanpa scrollY/scrollX)
            actionMenu.style.visibility = 'hidden';
            actionMenu.classList.remove('hidden');

            const rect       = btn.getBoundingClientRect();
            const menuH      = actionMenu.offsetHeight;
            const menuW      = actionMenu.offsetWidth;
            const spaceBelow = window.innerHeight - rect.bottom;

            const top  = spaceBelow < menuH + 8
                ? rect.top  - menuH - 4          // tampilkan di atas tombol
                : rect.bottom + 4;               // tampilkan di bawah tombol

            const left = Math.min(
                rect.right - menuW,              // rata kanan tombol
                window.innerWidth - menuW - 8    // jangan keluar kanan layar
            );

            actionMenu.style.top  = top + 'px';
            actionMenu.style.left = Math.max(8, left) + 'px';
            actionMenu.style.visibility = 'visible';
            return;
        }

        // Klik di luar → tutup menu
        if (actionMenu) actionMenu.classList.add('hidden');
    });

    const menuEdit = document.getElementById('menuEdit');
    if (menuEdit) {
        menuEdit.addEventListener('click', () => {
            if (!activeProduct) return;
            window.location.href = `${cfg.editBaseUrl}/${activeProduct.id}/edit`;
        });
    }

    const menuToggle = document.getElementById('menuToggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            if (!activeProduct) return;
            if (actionMenu) actionMenu.classList.add('hidden');
            const form = document.getElementById('toggleForm');
            form.action = `${cfg.toggleBase}/${activeProduct.id}/toggle`;
            form.submit();
        });
    }

    const menuDelete = document.getElementById('menuDelete');
    if (menuDelete) {
        menuDelete.addEventListener('click', () => {
            if (!activeProduct) return;
            if (!confirm(`Hapus produk "${activeProduct.name_product}"?`)) return;
            if (actionMenu) actionMenu.classList.add('hidden');
            const form = document.getElementById('deleteForm');
            form.action = `${cfg.deleteBase}/${activeProduct.id}`;
            form.submit();
        });
    }

    render();
    return true;
}

// ─── Modul B: Dummy inventoryData (fallback / halaman lain) ──────────────────

function initDummyStorage() {
    const storageContainer = document.querySelector('.storage-container');
    if (!storageContainer) return false;

    console.log('[v0] Initializing storage page (dummy data)...');

    filteredData = [...inventoryData];
    renderDummyTable();

    const searchInput = document.getElementById('storageSearch');
    if (searchInput) searchInput.addEventListener('input', handleSearch);

    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderDummyTable();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const maxPage = Math.ceil(filteredData.length / itemsPerPage);
            if (currentPage < maxPage) {
                currentPage++;
                renderDummyTable();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    const addBtn  = document.querySelector('.storage-btn-add');
    const editBtn = document.querySelector('.storage-btn-edit');
    if (addBtn)  addBtn.addEventListener('click',  () => console.log('[v0] Tambah Stok clicked'));
    if (editBtn) editBtn.addEventListener('click', () => console.log('[v0] Edit Item clicked'));

    document.querySelectorAll('.btn-filter').forEach(btn => {
        btn.addEventListener('click', function () {
            console.log('[v0] Filter clicked:', this.textContent.trim());
        });
    });

    return true;
}

function renderDummyTable() {
    const tableBody = document.getElementById('storageTableBody');
    if (!tableBody) return;

    tableBody.innerHTML = '';

    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex   = startIndex + itemsPerPage;
    const pageData   = filteredData.slice(startIndex, endIndex);

    pageData.forEach(item => {
        const row         = document.createElement('tr');
        const statusClass = item.status === 'in-stock' ? 'in-stock' : 'out-of-stock';
        const statusText  = item.status === 'in-stock' ? 'In Stock' : 'Out of Stock';

        row.innerHTML = `
            <td>
                <div class="item-cell">
                    <img src="${item.image || 'https://via.placeholder.com/48'}" alt="${item.name}" class="item-image" onerror="this.src='https://via.placeholder.com/48'">
                    <span class="item-name">${item.name}</span>
                </div>
            </td>
            <td><span class="category-badge">${item.category}</span></td>
            <td class="text-right"><span class="item-stock">${item.stock} ${item.unit}</span></td>
            <td class="text-right"><strong>${formatCurrency(item.price)}</strong></td>
            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
            <td class="text-center">
                <button class="action-btn" data-item-id="${item.id}" title="More options">⋮</button>
            </td>
        `;
        tableBody.appendChild(row);
    });

    updateDummyPagination();
}

function updateDummyPagination() {
    const totalItems = filteredData.length;
    const maxPage    = Math.ceil(totalItems / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex   = Math.min(currentPage * itemsPerPage, totalItems);

    const paginationInfo = document.getElementById('paginationInfo');
    if (paginationInfo) {
        paginationInfo.textContent = `Menampilkan ${startIndex} hingga ${endIndex} dari ${totalItems} items`;
    }

    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    if (prevBtn) prevBtn.disabled = currentPage === 1;
    if (nextBtn) nextBtn.disabled = currentPage === maxPage;
}

function handleSearch(event) {
    const searchValue = event.target.value.toLowerCase();
    filteredData = inventoryData.filter(item =>
        item.name.toLowerCase().includes(searchValue) ||
        item.category.toLowerCase().includes(searchValue) ||
        item.status.toLowerCase().includes(searchValue)
    );
    currentPage = 1;
    renderDummyTable();
}

// ─── Entry point ─────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {
    console.log('[v0] Storage module loaded');

    const bladeReady = initBladeProducts();
    if (!bladeReady) {
        initDummyStorage();
    }
});