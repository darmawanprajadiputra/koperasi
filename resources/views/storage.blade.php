@extends('layouts.app')

@section('title', 'Gudang')

@section('content')
    <div class="max-w-7xl mx-8 mt-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-teal-900 mb-2">Gudang</h1>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="mb-6 px-6 py-4 bg-green-500 text-white rounded-lg font-semibold shadow">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 px-6 py-4 bg-red-500 text-white rounded-lg font-semibold shadow">
                {{ session('error') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Total Produk</p>
                <span class="text-3xl font-bold text-teal-900">{{ $products->count() }}</span>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Hampir Habis</p>
                <span class="text-3xl font-bold text-teal-900">{{ $products->where('stock', '<=', 10)->count() }}</span>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Produk Aktif</p>
                <span class="text-3xl font-bold text-teal-900">{{ $products->where('is_active', true)->count() }}</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 mb-4 flex-wrap">
            <a href="{{ route('product.create') }}"
                class="bg-teal-700 text-white px-4 py-2 rounded-full font-semibold hover:bg-teal-800 transition-colors flex items-center gap-2 text-sm no-underline inline-flex">
                <span>+</span> Tambah Item
            </a>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-t-xl border border-b-0 border-gray-200 p-4 flex gap-4 items-center">
            <div class="flex-1 relative">
                <span class="absolute left-3 top-3 text-gray-400">🔍</span>
                <input type="text" id="searchInput" placeholder="Cari nama produk atau kategori..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm" />
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-b-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Foto</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Nama Produk</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Kategori</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Stok</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Harga</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-gray-200"></tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="hidden py-16 text-center text-gray-400">
                <p class="text-4xl mb-3">📦</p>
                <p class="font-semibold">Tidak ada produk ditemukan</p>
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-between">
                <span id="paginationInfo" class="text-sm text-gray-600"></span>
                <div class="flex gap-2">
                    <button id="prevBtn"
                        class="px-3 py-1 bg-white border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>Previous</button>
                    <button id="nextBtn"
                        class="px-3 py-1 bg-white border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden forms untuk aksi produk --}}
    <form id="deleteForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
    <form id="toggleForm" method="POST" style="display:none;">
        @csrf
        @method('PATCH')
    </form>

    {{-- Dropdown menu (satu, dipakai bersama) --}}
    <div id="actionMenu" class="hidden fixed z-50 bg-white rounded-xl shadow-xl border border-gray-100 py-1 w-44"
        style="min-width:160px;">
        <button id="menuEdit"
            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <span class="material-symbols-outlined" style="font-size:18px">edit</span> Edit
        </button>
        <button id="menuToggle"
            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <span class="material-symbols-outlined" style="font-size:18px" id="menuToggleIcon">toggle_off</span>
            <span id="menuToggleLabel">Nonaktifkan</span>
        </button>
        <hr class="my-1 border-gray-100">
        <button id="menuDelete"
            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
            <span class="material-symbols-outlined" style="font-size:18px">delete</span> Hapus
        </button>
    </div>

    <script>
        const allProducts = @json($products);
        const storageUrl = '{{ asset('storage') }}';
        const defaultImg = '/assets/pictures/produk.jpg';
        const editBaseUrl = '{{ url('product') }}';
        const deleteBase = '{{ url('product') }}';
        const toggleBase = '{{ url('product') }}';
        const csrfToken = '{{ csrf_token() }}';

        const PER_PAGE = 10;
        let currentPage = 1;
        let filteredData = [...allProducts];
        let activeProduct = null; // produk yang sedang di-klik titik 3

        const tableBody = document.getElementById('tableBody');
        const paginationInfo = document.getElementById('paginationInfo');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const emptyState = document.getElementById('emptyState');
        const searchInput = document.getElementById('searchInput');
        const actionMenu = document.getElementById('actionMenu');

        function formatRupiah(v) {
            return 'Rp ' + Number(v).toLocaleString('id-ID');
        }

        function getStatusBadge(isActive) {
            return isActive ?
                '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aktif</span>' :
                '<span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-semibold">Nonaktif</span>';
        }

        function getImageSrc(image) {
            if (!image) return defaultImg;
            return storageUrl + '/' + image;
        }

        function render() {
            const total = filteredData.length;
            const totalPages = Math.ceil(total / PER_PAGE) || 1;
            if (currentPage > totalPages) currentPage = totalPages;

            const start = (currentPage - 1) * PER_PAGE;
            const pageData = filteredData.slice(start, start + PER_PAGE);

            if (pageData.length === 0) {
                tableBody.innerHTML = '';
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
                tableBody.innerHTML = pageData.map(p => `
                    <tr class="hover:bg-gray-50 transition-colors" data-id="${p.id}">
                        <td class="px-4 py-3">
                            <img src="${getImageSrc(p.image)}"
                                 onerror="this.src='${defaultImg}'"
                                 alt="${p.name_product}"
                                 class="w-12 h-12 object-cover rounded-lg border border-gray-200" />
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">${p.name_product}</td>
                        <td class="px-4 py-3 text-gray-600">${p.category ? p.category.name_categories : '-'}</td>
                        <td class="px-4 py-3 text-right text-gray-700">${p.stock}</td>
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
            paginationInfo.textContent = `Menampilkan ${showing} dari ${total} produk`;
            prevBtn.disabled = currentPage <= 1;
            nextBtn.disabled = currentPage >= totalPages || total === 0;
        }

        // Search
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.toLowerCase().trim();
            filteredData = allProducts.filter(p =>
                p.name_product.toLowerCase().includes(q) ||
                (p.category && p.category.name_categories.toLowerCase().includes(q))
            );
            currentPage = 1;
            render();
        });

        prevBtn.addEventListener('click', () => {
            currentPage--;
            render();
        });
        nextBtn.addEventListener('click', () => {
            currentPage++;
            render();
        });

        // ── Action Menu ──────────────────────────────────────────
        // Buka menu ketika klik titik 3
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.action-btn');

            if (btn) {
                e.stopPropagation();
                const id = parseInt(btn.dataset.id);
                activeProduct = allProducts.find(p => p.id === id);

                // Label toggle sesuai status saat ini
                document.getElementById('menuToggleIcon').textContent = activeProduct.is_active ? 'toggle_off' :
                    'toggle_on';
                document.getElementById('menuToggleLabel').textContent = activeProduct.is_active ? 'Nonaktifkan' :
                    'Aktifkan';

                // Tampilkan dulu (tersembunyi) untuk ukur tingginya
                actionMenu.style.visibility = 'hidden';
                actionMenu.classList.remove('hidden');

                const rect = btn.getBoundingClientRect();
                const menuH = actionMenu.offsetHeight;
                const menuW = actionMenu.offsetWidth;
                const spaceBelow = window.innerHeight - rect.bottom;
                const spaceLeft = rect.left + window.scrollX;

                // Buka ke atas jika ruang bawah tidak cukup
                const top = spaceBelow < menuH + 8 ?
                    rect.top + window.scrollY - menuH - 4 :
                    rect.bottom + window.scrollY + 4;

                // Jaga agar tidak keluar sisi kanan layar
                const left = Math.min(
                    rect.right + window.scrollX - menuW,
                    window.innerWidth - menuW - 8
                );

                actionMenu.style.top = top + 'px';
                actionMenu.style.left = Math.max(8, left) + 'px';
                actionMenu.style.visibility = 'visible';
                return;
            }

            // Klik di luar → tutup menu
            actionMenu.classList.add('hidden');
        });

        // Edit
        document.getElementById('menuEdit').addEventListener('click', () => {
            if (!activeProduct) return;
            window.location.href = `${editBaseUrl}/${activeProduct.id}/edit`;
        });

        // Toggle aktif/nonaktif
        document.getElementById('menuToggle').addEventListener('click', () => {
            if (!activeProduct) return;
            actionMenu.classList.add('hidden');

            const form = document.getElementById('toggleForm');
            form.action = `${toggleBase}/${activeProduct.id}/toggle`;
            form.submit();
        });

        // Hapus
        document.getElementById('menuDelete').addEventListener('click', () => {
            if (!activeProduct) return;
            if (!confirm(`Hapus produk "${activeProduct.name_product}"?`)) return;
            actionMenu.classList.add('hidden');

            const form = document.getElementById('deleteForm');
            form.action = `${deleteBase}/${activeProduct.id}`;
            form.submit();
        });

        render();
    </script>
@endsection
