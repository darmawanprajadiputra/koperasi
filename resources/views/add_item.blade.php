@extends('layouts.app')

@section('title', 'Tambah Item - Koperasi')

@section('content')

    <div class="mt-12 mb-8 mx-6">
        <div class="flex items-center gap-2 text-2xl text-on-surface-variant uppercase tracking-widest font-semibold">
            <span>Manajemen Gudang</span>
            <span class="material-symbols-outlined text-xs" style="font-size: 14px">chevron_right</span>
            <span class="text-primary font-bold">Tambah Barang Baru</span>
        </div>
    </div>

    <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mx-6 mb-8">

            <!-- Left Side -->
            <div class="lg:col-span-8 space-y-8">
                <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm">
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
                                value="{{ old('name_product') }}" required />
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
                                    <option disabled selected value="">Pilih Kategori</option>
                                    @forelse ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('categories_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name_categories }}
                                        </option>
                                    @empty
                                        <option disabled value="">Belum ada kategori tersedia</option>
                                    @endforelse
                                </select>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-outline">expand_more</span>
                            </div>
                            @error('categories_id')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-2">Stok</label>
                            <input name="stock"
                                class="w-full bg-surface-container-low border-none rounded-lg p-4 focus:ring-2 focus:ring-primary-container text-on-surface @error('stock') ring-2 ring-red-500 @enderror"
                                placeholder="0" type="number" min="0" value="{{ old('stock', 0) }}" required />
                            @error('stock')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Harga Jual -->
                        <div class="relative">
                            <label class="block text-sm font-semibold text-on-surface-variant mb-2">Harga Jual</label>
                            <div class="flex items-center">
                                <span class="absolute left-4 font-bold text-on-surface-variant">Rp</span>
                                <input name="price"
                                    class="w-full bg-surface-container-low border-none rounded-lg p-4 pl-12 focus:ring-2 focus:ring-primary-container text-on-surface @error('price') ring-2 ring-red-500 @enderror"
                                    placeholder="0" type="number" min="0" step="0.01"
                                    value="{{ old('price', 0) }}" required />
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
                <section class="bg-surface-container-lowest p-8 rounded-xl shadow-sm text-center">
                    <h2 class="text-sm font-bold mb-4 text-on-surface-variant uppercase tracking-widest">
                        Foto Produk <span class="text-outline font-normal normal-case">(opsional)</span>
                    </h2>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png" style="display:none;" />
                    <div id="imageUploadBox"
                        class="relative aspect-square rounded-xl bg-surface-container-low border-2 border-dashed border-outline-variant flex flex-col items-center justify-center p-6 cursor-pointer hover:bg-surface-variant transition-colors overflow-hidden">
                        <img id="previewImage" class="absolute inset-0 w-full h-full object-cover hidden" src="" alt="" />
                        <div id="uploadPrompt" class="relative z-10 flex flex-col items-center">
                            <span class="material-symbols-outlined text-4xl text-primary mb-2">cloud_upload</span>
                            <p class="text-primary font-bold">Pilih atau Seret Foto</p>
                            <p class="text-xs text-on-surface-variant mt-2 px-4 leading-relaxed">Format JPG, PNG (Maks. 5MB).</p>
                        </div>
                        <div id="uploadSuccess" class="relative z-10 flex-col items-center hidden">
                            <span class="material-symbols-outlined text-4xl text-secondary mb-2">check_circle</span>
                            <p class="text-secondary font-bold">Foto Siap</p>
                            <p class="text-xs text-on-surface-variant mt-2">Klik untuk mengganti</p>
                        </div>
                    </div>
                </section>

                <!-- Actions -->
                <section class="bg-primary-container p-6 rounded-xl shadow-sm">
                    <div class="space-y-4">
                        <button type="submit"
                            class="w-full btn-gradient py-4 rounded-full font-bold text-white shadow-xl hover:scale-95 transition-transform flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">save</span>
                            Simpan Barang
                        </button>
                        <a href="{{ route('storage') }}"
                            class="w-full bg-transparent border-2 border-on-primary-container/30 py-4 rounded-full font-bold text-on-primary-container hover:bg-on-primary-container/10 transition-colors block text-center no-underline">
                            Batalkan
                        </a>
                    </div>
                </section>

            </div>
        </div>
    </form>

    {{-- Error & Success toast --}}
    @if ($errors->any())
        <div class="fixed top-4 right-4 px-6 py-4 rounded-lg font-semibold shadow-lg z-50 bg-red-500 text-white" style="max-width:380px">
            <p class="font-bold mb-1">Terjadi kesalahan:</p>
            <ul class="text-sm list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <script>
        const imageInput   = document.getElementById('image');
        const uploadBox    = document.getElementById('imageUploadBox');
        const previewImage = document.getElementById('previewImage');
        const uploadPrompt = document.getElementById('uploadPrompt');
        const uploadSuccess = document.getElementById('uploadSuccess');

        uploadBox.addEventListener('click', () => imageInput.click());

        uploadBox.addEventListener('dragover', e => { e.preventDefault(); });
        uploadBox.addEventListener('drop', e => {
            e.preventDefault();
            if (e.dataTransfer.files.length > 0) {
                imageInput.files = e.dataTransfer.files;
                handleImageChange();
            }
        });

        imageInput.addEventListener('change', handleImageChange);

        function handleImageChange() {
            const file = imageInput.files[0];
            if (!file) return;

            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                alert('Hanya file JPG dan PNG yang diizinkan');
                imageInput.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file tidak boleh lebih dari 5MB');
                imageInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                previewImage.src = e.target.result;
                previewImage.classList.remove('hidden');
                uploadPrompt.classList.add('hidden');
                uploadSuccess.classList.remove('hidden');
                uploadSuccess.classList.add('flex');
            };
            reader.readAsDataURL(file);
        }
    </script>

@endsection