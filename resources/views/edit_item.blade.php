@extends('layouts.app')

@section('title', 'Edit Item - Koperasi')
@section('page_title', 'Manajemen Gudang')

@section('content')

    <div class="flex items-center gap-3 my-6 mx-4">
        <a href="{{ route('storage') }}"
            class="p-2 hover:bg-surface-container rounded-full transition-colors text-on-surface-variant">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <h1 class="font-manrope text-2xl font-extrabold text-primary tracking-wide">Edit Produk</h1>
    </div>

    <form method="POST" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mx-6 mb-8">

            <!-- Left Side -->
            <div class="lg:col-span-8 space-y-8">
                <div class="bg-white p-8 rounded-xl shadow-sm">
                    <h2 class="text-xl font-bold mb-8 text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined">inventory_2</span> Detail Produk
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                        <!-- Product Name -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-on-surface-variant mb-2">Nama Produk</label>
                            <input name="name_product"
                                class="w-full bg-surface-container-low border-none rounded-lg p-4 focus:ring-2 focus:ring-primary-container text-on-surface placeholder-outline-variant @error('name_product') ring-2 ring-red-500 @enderror"
                                placeholder="Contoh: Susu Sapi Organik A2" type="text"
                                value="{{ old('name_product', $product->name_product) }}" required />
                            @error('name_product')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-2">Kategori</label>
                            <div class="relative">
                                <select name="categories_id"
                                    class="w-full bg-surface-container-low border-none rounded-lg p-4 focus:ring-2 focus:ring-primary-container appearance-none text-on-surface @error('categories_id') ring-2 ring-red-500 @enderror"
                                    required>
                                    <option disabled value="">Pilih Kategori</option>
                                    @forelse ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('categories_id', $product->categories_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name_categories }}
                                        </option>
                                    @empty
                                        <option disabled value="">Belum ada kategori tersedia</option>
                                    @endforelse
                                </select>
                                <span
                                    class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-outline">expand_more</span>
                            </div>
                            @error('categories_id')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Unit (Autocomplete) -->
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-2">Satuan</label>
                            <div class="relative" id="unitWrapper">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline pointer-events-none"
                                    style="font-size:18px">straighten</span>
                                <input type="text" id="unitInput" name="unit_name" autocomplete="off"
                                    placeholder="Cari atau ketik satuan... (pcs, kg, liter)"
                                    value="{{ old('unit_name', $product->unit?->name_unit) }}"
                                    class="w-full bg-surface-container-low border-none rounded-lg p-4 pl-11 focus:ring-2 focus:ring-primary-container text-on-surface placeholder-outline-variant @error('unit_id') ring-2 ring-red-500 @enderror" />
                                <input type="hidden" id="unitId" name="unit_id"
                                    value="{{ old('unit_id', $product->unit_id) }}" />
                            </div>
                            @error('unit_id')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-2">Stok Saat Ini</label>
                            <input name="stock" id="stockField"
                                class="w-full bg-surface-container rounded-lg p-4 text-on-surface font-bold border-none cursor-not-allowed"
                                type="number" value="{{ old('stock', $product->stock) }}"
                                readonly />
                            @error('stock')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror

                            {{-- Tambah Stok --}}
                            <div class="mt-3 flex items-center gap-3">
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-teal-600">+</span>
                                    <input id="addStockInput" type="number" min="1"
                                        placeholder="Jumlah tambahan"
                                        class="w-full bg-surface-container-low border-none rounded-lg py-3 pl-7 pr-4 focus:ring-2 focus:ring-primary-container text-on-surface text-sm" />
                                </div>
                                <button type="button" id="addStockBtn"
                                    class="flex items-center gap-1.5 px-4 py-3 rounded-lg bg-teal-700 text-white text-sm font-bold hover:bg-teal-800 active:scale-95 transition-all whitespace-nowrap">
                                    <span class="material-symbols-outlined text-base">add_circle</span>
                                    Tambah
                                </button>
                                <button type="button" id="cancelStockBtn"
                                    class="hidden flex items-center gap-1.5 px-4 py-3 rounded-lg bg-surface-container-high text-on-surface-variant text-sm font-bold hover:bg-red-50 hover:text-red-600 active:scale-95 transition-all whitespace-nowrap">
                                    <span class="material-symbols-outlined text-base">undo</span>
                                    Batalkan
                                </button>
                            </div>
                            <p id="stockHint" class="text-xs text-on-surface-variant mt-2 hidden">
                                <span class="material-symbols-outlined align-middle text-teal-600" style="font-size:13px">info</span>
                                Stok akan menjadi <strong id="stockPreview" class="text-teal-700"></strong>
                            </p>
                        </div>

                        <!-- Harga Jual -->
                        <div class="relative">
                            <label class="block text-sm font-semibold text-on-surface-variant mb-2">Harga</label>
                            <div class="flex items-center">
                                <span class="absolute left-4 font-bold text-on-surface-variant">Rp</span>
                                <input name="price"
                                    class="w-full bg-surface-container-low border-none rounded-lg p-4 pl-12 focus:ring-2 focus:ring-primary-container text-on-surface @error('price') ring-2 ring-red-500 @enderror"
                                    placeholder="0" type="number" min="0" step="0.01"
                                    value="{{ old('price', $product->price) }}" required />
                            </div>
                            @error('price')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="lg:col-span-4 space-y-8">

                <!-- Image Upload -->
                <section class="bg-white p-8 rounded-xl shadow-sm text-center">
                    <h2 class="text-sm font-bold mb-4 text-on-surface-variant uppercase tracking-widest">
                        Foto Produk
                    </h2>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png"
                        style="display:none;" />
                    <div id="imageUploadBox"
                        class="relative aspect-square rounded-xl bg-surface-container-low border-2 border-dashed border-outline-variant flex flex-col items-center justify-center p-6 cursor-pointer hover:bg-surface-variant transition-colors overflow-hidden">

                        <img id="previewImage" class="absolute inset-0 w-full h-full object-cover"
                            src="{{ $product->image ? asset('storage/' . $product->image) : '/assets/pictures/produk.jpg' }}"
                            onerror="this.src='/assets/pictures/produk.jpg'" alt="Foto Produk" />

                        <div id="uploadOverlay"
                            class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center opacity-0 hover:opacity-100 transition-opacity z-10">
                            <span class="material-symbols-outlined text-4xl text-white mb-2">photo_camera</span>
                            <p class="text-white font-bold text-sm">Ganti Foto</p>
                        </div>

                        {{-- State: foto baru dipilih --}}
                        <div id="uploadSuccess" class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 hidden">
                            <span
                                class="bg-secondary text-white text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size:14px">check_circle</span>
                                Foto baru dipilih
                            </span>
                        </div>
                    </div>

                    {{-- Info foto saat ini --}}
                    @if ($product->image)
                        <p class="text-xs text-on-surface-variant mt-3">
                            <span class="material-symbols-outlined align-middle" style="font-size:14px">image</span>
                            Foto tersimpan. Pilih baru untuk mengganti.
                        </p>
                    @else
                        <p class="text-xs text-on-surface-variant mt-3">Belum ada foto. Klik untuk menunggah.</p>
                    @endif
                </section>

                <!-- Actions -->
                <section class="bg-teal-50 border border-teal-100 p-6 rounded-xl shadow-sm">
                    <div class="space-y-4">
                        <button type="submit"
                            class="w-full py-4 rounded-full font-bold text-white shadow-lg flex items-center justify-center gap-2 transition-all hover:scale-95 hover:shadow-xl"
                            style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);">
                            <span class="material-symbols-outlined">save</span>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('storage') }}"
                            class="w-full bg-transparent border-2 border-teal-300 py-4 rounded-full font-bold text-teal-700 hover:bg-teal-100 transition-colors block text-center no-underline">
                            Batalkan
                        </a>
                    </div>
                </section>

            </div>
        </div>
    </form>

    {{-- Error toast --}}
    @if ($errors->any())
        <div class="fixed top-4 right-4 px-6 py-4 rounded-lg font-semibold shadow-lg z-50 bg-red-500 text-white"
            style="max-width:380px">
            <p class="font-bold mb-1">Terjadi kesalahan:</p>
            <ul class="text-sm list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <script>
        window.EDIT_CONFIG = {
            units: {!! json_encode($units) !!},
            currentUnitId: {{ $product->unit_id ?? 'null' }},
            currentUnitName: "{{ addslashes($product->unit->name_unit ?? '') }}",
            originalStock: {{ $product->stock }},
        };
    </script>
@endsection