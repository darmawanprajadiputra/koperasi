@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
    <div class="pt-8 px-8 pb-20 max-w-7xl mx-auto">

        <header class="mb-12">
            <h1 class="font-manrope font-extrabold text-3xl text-primary uppercase -tracking-tighter mb-4">
                Daftar Pesanan
            </h1>
        </header>

        {{-- Success flash dari checkout --}}
        @if (session('success'))
            <div id="flashSuccess"
                class="mb-8 flex items-center gap-3 bg-[#e8f5e9] text-[#1b5e20] px-5 py-4 rounded-xl text-sm font-semibold">
                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter Buttons -->
        <div class="flex gap-3 mb-8 overflow-x-auto pb-2">
            <button
                class="filter-btn px-8 py-2.5 rounded-full font-semibold text-sm bg-tertiary text-on-tertiary shadow-lg shadow-tertiary/10 transition-all"
                data-filter="all">Semua</button>
            <button
                class="filter-btn px-8 py-2.5 rounded-full font-semibold text-sm bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest transition-all"
                data-filter="process">Proses</button>
            <button
                class="filter-btn px-8 py-2.5 rounded-full font-semibold text-sm bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest transition-all"
                data-filter="completed">Selesai</button>
        </div>

        <!-- Loading -->
        <div id="loadingState" class="text-center py-16 text-on-surface-variant">
            <div class="inline-block animate-spin mb-4">
                <span class="material-symbols-outlined text-4xl text-primary">hourglass_empty</span>
            </div>
            <p>Memuat pesanan...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-16 text-on-surface-variant">
            <span class="material-symbols-outlined text-6xl opacity-20 block mb-4">receipt_long</span>
            <p class="font-semibold text-base">Belum ada pesanan</p>
            <p class="text-sm mt-1">Pesanan yang masuk akan muncul di sini.</p>
        </div>

        <!-- Orders Container -->
        <div id="ordersContainer" class="space-y-4 hidden"></div>

        <!-- Pagination -->
        <div id="paginationWrapper" class="mt-16 hidden flex justify-center items-center gap-4">
            <button id="prevBtn"
                class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-primary hover:bg-surface-container-highest transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <div id="paginationContainer" class="flex gap-2"></div>
            <button id="nextBtn"
                class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-primary hover:bg-surface-container-highest transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined">chevron_right</span>
            </button>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const DEFAULT_IMG = '/assets/pictures/produk.jpg';
            const PER_PAGE = 10;

            let allOrders = [];
            let currentPage = 1;
            let activeFilter = 'all';

            // ── Status helpers ─────────────────────────────────────────────────────────
            const STATUS_LABEL = {
                pending: 'DIPROSES',
                processing: 'DIPROSES',
                completed: 'SELESAI',
                cancelled: 'DIBATALKAN',
            };
            const STATUS_CLASS = {
                pending: 'bg-[#f5e6c8] text-[#7a4f00]',
                processing: 'bg-[#f5e6c8] text-[#7a4f00]',
                completed: 'bg-[#c8f5d5] text-[#005c20]',
                cancelled: 'bg-red-100 text-red-700',
            };

            function statusLabel(s) {
                return STATUS_LABEL[s] ?? s?.toUpperCase() ?? '-';
            }

            function statusClass(s) {
                return STATUS_CLASS[s] ?? 'bg-surface-container text-on-surface-variant';
            }

            // ── Currency ───────────────────────────────────────────────────────────────
            function formatRp(v) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(v);
            }

            // ── Build one order card (matching the screenshot design) ──────────────────
            function createOrderCard(order) {
                return `
      <div class="order-card bg-surface-container-lowest rounded-2xl overflow-hidden flex items-stretch gap-0 shadow-sm hover:shadow-md transition-shadow"
           data-status="${order.payment_status ?? 'pending'}">

        {{-- Gambar kiri --}}
        <div class="relative w-28 flex-shrink-0">
          <img src="${DEFAULT_IMG}"
               onerror="this.src='${DEFAULT_IMG}'"
               alt="Pesanan"
               class="w-full h-full object-cover" />
          <div class="absolute top-3 left-3">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider ${statusClass(order.payment_status ?? 'pending')}">
              ${statusLabel(order.payment_status ?? 'pending')}
            </span>
          </div>
        </div>

        {{-- Konten --}}
        <div class="flex-1 px-8 py-6 flex flex-col justify-between gap-4">

          <div class="flex justify-between items-start">
            <div>
              <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">No. Faktur</p>
              <p class="font-manrope font-extrabold text-2xl text-on-surface">#${order.num_factur}</p>
            </div>
            <div class="text-right">
              <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Total Pesanan</p>
              <p class="font-manrope font-extrabold text-2xl text-primary">${formatRp(order.total_amount)}</p>
            </div>
          </div>

          <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex gap-8">
              <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Tanggal</p>
                <p class="text-sm font-semibold text-on-surface">${order.date}</p>
              </div>
              <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Pemesan</p>
                <p class="text-sm font-semibold text-on-surface">${order.name_customer}</p>
              </div>
              <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Total Item</p>
                <p class="text-sm font-semibold text-on-surface">${order.total_items} Produk</p>
              </div>
              <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Metode</p>
                <p class="text-sm font-semibold text-on-surface">${order.payment_method ?? '-'}</p>
              </div>
            </div>
            <button
              class="detail-btn flex items-center gap-2 bg-on-surface text-surface px-6 py-3 rounded-full font-bold text-sm hover:opacity-80 active:scale-95 transition-all"
              data-factur="${order.num_factur}">
              Lihat Detail
              <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 0">arrow_forward</span>
            </button>
          </div>

        </div>
      </div>
    `;
            }

            // ── Render current page ────────────────────────────────────────────────────
            function render() {
                const container = document.getElementById('ordersContainer');
                const emptyState = document.getElementById('emptyState');
                const paginationWr = document.getElementById('paginationWrapper');
                const pagContainer = document.getElementById('paginationContainer');

                const filtered = activeFilter === 'all' ?
                    allOrders :
                    allOrders.filter(o => {
                        if (activeFilter === 'process') return ['pending', 'processing'].includes(o.payment_status);
                        if (activeFilter === 'completed') return o.payment_status === 'completed';
                        return true;
                    });

                const totalPages = Math.ceil(filtered.length / PER_PAGE) || 1;
                if (currentPage > totalPages) currentPage = totalPages;

                const slice = filtered.slice((currentPage - 1) * PER_PAGE, currentPage * PER_PAGE);

                if (slice.length === 0) {
                    container.classList.add('hidden');
                    paginationWr.classList.add('hidden');
                    emptyState.classList.remove('hidden');
                } else {
                    emptyState.classList.add('hidden');
                    container.classList.remove('hidden');
                    container.innerHTML = slice.map(createOrderCard).join('');

                    // detail button handlers
                    container.querySelectorAll('.detail-btn').forEach(btn => {
                        btn.addEventListener('click', () => {
                            alert('Detail pesanan: ' + btn.dataset.factur);
                            // TODO: buka modal atau redirect ke halaman detail
                        });
                    });

                    // pagination
                    if (totalPages > 1) {
                        paginationWr.classList.remove('hidden');
                        pagContainer.innerHTML = Array.from({
                            length: totalPages
                        }, (_, i) => `
          <button class="page-btn w-9 h-9 rounded-full text-sm font-bold transition-all
            ${i + 1 === currentPage
              ? 'bg-tertiary text-on-tertiary shadow'
              : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest'}"
            data-page="${i + 1}">${i + 1}</button>
        `).join('');

                        pagContainer.querySelectorAll('.page-btn').forEach(btn => {
                            btn.addEventListener('click', () => {
                                currentPage = parseInt(btn.dataset.page);
                                render();
                            });
                        });

                        document.getElementById('prevBtn').disabled = currentPage <= 1;
                        document.getElementById('nextBtn').disabled = currentPage >= totalPages;
                    } else {
                        paginationWr.classList.add('hidden');
                    }
                }
            }

            // ── Load from API ──────────────────────────────────────────────────────────
            async function loadOrders() {
                const loading = document.getElementById('loadingState');
                try {
                    const res = await fetch('/api/get_orders');
                    const data = await res.json();

                    loading.classList.add('hidden');

                    if (data.success) {
                        allOrders = data.orders;
                        render();
                    } else {
                        document.getElementById('emptyState').classList.remove('hidden');
                    }
                } catch (err) {
                    console.error('[ORDER]', err);
                    loading.classList.add('hidden');
                    document.getElementById('emptyState').classList.remove('hidden');
                }
            }

            // ── Filter buttons ─────────────────────────────────────────────────────────
            function setupFilters() {
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('.filter-btn').forEach(b => {
                            b.classList.remove('bg-tertiary', 'text-on-tertiary', 'shadow-lg',
                                'shadow-tertiary/10');
                            b.classList.add('bg-surface-container-high',
                                'text-on-surface-variant');
                        });
                        btn.classList.add('bg-tertiary', 'text-on-tertiary', 'shadow-lg',
                            'shadow-tertiary/10');
                        btn.classList.remove('bg-surface-container-high', 'text-on-surface-variant');
                        activeFilter = btn.dataset.filter;
                        currentPage = 1;
                        render();
                    });
                });

                document.getElementById('prevBtn').addEventListener('click', () => {
                    currentPage--;
                    render();
                });
                document.getElementById('nextBtn').addEventListener('click', () => {
                    currentPage++;
                    render();
                });
            }

            // ── Auto-dismiss flash message ─────────────────────────────────────────────
            const flash = document.getElementById('flashSuccess');
            if (flash) setTimeout(() => flash.style.display = 'none', 5000);

            // ── Init ──────────────────────────────────────────────────────────────────
            document.addEventListener('DOMContentLoaded', () => {
                setupFilters();
                loadOrders();
            });
        })();
    </script>
@endpush
