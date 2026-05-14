// Order Page JavaScript

// Sample order data
const sampleOrders = [
  {
    id: '#AGR-2024-8812',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuC40c1gFpL7eYCg6Xn4CRPhl8eWVdZQyv_VQPjG0rZMG3Dhvk5Vz5ZjqyGqyRFaF-bI3ej-n1OPCE-ldB8aliUIz9XaNXIfMpsaRk9WAsZvjNwfCzA6L2ThwA91N0lll6ySQmi3JttS4KEP0liWfG14rN11PKtt3YS_Ayk2AmCt8kna7JA1myPyBCFL-Z4O3IfGc2iU_c1g_iEK9J8YibDeeQqOYFIGQH1aNpQK6sWiEHQYxJuQerbpu994girH-JKO78Frfq8_LUvp',
    status: 'process',
    badge: 'Diproses',
    badgeClass: 'badge-process',
    total: 1450000,
    date: '12 Okt 2024',
    items: '12 Komoditas',
    method: 'Transfer Bank'
  },
  {
    id: '#AGR-2024-8795',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAhGUdi9Ks9s3FSIHlrN6Li-jiaboxBHOfsE0Qn2JoyUUEdY066w8ZzzVFYiZfTePDSNpOk6W7sActbDLhAEL4pqQ6yNZos0_NrvMHobiTElJNIEUORT5VWgeZXd_seFTuHA9gVmB2w4s_exreB3EjQRoGyhIL_yuD7nL-n5JnrJvUac6VeBFtGb1wCbgr1OSIVe_o-jeEWbMo7pcq6RLSns6NWEIseTgkwv9eeBt1hVR1dV6pSmaHRpub1F1aQ77NyMLzXvxZi22dZ',
    status: 'shipped',
    badge: 'Dikirim',
    badgeClass: 'badge-shipped',
    total: 842000,
    date: '10 Okt 2024',
    items: '5 Komoditas',
    method: 'Agrarian Express'
  },
  {
    id: '#AGR-2024-8702',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBXYY4tUeZOBbe4CcpkhkouD2U4ZwBzxpaBjhC9T6R8oe5se8bz9QR5BhaqrpktiDmQejTnXGlf9AHfG0uQZUWWvyU387Y6tJ-OiNNkN35IzeCIyt9rR5bL2IAqyVhCox6YClIY-aI1a2D_-aCxsoOkMZqEpg2goHTfaqfTeEgG94Ywh-QfuegLtkC_reqi0Uno2BxdGenOduohmOxtk7j-cG8-cfUxF0-R0rc4JXV_fmFddFE2aSqJprvTbR0N8SvLps6WR2nQm-rn',
    status: 'completed',
    badge: 'Selesai',
    badgeClass: 'badge-completed',
    total: 2105000,
    date: '05 Okt 2024',
    items: '22 Komoditas',
    method: 'Credit Line'
  }
];

let currentPage = 1;
let itemsPerPage = 5;
let filteredOrders = sampleOrders;
let currentFilter = 'all';

// Utility function to format currency
function formatCurrency(value) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value);
}

// Create order card HTML
function createOrderCard(order, isCompleted = false) {
  const imageClass = isCompleted ? 'completed' : '';
  const btnClass = isCompleted ? 'text-btn' : '';
  const methodLabel = order.status === 'shipped' ? 'Kurir' : 'Metode';
  const methodValue = order.method;

  return `
    <div class="order-card">
      <div class="order-card-image-wrapper ${imageClass}">
        <img src="${order.image}" alt="order image" class="order-card-image" />
        <span class="badge ${order.badgeClass}">${order.badge}</span>
      </div>
      <div class="order-card-content">
        <div class="order-card-header">
          <div>
            <span class="order-id">Order ID</span>
            <h3 class="order-id-value">${order.id}</h3>
          </div>
          <div>
            <span class="order-total-label">Total Pesanan</span>
            <p class="order-total-value">${formatCurrency(order.total)}</p>
          </div>
        </div>
        <div class="order-card-details">
          <div class="order-detail-item">
            <p class="order-detail-label">Tanggal</p>
            <p class="order-detail-value">${order.date}</p>
          </div>
          <div class="order-detail-item">
            <p class="order-detail-label">Total Item</p>
            <p class="order-detail-value">${order.items}</p>
          </div>
          <div class="order-detail-item">
            <p class="order-detail-label">${methodLabel}</p>
            <p class="order-detail-value">${methodValue}</p>
          </div>
          <div class="order-detail-item order-detail-button">
            <button class="order-detail-btn ${btnClass}">
              Lihat Detail
              <span class="material-symbols-outlined">arrow_forward</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
}

// Render orders
function renderOrders() {
  const container = document.getElementById('ordersContainer');
  
  if (!container) return;

  // Apply filter
  if (currentFilter === 'all') {
    filteredOrders = sampleOrders;
  } else {
    filteredOrders = sampleOrders.filter(order => order.status === currentFilter);
  }

  // Calculate pagination
  const totalPages = Math.ceil(filteredOrders.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const paginatedOrders = filteredOrders.slice(startIndex, endIndex);

  // Render orders
  container.innerHTML = paginatedOrders.map((order, index) => {
    const isCompleted = order.status === 'completed';
    return createOrderCard(order, isCompleted);
  }).join('');

  // Render pagination
  renderPagination(totalPages);

  // Update button states
  updatePaginationButtons(totalPages);
}

// Render pagination buttons
function renderPagination(totalPages) {
  const paginationContainer = document.getElementById('paginationContainer');
  
  if (!paginationContainer) return;

  let html = '';
  for (let i = 1; i <= totalPages; i++) {
    const activeClass = i === currentPage ? 'active' : '';
    html += `<button class="pagination-btn ${activeClass}" data-page="${i}">${i}</button>`;
  }
  
  paginationContainer.innerHTML = html;

  // Add event listeners
  document.querySelectorAll('.pagination-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      currentPage = parseInt(e.target.dataset.page);
      renderOrders();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  });
}

// Update pagination button states
function updatePaginationButtons(totalPages) {
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');

  if (prevBtn) {
    prevBtn.disabled = currentPage === 1;
    prevBtn.style.opacity = currentPage === 1 ? '0.5' : '1';
    prevBtn.addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage--;
        renderOrders();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });
  }

  if (nextBtn) {
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.style.opacity = currentPage === totalPages ? '0.5' : '1';
    nextBtn.addEventListener('click', () => {
      if (currentPage < totalPages) {
        currentPage++;
        renderOrders();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });
  }
}

// Filter handler
function setupFilterHandlers() {
  const filterButtons = document.querySelectorAll('.filter-btn');
  
  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      // Remove active class from all buttons
      filterButtons.forEach(b => b.classList.remove('active'));
      
      // Add active class to clicked button
      btn.classList.add('active');
      
      // Update filter and reset pagination
      currentFilter = btn.dataset.filter || 'all';
      currentPage = 1;
      
      // Re-render
      renderOrders();
    });
  });

  // Set initial active button
  filterButtons[0].classList.add('active');
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  if (!document.getElementById('ordersContainer')) return;
  setupFilterHandlers();
  renderOrders();
});
