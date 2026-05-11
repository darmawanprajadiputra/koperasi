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

let currentPage = 1;
const itemsPerPage = 4;
let filteredData = [...inventoryData];

// Initialize storage module when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('[v0] Storage module loaded');
    initializeStorage();
});

/**
 * Initialize storage page features
 */
function initializeStorage() {
    const storageContainer = document.querySelector('.storage-container');
    if (!storageContainer) return;

    console.log('[v0] Initializing storage page...');
    
    // Render initial table
    renderTable();

    // Search input listener
    const searchInput = document.getElementById('storageSearch');
    if (searchInput) {
        searchInput.addEventListener('input', handleSearch);
    }

    // Pagination buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            const maxPage = Math.ceil(filteredData.length / itemsPerPage);
            if (currentPage < maxPage) {
                currentPage++;
                renderTable();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    // Action buttons
    const addBtn = document.querySelector('.storage-btn-add');
    const editBtn = document.querySelector('.storage-btn-edit');

    if (addBtn) {
        addBtn.addEventListener('click', function() {
            console.log('[v0] Tambah Stok clicked');
        });
    }

    if (editBtn) {
        editBtn.addEventListener('click', function() {
            console.log('[v0] Edit Item clicked');
        });
    }

    // Filter button handlers
    const filterButtons = document.querySelectorAll('.btn-filter');
    filterButtons.forEach((btn) => {
        btn.addEventListener('click', function() {
            console.log('[v0] Filter clicked:', this.textContent.trim());
        });
    });
}

/**
 * Format currency to IDR
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);
}

/**
 * Render table with current page data
 */
function renderTable() {
    const tableBody = document.getElementById('storageTableBody');
    if (!tableBody) return;

    tableBody.innerHTML = '';

    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageData = filteredData.slice(startIndex, endIndex);

    pageData.forEach((item) => {
        const row = document.createElement('tr');
        const statusClass = item.status === 'in-stock' ? 'in-stock' : 'out-of-stock';
        const statusText = item.status === 'in-stock' ? 'In Stock' : 'Out of Stock';

        row.innerHTML = `
            <td>
                <div class="item-cell">
                    <img src="${item.image || 'https://via.placeholder.com/48'}" alt="${item.name}" class="item-image" onerror="this.src='https://via.placeholder.com/48'">
                    <span class="item-name">${item.name}</span>
                </div>
            </td>
            <td>
                <span class="category-badge">${item.category}</span>
            </td>
            <td class="text-right">
                <span class="item-stock">${item.stock} ${item.unit}</span>
            </td>
            <td class="text-right">
                <strong>${formatCurrency(item.price)}</strong>
            </td>
            <td>
                <span class="status-badge ${statusClass}">${statusText}</span>
            </td>
            <td class="text-center">
                <button class="action-btn" data-item-id="${item.id}" title="More options">⋮</button>
            </td>
        `;
        tableBody.appendChild(row);
    });

    updatePagination();
}

/**
 * Update pagination info and button states
 */
function updatePagination() {
    const totalItems = filteredData.length;
    const maxPage = Math.ceil(totalItems / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(currentPage * itemsPerPage, totalItems);

    const paginationInfo = document.getElementById('paginationInfo');
    if (paginationInfo) {
        paginationInfo.textContent = 
            `Menampilkan ${startIndex} hingga ${endIndex} dari ${totalItems} items`;
    }

    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (prevBtn) prevBtn.disabled = currentPage === 1;
    if (nextBtn) nextBtn.disabled = currentPage === maxPage;
}

/**
 * Handle search input
 */
function handleSearch(event) {
    const searchValue = event.target.value.toLowerCase();

    filteredData = inventoryData.filter((item) => {
        return (
            item.name.toLowerCase().includes(searchValue) ||
            item.category.toLowerCase().includes(searchValue) ||
            item.status.toLowerCase().includes(searchValue)
        );
    });

    currentPage = 1;
    renderTable();
}
