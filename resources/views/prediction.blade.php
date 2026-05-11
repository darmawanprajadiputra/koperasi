@extends('layouts.app')

@section('title', 'Prediksi Stok')

@section('content')
<div class="max-w-6xl mx-auto px-8 py-12">
  <!-- Header Section -->
  <div class="mb-12">
    <h2 class="font-headline font-extrabold text-4xl text-primary tracking-tight mb-2">
      Prediksi Stok Produk
    </h2>
  </div>

  <!-- Main Input Bento Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-16">
    <!-- Prediction Input Card -->
    <div class="lg:col-span-7 bg-surface-container-lowest rounded-xl p-8 shadow-[0_12px_32px_rgba(25,28,27,0.04)] relative overflow-hidden group">
      <div class="absolute top-0 right-0 w-32 h-32 bg-secondary-container/20 rounded-bl-full -mr-16 -mt-16"></div>
      <div class="relative z-10">
        <label class="block font-headline font-bold text-on-surface mb-4" for="product-name">Nama Produk</label>
        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-grow relative">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">search</span>
            <input class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-0 focus:ring-2 focus:ring-primary-container rounded-full text-on-surface placeholder-on-surface-variant/50 transition-all font-medium" id="product-name" placeholder="Misal: Beras Pandan Wangi, Jagung Hibrida..." type="text" />
          </div>
          <button class="btn-predict bg-gradient-to-br from-[#00342b] to-[#004d40] text-white px-8 py-4 rounded-full font-headline font-bold text-sm flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 transition-all">
            <span>Dapatkan Rekomendasi</span>
            <span class="material-symbols-outlined text-lg">auto_awesome</span>
          </button>
        </div>
        <div class="mt-6 flex flex-wrap gap-2 items-center">
          <span class="text-xs font-label text-on-surface-variant uppercase tracking-wider mr-2">Pencarian Terakhir:</span>
          <span class="recent-search px-3 py-1 bg-surface-container-high rounded-full text-xs font-medium text-primary hover:bg-primary-container hover:text-white cursor-pointer transition-colors">Beras Merah</span>
          <span class="recent-search px-3 py-1 bg-surface-container-high rounded-full text-xs font-medium text-primary hover:bg-primary-container hover:text-white cursor-pointer transition-colors">Pupuk Organik</span>
          <span class="recent-search px-3 py-1 bg-surface-container-high rounded-full text-xs font-medium text-primary hover:bg-primary-container hover:text-white cursor-pointer transition-colors">Kedelai Impor</span>
        </div>
      </div>
    </div>

    <!-- AI Insight Card -->
    <div class="lg:col-span-5 bg-tertiary-container rounded-xl p-8 text-on-tertiary flex flex-col justify-between relative overflow-hidden">
      <div class="relative z-10">
        <h3 class="font-headline font-bold text-xl mb-4">Mengapa Menggunakan AI?</h3>
        <p class="font-body text-sm leading-relaxed opacity-90">
          Algoritma kami menganalisis curah hujan, fluktuasi harga pasar lokal, dan siklus panen regional untuk memberikan estimasi stok paling presisi bagi koperasi Anda.
        </p>
      </div>
      <div class="mt-8 flex items-center gap-4">
        <div class="flex -space-x-3">
          <img class="w-10 h-10 rounded-full border-2 border-tertiary-container object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAWFkQe2kVo2JuSlLK35krq1QwGXbZBswKOYdaCbqQHLYnNIwwQF-iZZo3DsIS_1N_TFtogp6NmEElaiYp8AantDBTsijik84uCpOpWdnsAh4cs66kvy-XCLXgVK3JAMPq7-Kb136zUKBj0_Wq_Ewy8igdCZdsUuc-JE4Oq27GCSxoVntVrRI_ro6DZwokGI5BRAmXi2o3xdWhZzmGNSoU18iso8YwwokIUzDCJKhYWIQbUjZ9fwd9Ds1Ww4dbthb2TCzzP5DOFR4y4" alt="expert" />
          <img class="w-10 h-10 rounded-full border-2 border-tertiary-container object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB6WRxDjpt29RsW3aNC3ZTz1hvuIqDvMjOCQR-ukGad9eXRPpsWvbR1zREmp-rEdWxxJTVFHJ3guELoVhBN_KMh6RkQ_Sml0N_-6OVe48JqbJJ2MhdJZ2HuV7loAAwbvZ3Mfo5WRMVx9olKx3RaNBGN9ylglcqByuI1Lk-tG8ylCT1I1-76ag6F6GH3jMzPmhWQingTlTi0DZKd1bt1UE00UKK731NYbaugqongKa5MLUCCbuV2J0yiN-enK5VHwa1NYcB74X6F4JEI" alt="expert" />
        </div>
        <span class="text-xs font-medium font-label">Dipercayai oleh 500+ Manajer Gudang</span>
      </div>
    </div>
  </div>

  <!-- Result Section -->
  <section class="mt-16">
    <div class="flex items-center justify-between mb-8">
      <h3 class="font-headline font-bold text-2xl text-primary">
        Hasil Prediksi
      </h3>
      <div class="flex gap-2">
        <span class="px-4 py-2 bg-surface-container-high rounded-full text-xs font-bold text-on-surface-variant flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-secondary"></span> Live Data
        </span>
      </div>
    </div>
    <div id="predictionResult" class="bg-surface-container-low rounded-xl border-2 border-dashed border-outline-variant/30 py-24 flex flex-col items-center text-center px-8">
      <div class="w-20 h-20 bg-surface-container-high rounded-full flex items-center justify-center mb-6">
        <span class="material-symbols-outlined text-4xl text-outline-variant">insights</span>
      </div>
      <h4 class="font-headline font-bold text-xl text-on-surface mb-2">
        Belum Ada Produk Dipilih
      </h4>
      <p class="text-on-surface-variant max-w-md mx-auto mb-8">
        Masukkan nama produk di atas untuk memulai analisis stok. AI akan mengolah data secara real-time dan menampilkan grafik estimasi di sini.
      </p>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-3xl">
        <div class="bg-surface-container-lowest p-6 rounded-xl text-left">
          <span class="material-symbols-outlined text-primary mb-3">trending_up</span>
          <h5 class="font-headline font-bold text-sm mb-1">Tren Permintaan</h5>
          <p class="text-xs text-on-surface-variant">Visualisasi kenaikan atau penurunan minat pasar.</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl text-left">
          <span class="material-symbols-outlined text-primary mb-3">inventory</span>
          <h5 class="font-headline font-bold text-sm mb-1">Rekomendasi Qty</h5>
          <p class="text-xs text-on-surface-variant">Jumlah stok optimal untuk menghindari pemborosan.</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl text-left">
          <span class="material-symbols-outlined text-primary mb-3">event_repeat</span>
          <h5 class="font-headline font-bold text-sm mb-1">Siklus Re-order</h5>
          <p class="text-xs text-on-surface-variant">Jadwal pengadaan barang yang paling efisien.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Seasonal Products Section -->
  <section class="mt-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
      <div>
        <h3 class="font-headline font-extrabold text-2xl text-primary mb-2">
          Komoditas Musim Ini
        </h3>
        <p class="text-on-surface-variant text-sm">
          Produk dengan volatilitas stok tertinggi bulan ini.
        </p>
      </div>
      <button class="text-primary font-headline font-bold text-sm flex items-center gap-2 hover:underline underline-offset-4">
        Lihat Semua Laporan
        <span class="material-symbols-outlined">arrow_forward</span>
      </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Product Card 1 -->
      <div class="seasonal-card bg-surface-container-lowest rounded-xl overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:shadow-lg transition-shadow group">
        <div class="h-48 overflow-hidden">
          <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDN4Fa2n7nIbfhzuAfIvZP4YO9WTjrAvIah7THZOVdnIwu5_xoF5JZI7i64QJcA1mewFmw0qHnhc2RWYHNSxbaUQzum1EM7NQy2OOxEl0MCR7MtyjaM546RBNdOsLd1r2Kwkb5SuQJeNVEJWWc6ue5QtQ7VkTziKm9d8w7IMTqzF3fi0kcW0Q1E-ODib4rQYQn_TINAsPhksTkNjzjPHXfRCQyRqDyQljn6anrS2pdFMZg2ub3G1rZWTAZD2i5LTLZStO53wC4F16Vn" alt="Beras" />
        </div>
        <div class="p-5">
          <div class="flex justify-between items-start mb-2">
            <h4 class="font-headline font-bold text-on-surface">Beras Cianjur</h4>
            <span class="text-secondary font-bold text-sm">Tinggi</span>
          </div>
          <p class="text-xs text-on-surface-variant mb-4">Permintaan naik 15% minggu ini.</p>
          <button class="w-full py-2 rounded-full border border-outline-variant/30 text-xs font-bold text-primary hover:bg-primary hover:text-white transition-colors">Analisis Sekarang</button>
        </div>
      </div>
      <!-- Product Card 2 -->
      <div class="seasonal-card bg-surface-container-lowest rounded-xl overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:shadow-lg transition-shadow group">
        <div class="h-48 overflow-hidden">
          <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDvkoBxekHs77z4cz2qs-lDdeNsmOH8EmsKWh9de7dS1jzP2OK9aCCQqhjiA92nSgU9IjpywWV84-Lkps4jp-vbrfe_KyRHEbse9y-e6WCZU3B4hKEUrL1Oqrj5O4JeI5jpJV14rDTCGTYQOE3GtBF-4l46QCTGDjbDBzRkV3Kv-lsZ8jZiCyapgAvjZBPKVs3Bn4ShkNuAA01JF_LhDfiezB4O_9iNGa7-TVUJ77bv_46cRSmg55kpZIvmU3h9HIyUdevH11eW-kdk" alt="Jagung" />
        </div>
        <div class="p-5">
          <div class="flex justify-between items-start mb-2">
            <h4 class="font-headline font-bold text-on-surface">Jagung Manis</h4>
            <span class="text-on-surface-variant font-bold text-sm">Stabil</span>
          </div>
          <p class="text-xs text-on-surface-variant mb-4">Stok tersedia mencukupi 3 minggu.</p>
          <button class="w-full py-2 rounded-full border border-outline-variant/30 text-xs font-bold text-primary hover:bg-primary hover:text-white transition-colors">Analisis Sekarang</button>
        </div>
      </div>
      <!-- Product Card 3 -->
      <div class="seasonal-card bg-surface-container-lowest rounded-xl overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:shadow-lg transition-shadow group">
        <div class="h-48 overflow-hidden">
          <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBHWyXIP5s8RdE8jiNbvbDfGS22bbg2Y2Q5ERR94xi3-ZFsti_ZT-hgkxzIpGAYoQpUTUMlfRxDRY26WqylxeirdEdTU1jMrgLLYVDNT2EBBmjaHuoamtHdhrp2bd6Mq_geVcxxUWLB69gPcSmYfKBMh7L4H-hvo6vxVq2qzZN0-Q5ZDSenU6lJZMj43PnJrEoLmNJgu826jZ8tQbBzJHKXTWiNeMoRvOyINcojL4moQH1VDtv0oSg9RAQaJpZle1Tjx6lTVTOnp3YI" alt="Cabai" />
        </div>
        <div class="p-5">
          <div class="flex justify-between items-start mb-2">
            <h4 class="font-headline font-bold text-on-surface">Cabai Keriting</h4>
            <span class="text-error font-bold text-sm">Kritis</span>
          </div>
          <p class="text-xs text-on-surface-variant mb-4">Stok menipis karena cuaca buruk.</p>
          <button class="w-full py-2 rounded-full border border-outline-variant/30 text-xs font-bold text-primary hover:bg-primary hover:text-white transition-colors">Analisis Sekarang</button>
        </div>
      </div>
      <!-- Product Card 4 -->
      <div class="seasonal-card bg-surface-container-lowest rounded-xl overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:shadow-lg transition-shadow group">
        <div class="h-48 overflow-hidden">
          <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAFCRTeTPHonTU-9rC3XPka0S4zEU5FEFjj5zWGK37loQ__rJ_bJ6rWjahpiLYyTfXQy7ZmOcYt4ijvuxhYJY9k95nJ4TYezJgDnMmciDcilQ6X9tunuh0bN5vQmXndKiYtdhSxkZtYyjgkvFCPGmPnbgNQc0_u9nxLl86D-pTad2hOJdCG7J_5TpyUPVGKR6v8CcP1s0hE_p5zKR4YulvX69y5wZep0-d0Z-exsxUr299nVdcDZFKyrXiOLy6q1Ksc8Py1QXljMYWw" alt="Kedelai" />
        </div>
        <div class="p-5">
          <div class="flex justify-between items-start mb-2">
            <h4 class="font-headline font-bold text-on-surface">Kedelai Lokal</h4>
            <span class="text-secondary font-bold text-sm">Normal</span>
          </div>
          <p class="text-xs text-on-surface-variant mb-4">Pengadaan baru dijadwalkan besok.</p>
          <button class="w-full py-2 rounded-full border border-outline-variant/30 text-xs font-bold text-primary hover:bg-primary hover:text-white transition-colors">Analisis Sekarang</button>
        </div>
      </div>
    </div>
  </section>
</div>

<script src="{{ asset('js/prediksi.js') }}"></script>
@endsection
