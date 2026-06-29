@extends('layouts.app')

@section('title', 'Prediksi Stok')
@section('page_title', 'Prediksi Stok')

@section('content')
    <div class="max-w-7xl mx-8 py-10">

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
                    <label class="block font-bold text-on-surface mb-2 text-sm">Pesanan Tiba (hari)</label>
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
                <div id="result-mape-container" class="hidden mt-3">
                    <p class="text-xs opacity-50 uppercase tracking-wider mb-1">Akurasi Model (MAPE)</p>
                    <p id="result-mape-value" class="text-2xl font-bold"></p>
                    <p id="result-mape-label" class="text-xs opacity-60 mt-0.5"></p>
                </div>
            </div>

            {{-- ROP & Status Stok --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- ROP Info --}}
                <div class="bg-surface-container-low rounded-xl p-5 flex items-start gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant mt-0.5">moving</span>
                    <div>
                        <p class="text-xs text-on-surface-variant mb-1 font-medium uppercase tracking-wide">Reorder Point
                            (ROP)</p>
                        <p class="text-3xl font-extrabold text-on-surface mb-1">
                            <span id="result-rop"></span>
                            <span class="text-base font-normal text-on-surface-variant ml-1">unit</span>
                        </p>
                        <p class="text-xs text-on-surface-variant">Batas minimum stok sebelum perlu restock</p>
                    </div>
                </div>

                {{-- Kondisi AMAN (stok >= ROP) --}}
                <div id="result-stock-safe"
                    class="hidden bg-green-50 border border-green-200 rounded-xl p-5 flex items-start gap-3">
                    <span class="material-symbols-outlined text-green-600 mt-0.5">check_circle</span>
                    <div>
                        <p class="text-xs text-green-700 mb-1 font-medium uppercase tracking-wide">Status Stok &mdash; Aman
                        </p>
                        <p class="text-3xl font-extrabold text-green-800 mb-1">
                            <span id="result-current-stock-safe"></span>
                            <span class="text-base font-normal text-green-600 ml-1">unit tersedia</span>
                        </p>
                        <p class="text-xs text-green-700">
                            Stok saat ini <strong>di atas ROP</strong>. Belum perlu melakukan pemesanan ulang.
                        </p>
                    </div>
                </div>

                {{-- Kondisi BAHAYA (stok < ROP) --}}
                <div id="result-stock-danger"
                    class="hidden bg-amber-50 border border-amber-200 rounded-xl p-5 flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-500 mt-0.5">warning</span>
                    <div>
                        <p class="text-xs text-amber-700 mb-1 font-medium uppercase tracking-wide">Status Stok &mdash; Perlu
                            Restock</p>
                        <p class="text-3xl font-extrabold text-amber-900 mb-1">
                            <span id="result-current-stock-danger"></span>
                            <span class="text-base font-normal text-amber-600 ml-1">unit tersedia</span>
                        </p>
                        <p class="text-xs text-amber-700">
                            Stok saat ini <strong>di bawah ROP</strong>. Segera lakukan pemesanan ulang untuk menghindari
                            kehabisan stok.
                        </p>
                    </div>
                </div>

            </div>

        </div>

        {{-- Error State --}}
        <div id="result-error" class="hidden bg-red-50 border border-red-200 rounded-xl p-5 flex items-start gap-3">
            <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
            <p id="result-error-msg" class="text-sm text-red-700"></p>
        </div>

        <!-- Riwayat Prediksi -->
        <div class="mt-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-headline font-extrabold text-xl uppercase text-on-surface">Riwayat Prediksi Produk</h2>
                </div>
                <span
                    class="px-3 py-1 bg-surface-container-high rounded-full text-xs font-semibold text-on-surface-variant">
                    {{ $historyRows->count() }} produk
                </span>
            </div>

            <div class="bg-surface-container-lowest rounded-xl shadow-[0_12px_32px_rgba(25,28,27,0.04)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gradient-to-r from-[#00342b] to-[#004d40] text-white">
                                <th class="text-left px-5 py-4 font-semibold text-xs uppercase tracking-wider">Nama Produk
                                </th>
                                <th class="text-center px-5 py-4 font-semibold text-xs uppercase tracking-wider">Rekomendasi
                                    Stok</th>
                                <th class="text-center px-5 py-4 font-semibold text-xs uppercase tracking-wider">Stok
                                    Tersedia</th>
                                <th class="text-center px-5 py-4 font-semibold text-xs uppercase tracking-wider">Stok ROP
                                </th>
                                <th class="text-center px-5 py-4 font-semibold text-xs uppercase tracking-wider">Tanggal
                                    Prediksi</th>
                                <th class="text-center px-5 py-4 font-semibold text-xs uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body" class="divide-y divide-outline-variant/10">
                            @foreach ($historyRows as $row)
                                @php
                                    $hasPrediction = $row['safety_stock'] !== null;
                                    $isAman = $hasPrediction && $row['stock'] >= $row['rop'];
                                @endphp
                                <tr class="hover:bg-surface-container-low transition-colors duration-150"
                                    id="history-row-{{ $row['product_id'] }}" data-product-id="{{ $row['product_id'] }}">

                                    {{-- Nama Produk --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="font-semibold text-on-surface">{{ $row['product_name'] }}</span>
                                        </div>
                                    </td>

                                    {{-- Safety Stock --}}
                                    <td class="px-5 py-4 text-center">
                                        @if ($hasPrediction)
                                            <span
                                                class="history-safety-stock font-bold text-on-surface">{{ $row['safety_stock'] }}</span>
                                            <span class="text-xs text-on-surface-variant ml-1">unit</span>
                                        @else
                                            <span class="history-safety-stock text-outline-variant font-medium">-</span>
                                        @endif
                                    </td>

                                    {{-- Stok Tersedia --}}
                                    <td class="px-5 py-4 text-center">
                                        <span class="history-stock font-bold text-on-surface">{{ $row['stock'] }}</span>
                                        <span class="text-xs text-on-surface-variant ml-1">unit</span>
                                    </td>

                                    {{-- Stok ROP --}}
                                    <td class="px-5 py-4 text-center">
                                        @if ($hasPrediction)
                                            <span class="history-rop font-bold text-on-surface">{{ $row['rop'] }}</span>
                                            <span class="text-xs text-on-surface-variant ml-1">unit</span>
                                        @else
                                            <span class="history-rop text-outline-variant font-medium">-</span>
                                        @endif
                                    </td>

                                    {{-- Tanggal Prediksi --}}
                                    <td class="px-5 py-4 text-center">
                                        @if ($hasPrediction)
                                            <span class="history-tanggal text-xs text-on-surface-variant">
                                                {{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d F Y') }}
                                            </span>
                                        @else
                                            <span class="history-tanggal text-outline-variant font-medium">-</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-5 py-4 text-center">
                                        @if (!$hasPrediction)
                                            <span
                                                class="history-status inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-surface-container-high text-outline-variant">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-outline-variant inline-block"></span>
                                                Belum Diprediksi
                                            </span>
                                        @elseif ($isAman)
                                            <span
                                                class="history-status inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                                Aman
                                            </span>
                                        @else
                                            <span
                                                class="history-status inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>
                                                Perlu Restock
                                            </span>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($historyRows->isEmpty())
                    <div class="py-16 text-center">
                        <span class="material-symbols-outlined text-4xl text-outline-variant">table_rows</span>
                        <p class="text-on-surface-variant text-sm mt-2">Belum ada data produk.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <script>
        const PREDICT_URL = "{{ route('prediction.predict') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/prediction.js') }}"></script>
@endsection
