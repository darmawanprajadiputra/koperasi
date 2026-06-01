@extends('layouts.app')

@section('title', 'Gudang')
@section('page_title', 'Gudang')

@section('content')

    <div class="max-w-7xl mx-8 mt-8">

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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Total Produk</p>
                <span class="text-3xl font-bold text-teal-900">{{ $products->count() }}</span>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-amber-200">
                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide mb-3">Perlu Restock</p>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-bold text-amber-600">{{ $restockCount ?? 0 }}</span>
                    @if (($restockCount ?? 0) > 0)
                        <span class="text-xs text-amber-500 mb-1 font-medium">stok di bawah ROP</span>
                    @else
                        <span class="text-xs text-gray-400 mb-1 font-medium">semua aman</span>
                    @endif
                </div>
            </div>            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
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
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                <input type="text" id="searchInput" placeholder="Cari produk..."
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
            <div id="emptyState" class="hidden py-16 text-center text-gray-400" style="display:none;">
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

    {{-- Hidden forms aksi produk --}}
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
        window.StorageConfig = {
            allProducts : @json($products),
            storageUrl  : '{{ asset('storage') }}',
            defaultImg  : '/assets/pictures/produk.jpg',
            editBaseUrl : '{{ url('product') }}',
            deleteBase  : '{{ url('product') }}',
            toggleBase  : '{{ url('product') }}',
            csrfToken   : '{{ csrf_token() }}',
        };
    </script>
    <script src="{{ asset('js/storage.js') }}"></script>
@endsection