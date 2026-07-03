@extends('layouts.app')

@section('title', 'Detail Pesanan')
@section('page_title', 'Rincian Pesanan')

@section('content')
    <div class="pt-8 px-8 pb-10 max-w-7xl mx-auto">

        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('order') }}"
                class="group inline-flex items-center gap-2 text-primary font-medium text-sm hover:-translate-x-1 transition-transform">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Kembali ke Daftar Pesanan
            </a>
        </div>

        {{-- Loading State --}}
        <div id="loadingState" class="text-center py-16 text-on-surface-variant">
            <div class="inline-block animate-spin mb-4">
                <span class="material-symbols-outlined text-4xl text-primary">hourglass_empty</span>
            </div>
            <p>Memuat detail pesanan...</p>
        </div>

        {{-- Error State --}}
        <div id="errorState" class="hidden text-center py-16 text-on-surface-variant">
            <span class="material-symbols-outlined text-6xl opacity-20 block mb-4">error_outline</span>
            <p class="font-semibold text-base">Pesanan tidak ditemukan</p>
            <p class="text-sm mt-1">Pastikan nomor faktur yang Anda akses benar.</p>
        </div>

        {{-- Main Content --}}
        <div id="detailContent" class="hidden">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-xs font-bold tracking-widest text-secondary uppercase">Status Pesanan</span>
                        <span id="headerBadge" class="px-3 py-1 rounded-full text-[11px] font-bold"></span>
                    </div>
                    <h1 id="headerInvoice" class="font-manrope text-4xl font-extrabold text-primary tracking-tight"></h1>
                    <p id="headerDate" class="text-on-surface-variant mt-2 font-medium"></p>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-8">

                {{-- Kolom Kiri --}}
                <div class="col-span-12 lg:col-span-8 space-y-8">

                    {{-- Status Pesanan --}}
                    <section class="bg-white p-8 rounded-xl shadow-sm border border-outline-variant/5">
                        <h3 class="text-lg font-bold text-primary mb-8">Status Pesanan</h3>

                        {{-- Step track --}}
                        <div class="flex items-start">

                            {{-- Pesanan Diterima --}}
                            <div class="flex flex-col items-center text-center min-w-[80px]" id="step1">
                                <div id="step1Icon"
                                    class="w-10 h-10 rounded-full bg-[#005c20] text-white flex items-center justify-center shadow-md">
                                    <span class="material-symbols-outlined text-sm"
                                        style="font-variation-settings:'FILL' 1">check</span>
                                </div>
                                <p class="mt-3 text-xs font-bold text-[#005c20]">Pesanan Diterima</p>
                                <p id="step1Date" class="text-[10px] text-gray-400 mt-0.5">—</p>
                            </div>

                            <div class="flex-1 flex flex-col items-stretch mt-5">
                                <div class="h-[2px] w-full bg-gray-200 relative">
                                    <div id="line1" class="absolute inset-0 bg-[#005c20] transition-all duration-500" style="width:100%"></div>
                                </div>
                            </div>

                            {{-- Diproses --}}
                            <div class="flex flex-col items-center text-center min-w-[80px]" id="step2">
                                <div id="step2Icon"
                                    class="w-10 h-10 rounded-full bg-white border-[3px] border-[#005c20] flex items-center justify-center shadow-sm">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#005c20]"></span>
                                </div>
                                <p class="mt-3 text-xs font-bold text-[#005c20]" id="step2Label">Diproses</p>
                                <p class="text-[10px] text-gray-400 mt-0.5" id="step2Sub">Sedang disiapkan</p>
                            </div>

                            <div class="flex-1 flex flex-col items-stretch mt-5">
                                <div class="h-[2px] w-full bg-gray-200 relative">
                                    <div id="line2" class="absolute inset-0 bg-[#005c20] transition-all duration-500" style="width:0%"></div>
                                </div>
                            </div>

                            {{-- Selesai --}}
                            <div class="flex flex-col items-center text-center min-w-[80px]" id="step3">
                                <div id="step3Icon"
                                    class="w-10 h-10 rounded-full bg-gray-100 border-2 border-gray-300 flex items-center justify-center">
                                </div>
                                <p class="mt-3 text-xs font-bold text-gray-400" id="step3Label">Selesai</p>
                                <p class="text-[10px] text-gray-400 mt-0.5" id="step3Sub">—</p>
                            </div>

                        </div>
                    </section>

                    {{-- Ringkasan Pesanan --}}
                    <section
                        class="bg-white overflow-hidden rounded-xl shadow-sm border border-outline-variant/5">
                        <div class="px-8 py-6 border-b border-surface-container-low flex justify-between items-center">
                            <h3 class="text-lg font-bold text-primary">Ringkasan Pesanan</h3>
                            <span id="itemsCount" class="text-sm font-medium text-on-surface-variant"></span>
                        </div>
                        <div id="productList" class="divide-y divide-surface-container-low"></div>
                    </section>

                    {{-- Tombol Selesaikan --}}
                    <div id="completeOrderSection" class="hidden">
                        <button id="completeOrderBtn"
                            class="w-full bg-green-600 flex items-center justify-center gap-3 text-white px-8 py-4 rounded-xl font-bold text-base shadow-lg shadow-primary/20 hover:opacity-90 active:scale-[0.99] transition-all">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">task_alt</span>
                            Tandai Pesanan Selesai
                        </button>
                    </div>

                    {{-- Batal --}}
                    <div id="cancelOrderSection" class="hidden">
                        <button id="cancelOrderBtn"
                            class="w-full bg-white border-2 border-red-400 flex items-center justify-center gap-3 text-red-500 px-8 py-4 rounded-xl font-bold text-base hover:bg-red-50 active:scale-[0.99] transition-all">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">cancel</span>
                            Batalkan Pesanan
                        </button>
                    </div>

                </div>

                {{-- Kolom Kanan --}}
                <div class="col-span-12 lg:col-span-4 space-y-8">

                    {{-- Info Pemesan --}}
                    <section class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/5">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-primary text-xl">person</span>
                            <h3 class="text-lg font-bold text-primary">Info Pemesan</h3>
                        </div>
                        <div class="space-y-5">
                            <div>
                                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Nama</p>
                                <p id="infoName" class="text-sm font-bold text-on-surface mt-1">—</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">No.
                                    Telepon</p>
                                <p id="infoPhone" class="text-sm text-on-surface-variant mt-1">—</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Penerima
                                </p>
                                <p id="infoRecipient" class="text-sm text-on-surface-variant mt-1">—</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Alamat
                                    Pengiriman</p>
                                <p id="infoAddress" class="text-sm text-on-surface-variant mt-1 leading-relaxed">—</p>
                            </div>
                        </div>
                    </section>

                    {{-- Detail Pembayaran --}}
                    <section
                        class="bg-white text-on-primary p-6 rounded-xl shadow-xl shadow-primary/20 relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 opacity-10">
                            <span class="material-symbols-outlined text-9xl"
                                style="font-variation-settings:'FILL' 1">account_balance_wallet</span>
                        </div>
                        <h3 class="text-lg font-bold mb-4 relative z-10">Detail Pembayaran</h3>
                        <div class="space-y-4 relative z-10">
                            <div class="flex justify-between text-sm opacity-80">
                                <span>Metode</span>
                                <span id="payMethod" class="font-semibold">—</span>
                            </div>
                            <div class="flex justify-between text-sm opacity-80">
                                <span>Status</span>
                                <span id="payStatus" class="font-semibold">—</span>
                            </div>
                            <div id="notesRow" class="hidden flex justify-between text-sm opacity-80">
                                <span>Catatan</span>
                                <span id="payNotes" class="font-semibold text-right max-w-[60%]">—</span>
                            </div>
                            <div class="h-[1px] bg-white/20 my-4"></div>
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-60">Total Pembayaran
                                    </p>
                                    <p id="payTotal" class="font-manrope text-2xl font-extrabold tracking-tight">—</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-60">Jumlah Item</p>
                                    <p id="payItems" class="text-sm font-bold">—</p>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>

    </div>

    {{-- Confirm Modal --}}
    <div id="confirmModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-8 animate-fade-in">
            <div class="flex items-center justify-center w-16 h-16 bg-[#c8f5d5] rounded-full mx-auto mb-5">
                <span class="material-symbols-outlined text-3xl text-[#005c20]"
                    style="font-variation-settings:'FILL' 1">task_alt</span>
            </div>
            <h2 class="font-manrope text-xl font-extrabold text-on-surface text-center mb-2">Konfirmasi Pesanan Selesai
            </h2>
            <p class="text-sm text-on-surface-variant text-center mb-8">
                Pesanan ini akan ditandai sebagai <strong>Selesai</strong>.<br>Tindakan ini tidak dapat diurungkan.
            </p>
            <div class="flex gap-3">
                <button id="cancelConfirm"
                    class="flex-1 py-3 rounded-xl border border-red-200 text-red-600 font-semibold text-sm hover:bg-red-50 transition-colors">
                    Batal
                </button>
                <button id="confirmComplete"
                    class="flex-1 py-3 rounded-xl bg-[#005c20] text-white font-bold text-sm hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span id="confirmSpinner"
                        class="hidden material-symbols-outlined text-sm animate-spin">progress_activity</span>
                    Selesaikan
                </button>
            </div>
        </div>
    </div>
    
    {{-- Cancel Modal --}}
    <div id="cancelModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-8 animate-fade-in">
            <div class="flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mx-auto mb-5">
                <span class="material-symbols-outlined text-3xl text-red-500"
                    style="font-variation-settings:'FILL' 1">cancel</span>
            </div>
            <h2 class="font-manrope text-xl font-extrabold text-on-surface text-center mb-2">Batalkan Pesanan?</h2>
            <p class="text-sm text-on-surface-variant text-center mb-8">
                Pesanan ini akan dibatalkan dan stok produk akan <strong>dikembalikan</strong> secara otomatis.<br>
                Tindakan ini tidak dapat diurungkan.
            </p>
            <div class="flex gap-3">
                <button id="closeCancelModal"
                    class="flex-1 py-3 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition-colors">
                    Kembali
                </button>
                <button id="confirmCancel"
                    class="flex-1 py-3 rounded-xl bg-red-500 text-white font-bold text-sm hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span id="cancelSpinner"
                        class="hidden material-symbols-outlined text-sm animate-spin">progress_activity</span>
                    Ya, Batalkan
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endpush