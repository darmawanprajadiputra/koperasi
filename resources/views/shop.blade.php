@extends('layouts.public')

@section('title', 'KOPERASI BISMAHILLAH INDONESIA SEJAHTERA')

@section('content')
    <div class="px-8 py-8 max-w-7xl mx-auto">

        {{-- Success Banner setelah checkout --}}
        @if (session('order_success'))
            <div id="orderSuccessBanner"
                class="mb-6 flex items-center gap-3 bg-[#e8f5e9] text-[#1b5e20] border border-[#a5d6a7] px-5 py-4 rounded-xl text-sm font-medium shadow-sm">
                <span class="material-symbols-outlined text-xl flex-shrink-0"
                    style="font-variation-settings: 'FILL' 1">check_circle</span>
                <span>{{ session('order_success') }}</span>
                <button onclick="document.getElementById('orderSuccessBanner').remove()"
                    class="ml-auto p-1 hover:bg-[#c8e6c9] rounded-full transition-colors flex-shrink-0">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
        @endif

        <!-- Category Filter -->
        <div id="categoryFilter" class="flex flex-wrap gap-2 mb-6">
            <span
                class="category-badge px-6 py-2 bg-tertiary text-on-tertiary rounded-full text-sm font-medium cursor-pointer transition-colors"
                data-category="all">
                Semua Produk
            </span>
        </div>

        <!-- Loading State -->
        <div id="loadingSpinner" class="text-center py-12">
            <div class="inline-block animate-spin">
                <span class="material-symbols-outlined text-4xl text-primary">hourglass_empty</span>
            </div>
            <p class="mt-4 text-on-surface-variant">Memuat produk...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-12">
            <span class="material-symbols-outlined text-5xl text-outline/30 block mb-4">inventory_2</span>
            <p class="text-on-surface-variant font-medium">Belum ada produk tersedia</p>
            <p class="text-on-surface-variant text-sm mt-1">Tambahkan produk di halaman Gudang untuk ditampilkan di toko.
            </p>
        </div>

        <!-- Products Grid -->
        <div id="productsContainer" class="hidden grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-8">
            <!-- Product cards will be loaded here -->
        </div>
    </div>

    <!-- Floating Checkout Button -->
    <div id="floatingCart" class="fixed bottom-6 right-6 z-50 hidden">
        <button id="checkoutBtn"
            class="flex items-center gap-3 bg-gradient-to-br from-[#00342b] to-[#004d40] text-white px-6 py-4 rounded-2xl shadow-2xl hover:shadow-[0_8px_32px_rgba(0,52,43,0.4)] hover:-translate-y-1 transition-all duration-300 active:scale-95">
            <div class="relative">
                <span class="material-symbols-outlined text-2xl"
                    style="font-variation-settings: 'FILL' 1">shopping_basket</span>
                <span id="cartBadge"
                    class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">0</span>
            </div>
            <div class="flex flex-col items-start">
                <span class="text-xs text-white/70 leading-none mb-0.5">Keranjang</span>
                <span id="cartTotal" class="text-sm font-bold leading-none">Rp 0</span>
            </div>
            <span class="material-symbols-outlined text-sm opacity-70">arrow_forward</span>
        </button>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-24 right-6 z-50 hidden">
        <div class="flex items-center gap-2 bg-[#00342b] text-white px-4 py-3 rounded-xl shadow-lg text-sm font-medium">
            <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1">check_circle</span>
            <span id="toastMessage">Ditambahkan ke keranjang</span>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/shop.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('checkoutBtn');
            if (btn) {
                btn.addEventListener('click', function() {
                    if (typeof window.goToCheckout === 'function') {
                        window.goToCheckout();
                    } else {
                        window.location.href = '/checkout';
                    }
                });
            }
            @if (session('order_success'))
                sessionStorage.removeItem('pendingCart');
            @endif
        });
    </script>
@endpush