@extends('layouts.app')

@section('title', 'Dashboard - Koperasi')
@section('page_title', 'Dashboard')

@section('content')
    <div class="px-8 pt-10 max-w-7xl mx-auto">

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

            {{-- Pesanan Baru --}}
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-teal-100 rounded-xl">
                        <span class="text-xl">🛒</span>
                    </div>
                    <span
                        class="text-xs font-semibold px-2 py-1 rounded-full
                        {{ $orderChange['positive'] ? 'text-green-600 bg-green-100' : 'text-red-500 bg-red-100' }}">
                        {{ $orderChange['label'] }}
                    </span>
                </div>
                <p class="text-gray-600 font-medium text-sm mb-1">Pesanan Baru</p>
                <h3 class="text-3xl font-extrabold text-teal-900">{{ $totalOrders }}</h3>
                <p class="text-xs text-gray-400 mt-1">vs. minggu lalu</p>
            </div>

            {{-- Pendapatan Koperasi --}}
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <span class="text-xl">💳</span>
                    </div>
                    <span
                        class="text-xs font-semibold px-2 py-1 rounded-full
                        {{ $revenueChange['positive'] ? 'text-green-600 bg-green-100' : 'text-red-500 bg-red-100' }}">
                        {{ $revenueChange['label'] }}
                    </span>
                </div>
                <p class="text-gray-600 font-medium text-sm mb-1">Pendapatan Koperasi</p>
                <h3 class="text-3xl font-extrabold text-teal-900">{{ $revenue }}</h3>
                <p class="text-xs text-gray-400 mt-1">vs. minggu lalu</p>
            </div>

            {{-- Jumlah Pemesan --}}
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-purple-100 rounded-xl">
                        <span class="text-xl">👥</span>
                    </div>
                    <span
                        class="text-xs font-semibold px-2 py-1 rounded-full
                        {{ $customerChange['positive'] ? 'text-green-600 bg-green-100' : 'text-red-500 bg-red-100' }}">
                        {{ $customerChange['label'] }}
                    </span>
                </div>
                <p class="text-gray-600 font-medium text-sm mb-1">Jumlah Pemesan</p>
                <h3 class="text-3xl font-extrabold text-teal-900">{{ $totalCustomers }}</h3>
                <p class="text-xs text-gray-400 mt-1">vs. minggu lalu</p>
            </div>

        </div>

        <!-- Charts and Activity Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <div class="lg:col-span-2 bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-20">
                    <div>
                        <h2 class="text-xl font-bold text-teal-900">Tren Pesanan 7 Hari Terakhir</h2>
                        <p class="text-sm text-gray-600">Jumlah transaksi unik per hari</p>
                    </div>
                    <span class="flex items-center gap-2 text-xs font-semibold text-teal-900">
                        <span class="w-3 h-3 rounded-full bg-teal-900 inline-block"></span> Pesanan
                    </span>
                </div>

                {{-- Bar Chart --}}
                @php
                    $maxVal = max(array_merge($orderTrend, [1]));
                @endphp
                <div class="h-64 flex items-end justify-between gap-4 px-2">
                    @foreach ($orderTrend as $i => $value)
                        @php
                            $heightPct = round(($value / $maxVal) * 100);
                            $heightPct = max($heightPct, 4);
                        @endphp
                        <div class="flex flex-col items-center gap-3 w-full group">
                            <div class="relative w-full h-32">
                                <span
                                    class="absolute -top-6 left-1/2 -translate-x-1/2
                                             text-[10px] font-bold text-teal-900
                                             opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    {{ $value }}
                                </span>
                                <div class="absolute bottom-0 w-full bg-teal-900 rounded-t-lg
                                            group-hover:opacity-75 transition-opacity"
                                    style="height: {{ $heightPct }}%">
                                </div>
                                <div class="absolute bottom-0 w-full bg-gray-200 rounded-t-lg -z-10 h-full"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-600">{{ $dayLabels[$i] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-teal-900">Aktivitas Terkini</h2>
                </div>

                <div class="overflow-y-auto flex-1 pr-1 space-y-5"
                    style="max-height: 370px; scrollbar-width: thin; scrollbar-color: #0f766e transparent;">

                    {{-- Notifikasi Restock --}}
                    @if (!empty($restockAlerts))
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-orange-500 mb-3">⚠ Perlu Restock
                            </p>
                            @foreach ($restockAlerts as $alert)
                                <div class="flex gap-3 mb-3">
                                    <div class="w-2 min-h-[48px] rounded-full shrink-0 bg-orange-400"></div>
                                    <div class="flex-1 bg-orange-50 border border-orange-200 rounded-lg px-3 py-2">
                                        <p class="text-sm font-semibold text-orange-800">{{ $alert['product_name'] }}</p>
                                        <p class="text-xs text-orange-600 mt-0.5">
                                            Stok <span class="font-bold">{{ $alert['current_stock'] }}</span> unit
                                            &mdash; ROP <span class="font-bold">{{ $alert['rop'] }}</span> unit
                                        </p>
                                        <a href="/prediction"
                                            class="inline-block mt-1 text-[10px] font-bold text-orange-700 hover:underline">
                                            Lihat Prediksi →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <hr class="border-gray-100">
                    @endif

                    {{-- Aktivitas Transaksi --}}
                    @if (!empty($restockAlerts))
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Transaksi Terkini</p>
                    @endif
                    @forelse ($activities as $activity)
                        <div class="flex gap-4">
                            <div
                                class="w-2 h-12 rounded-full shrink-0
                                {{ $activity['status'] === 'completed' ? 'bg-green-600' : 'bg-blue-500' }}">
                            </div>
                            <div class="flex flex-col justify-center">
                                <p class="text-sm font-semibold text-gray-900">{{ $activity['title'] }}</p>
                                <p class="text-xs text-gray-600">{{ $activity['detail'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada aktivitas minggu ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Promotional Section -->
        <section
            class="bg-teal-900 text-white rounded-xl p-8 flex flex-col md:flex-row md:items-center mb-4 justify-between gap-6">
            <div>
                <h3 class="text-3xl font-extrabold text-white mb-2">Tingkatkan Efisiensi Distribusi</h3>
                <p class="max-w-2xl text-sm text-white/80">
                    Gunakan fitur analitik untuk memprediksi permintaan barang dan merencanakan stok koperasi dengan lebih
                    akurat.
                </p>
            </div>
            <a href="/prediction"
                class="px-8 py-3 rounded-full bg-white text-teal-900 font-bold hover:opacity-90 transition-all whitespace-nowrap text-center">
                Mulai Analisis
            </a>
        </section>

    </div>
@endsection