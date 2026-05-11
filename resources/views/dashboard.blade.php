@extends('layouts.app')

@section('title', 'Dashboard - Koperasi')

@section('content')
    <div class="px-8 pt-8 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-extrabold text-teal-900 uppercase mb-2">Dashboard</h1>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-teal-100 rounded-xl">
                        <span class="text-xl">🛒</span>
                    </div>
                    <span class="text-green-600 font-semibold text-xs bg-green-100 px-2 py-1 rounded-full">+12.5%</span>
                </div>
                <p class="text-gray-600 font-medium text-sm mb-1">Pesanan Baru</p>
                <h3 class="text-3xl font-extrabold text-teal-900">1,284</h3>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <span class="text-xl">💳</span>
                    </div>
                    <span class="text-green-600 font-semibold text-xs bg-green-100 px-2 py-1 rounded-full">+8.2%</span>
                </div>
                <p class="text-gray-600 font-medium text-sm mb-1">Pendapatan Koperasi</p>
                <h3 class="text-3xl font-extrabold text-teal-900">Rp 42.8M</h3>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-purple-100 rounded-xl">
                        <span class="text-xl">👥</span>
                    </div>
                    <span class="text-green-600 font-semibold text-xs bg-green-100 px-2 py-1 rounded-full">+4.1%</span>
                </div>
                <p class="text-gray-600 font-medium text-sm mb-1">Jumlah Pemesan</p>
                <h3 class="text-3xl font-extrabold text-teal-900">8,421</h3>
            </div>
        </div>

        <!-- Charts and Activity Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Weekly Order Chart -->
            <div class="lg:col-span-2 bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h2 class="text-xl font-bold text-teal-900">Tren Pesanan Mingguan</h2>
                        <p class="text-sm text-gray-600">Visualisasi performa pesanan dalam 7 hari terakhir</p>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex items-center gap-2 text-xs font-semibold text-teal-900">
                            <span class="w-3 h-3 rounded-full bg-teal-900"></span> Pesanan
                        </span>
                        <span class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                            <span class="w-3 h-3 rounded-full bg-gray-300"></span> Target
                        </span>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="h-64 flex items-end justify-between gap-4 px-2">
                    <div class="flex flex-col items-center gap-3 w-full">
                        <div class="w-full bg-gray-200 rounded-t-lg relative group h-32">
                            <div class="absolute bottom-0 w-full bg-teal-900 rounded-t-lg h-[65%] group-hover:opacity-80 transition-opacity"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600">Sen</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 w-full">
                        <div class="w-full bg-gray-200 rounded-t-lg relative group h-32">
                            <div class="absolute bottom-0 w-full bg-teal-900 rounded-t-lg h-[85%] group-hover:opacity-80 transition-opacity"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600">Sel</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 w-full">
                        <div class="w-full bg-gray-200 rounded-t-lg relative group h-32">
                            <div class="absolute bottom-0 w-full bg-teal-900 rounded-t-lg h-[45%] group-hover:opacity-80 transition-opacity"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600">Rab</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 w-full">
                        <div class="w-full bg-gray-200 rounded-t-lg relative group h-32">
                            <div class="absolute bottom-0 w-full bg-teal-900 rounded-t-lg h-[95%] group-hover:opacity-80 transition-opacity"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600">Kam</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 w-full">
                        <div class="w-full bg-gray-200 rounded-t-lg relative group h-32">
                            <div class="absolute bottom-0 w-full bg-teal-900 rounded-t-lg h-[75%] group-hover:opacity-80 transition-opacity"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600">Jum</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 w-full">
                        <div class="w-full bg-gray-200 rounded-t-lg relative group h-32">
                            <div class="absolute bottom-0 w-full bg-teal-900 rounded-t-lg h-[40%] group-hover:opacity-80 transition-opacity"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600">Sab</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 w-full">
                        <div class="w-full bg-gray-200 rounded-t-lg relative group h-32">
                            <div class="absolute bottom-0 w-full bg-teal-900 rounded-t-lg h-[55%] group-hover:opacity-80 transition-opacity"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600">Min</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-xl font-bold text-teal-900">Aktivitas Terkini</h2>
                    <a href="#" class="text-teal-900 text-xs font-bold hover:underline">Lihat Semua</a>
                </div>

                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-2 h-12 bg-green-600 rounded-full shrink-0"></div>
                        <div class="flex flex-col justify-center">
                            <p class="text-sm font-semibold text-gray-900">Restock Beras Merah Organik</p>
                            <p class="text-xs text-gray-600">Gudang Utama • 12 menit yang lalu</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-2 h-12 bg-blue-600 rounded-full shrink-0"></div>
                        <div class="flex flex-col justify-center">
                            <p class="text-sm font-semibold text-gray-900">Pesanan Baru #ORD-9921</p>
                            <p class="text-xs text-gray-600">Bpk. Ahmad Subarjo • 45 menit yang lalu</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-2 h-12 bg-gray-400 rounded-full shrink-0"></div>
                        <div class="flex flex-col justify-center">
                            <p class="text-sm font-semibold text-gray-900">Update Profil Anggota Baru</p>
                            <p class="text-xs text-gray-600">Admin System • 2 jam yang lalu</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-2 h-12 bg-green-600 rounded-full shrink-0"></div>
                        <div class="flex flex-col justify-center">
                            <p class="text-sm font-semibold text-gray-900">Laporan Bulanan Diterbitkan</p>
                            <p class="text-xs text-gray-600">Keuangan • 5 jam yang lalu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Promotional Section -->
        <section class="bg-teal-900 text-white rounded-xl p-8 flex flex-col md:flex-row md:items-center mb-4 justify-between gap-6">
            <div>
                <h3 class="text-3xl font-extrabold text-white mb-2">Tingkatkan Efisiensi Distribusi</h3>
                <p class="max-w-2xl text-sm text-white/80">
                    Gunakan fitur analitik baru untuk memprediksi permintaan barang dan merencanakan stok koperasi dengan lebih akurat.
                </p>
            </div>
            <button class="px-8 py-3 rounded-full bg-white text-teal-900 font-bold hover:opacity-90 transition-all whitespace-nowrap">
                Mulai Analisis
            </button>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('resources/js/dashboard.js') }}"></script>
    @endpush
@endsection
