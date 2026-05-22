@extends('layouts.app')

@section('title', 'Pesanan')
@section('page_title', 'Daftar Pesanan')

@section('content')
    <div class="pt-8 px-8 pb-20 max-w-7xl mx-auto">

        @if (session('success'))
            <div id="flashSuccess"
                class="mb-8 flex items-center gap-3 bg-[#e8f5e9] text-[#1b5e20] px-5 py-4 rounded-xl text-sm font-semibold">
                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter Buttons -->
        <div class="flex gap-3 mb-8 overflow-x-auto pb-2">
            <button class="filter-btn px-8 py-2.5 rounded-full font-semibold text-sm bg-tertiary text-on-tertiary shadow-lg shadow-tertiary/10 transition-all" data-filter="all">Semua</button>
            <button class="filter-btn px-8 py-2.5 rounded-full font-semibold text-sm bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest transition-all" data-filter="process">Proses</button>
            <button class="filter-btn px-8 py-2.5 rounded-full font-semibold text-sm bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest transition-all" data-filter="completed">Selesai</button>
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
            <button id="prevBtn" class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-primary hover:bg-surface-container-highest transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <div id="paginationContainer" class="flex gap-2"></div>
            <button id="nextBtn" class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-primary hover:bg-surface-container-highest transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined">chevron_right</span>
            </button>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/order.js') }}"></script>
@endpush