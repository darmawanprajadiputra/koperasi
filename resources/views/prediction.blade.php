@extends('layouts.app')

@section('title', 'Prediksi Stok')

@section('content')
    <div class="max-w-7xl mx-8 py-10">
        <div class="mb-10">
            <h2 class="font-headline text-teal-900 font-extrabold text-4xl text-primary tracking-tight mb-2">
                Prediksi Stok Produk
            </h2>
        </div>

        {{-- Input Card --}}
        <div class="bg-surface-container-lowest rounded-xl p-8 shadow-[0_12px_32px_rgba(25,28,27,0.04)] mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <div class="md:col-span-1">
                    <label class="block font-bold text-on-surface mb-2 text-sm">Pilih Produk</label>
                    <select id="product-select"
                        class="w-full px-4 py-3 bg-surface-container-low border-0 focus:ring-2 focus:ring-primary rounded-xl text-on-surface text-sm">
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name_product }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Lead Time --}}
                <div class="md:col-span-1">
                    <label class="block font-bold text-on-surface mb-2 text-sm">Lead Time (hari)</label>
                    <input type="number" id="lead-time" value="7" min="1" max="90"
                        class="w-full px-4 py-3 bg-surface-container-low border-0 focus:ring-2 focus:ring-primary rounded-xl text-on-surface text-sm" />
                </div>

                {{-- Tombol --}}
                <div class="md:col-span-1">
                    <button id="btn-predict"
                        class="w-full bg-gradient-to-br from-[#00342b] to-[#004d40] text-white px-6 py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="btn-text">Hitung Prediksi</span>
                        <span class="material-symbols-outlined text-base">auto_awesome</span>
                    </button>
                </div>

            </div>
        </div>

        {{-- Result Section --}}

        {{-- Empty State --}}
        <div id="result-empty"
            class="bg-surface-container-low rounded-xl border-2 border-dashed border-outline-variant/30 py-20 flex flex-col items-center text-center px-8">
            <div class="w-16 h-16 bg-surface-container-high rounded-full flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-3xl text-outline-variant">insights</span>
            </div>
            <h4 class="font-headline font-bold text-lg text-on-surface mb-2">Belum Ada Produk Dipilih</h4>
            <p class="text-on-surface-variant text-sm max-w-sm">
                Pilih produk dan masukkan lead time, lalu klik Hitung Prediksi.
            </p>
        </div>

        {{-- Loading State --}}
        <div id="result-loading"
            class="hidden bg-surface-container-low rounded-xl py-20 flex flex-col items-center text-center px-8">
            <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-on-surface font-medium">Sedang menghitung prediksi...</p>
            <p class="text-on-surface-variant text-sm mt-1">Proses ini memerlukan beberapa detik</p>
        </div>

        {{-- Result State --}}
        <div id="result-data" class="hidden space-y-4">

            {{-- Info produk & tanggal --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-on-surface-variant mb-1">Hasil Prediksi untuk</p>
                    <h3 id="result-product-name" class="font-headline font-extrabold text-2xl text-primary"></h3>
                </div>
                <div class="text-right">
                    <span
                        class="px-3 py-1 bg-secondary-container rounded-full text-xs font-bold text-on-secondary-container">
                        LSTM + Safety Stock
                    </span>
                    <p id="result-date" class="text-xs text-on-surface-variant mt-2"></p>
                </div>
            </div>

            {{-- Output Utama --}}
            <div class="bg-gradient-to-br from-[#00342b] to-[#004d40] rounded-xl p-10 text-white text-center">
                <p class="text-sm font-medium opacity-75 mb-3 uppercase tracking-wider">
                    Jumlah Stok yang Direkomendasikan
                </p>
                <p id="result-qty" class="text-7xl font-extrabold mb-3"></p>
                <p class="text-sm opacity-60">unit &bull; untuk 30 hari ke depan</p>
            </div>

            {{-- Info ROP --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-500 mt-0.5">warning</span>
                <div>
                    <p class="text-sm font-bold text-amber-800 mb-1">Reorder Point (ROP)</p>
                    <p class="text-sm text-amber-700">
                        Lakukan pemesanan ulang ketika stok mencapai
                        <strong id="result-rop" class="text-amber-900 text-base"></strong> unit.
                        Notifikasi restock akan otomatis muncul di dashboard saat stok menyentuh angka ini.
                    </p>
                </div>
            </div>

        </div>

        {{-- Error State --}}
        <div id="result-error" class="hidden bg-red-50 border border-red-200 rounded-xl p-5 flex items-start gap-3">
            <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
            <p id="result-error-msg" class="text-sm text-red-700"></p>
        </div>

    </div>

    <script>
        const PREDICT_URL = "{{ route('prediction.predict') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/prediction.js') }}"></script>
@endsection
