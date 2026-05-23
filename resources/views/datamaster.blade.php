@extends('layouts.app')

@section('title', 'Data Master')
@section('page_title', 'Data Master')

@push('styles')
    @vite(['resources/css/datamaster.css'])
    <style>
        /* ── Scrollable list container ── */
        .dm-list {
            overflow-y: auto;
            overflow-x: hidden;
            scroll-behavior: smooth;
            /* max-height di-set oleh JS setelah render supaya pas 5 item */

            scrollbar-width: thin;
            scrollbar-color: rgba(120,120,120,.3) transparent;
        }
        .dm-list::-webkit-scrollbar        { width: 4px; }
        .dm-list::-webkit-scrollbar-track  { background: transparent; }
        .dm-list::-webkit-scrollbar-thumb  {
            background: rgba(120,120,120,.3);
            border-radius: 99px;
        }
        .dm-list::-webkit-scrollbar-thumb:hover {
            background: rgba(120,120,120,.55);
        }

        /* Wrapper untuk gradient fade hint */
        .dm-list-wrapper {
            position: relative;
        }
        .dm-list-fade {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 48px;
            background: linear-gradient(to bottom, transparent, var(--dm-card-bg, #ffffff));
            pointer-events: none;
            border-radius: 0 0 8px 8px;
            opacity: 0;
            transition: opacity .25s ease;
        }
        /* Fade muncul hanya kalau ada item tersembunyi di bawah */
        .dm-list-wrapper.has-more .dm-list-fade { opacity: 1; }

        /* Counter kecil di bawah form */
        .dm-list-counter {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.72rem;
            color: #9ca3af;
            padding: 6px 2px 4px;
            user-select: none;
        }
        .dm-list-counter__hint {
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .dm-list-counter__hint .material-symbols-outlined {
            font-size: 14px;
        }
    </style>
@endpush

@section('content')
<div class="datamaster-wrapper">

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="dm-alert dm-alert--success">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="dm-alert dm-alert--error">
            <span class="material-symbols-outlined">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Two Column Grid --}}
    <div class="dm-grid">

        {{-- ===== KOLOM KIRI: MANAJEMEN KATEGORI ===== --}}
        <section class="dm-card">
            <div class="dm-card__header">
                <div>
                    <h3 class="dm-card__title uppercase pt-2">Manajemen Kategori</h3>
                </div>
                <div class="dm-card__icon dm-card__icon--category">
                    <span class="material-symbols-outlined">category</span>
                </div>
            </div>

            {{-- Form Tambah Kategori --}}
            <form action="{{ route('datamaster.category.store') }}" method="POST" class="dm-form" id="formAddCategory">
                @csrf
                <input
                    type="text"
                    name="name_categories"
                    class="dm-input"
                    placeholder="Nama kategori baru..."
                    required
                    autocomplete="off"
                >
                <button type="submit" class="dm-btn dm-btn--primary">
                    <span class="material-symbols-outlined">add</span>
                    Tambah
                </button>
            </form>

            {{-- List Kategori --}}
            <div class="dm-list-wrapper" id="categoryWrapper">
                <div class="dm-list" id="categoryList">
                    @forelse ($categories as $category)
                        <div class="dm-list-item" data-id="{{ $category->id }}">
                            <div class="dm-list-item__left">
                                <div class="dm-list-item__icon dm-list-item__icon--category">
                                    <span class="material-symbols-outlined">label</span>
                                </div>
                                <div class="dm-list-item__info">
                                    <p class="dm-list-item__name" id="category-name-{{ $category->id }}">
                                        {{ $category->name_categories }}
                                    </p>
                                    <p class="dm-list-item__meta">
                                        {{ $category->products_count ?? $category->products()->count() }} Produk Terkait
                                    </p>
                                </div>
                            </div>
                            <div class="dm-list-item__actions">
                                <form action="{{ route('datamaster.category.destroy', $category->id) }}" method="POST" class="dm-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dm-action-btn dm-action-btn--delete" title="Hapus"
                                        onclick="return confirm('Hapus kategori \'{{ $category->name_categories }}\'?')">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="dm-empty-state">
                            <span class="material-symbols-outlined">inbox</span>
                            <p>Belum ada kategori. Tambahkan yang pertama!</p>
                        </div>
                    @endforelse
                </div>
                <div class="dm-list-fade"></div>
            </div>
        </section>

        <!-- Kolom Unit-->
        <section class="dm-card">
            <div class="dm-card__header">
                <div>
                    <h3 class="dm-card__title uppercase pt-2">Manajemen Satuan</h3>
                </div>
                <div class="dm-card__icon dm-card__icon--unit">
                    <span class="material-symbols-outlined">straighten</span>
                </div>
            </div>

            {{-- Form Tambah Satuan --}}
            <form action="{{ route('datamaster.unit.store') }}" method="POST" class="dm-form" id="formAddUnit">
                @csrf
                <input
                    type="text"
                    name="name_unit"
                    class="dm-input"
                    placeholder="Nama satuan (ex: Liter)..."
                    required
                    autocomplete="off"
                >
                <button type="submit" class="dm-btn dm-btn--secondary">
                    <span class="material-symbols-outlined">add_circle</span>
                    Tambah
                </button>
            </form>

            {{-- List Satuan --}}
            <div class="dm-list-wrapper" id="unitWrapper">
                <div class="dm-list" id="unitList">
                    @forelse ($units as $unit)
                        <div class="dm-list-item" data-id="{{ $unit->id }}">
                            <div class="dm-list-item__left">
                                <div class="dm-list-item__icon dm-list-item__icon--unit">
                                    <span class="material-symbols-outlined">straighten</span>
                                </div>
                                <div class="dm-list-item__info">
                                    <p class="dm-list-item__name" id="unit-name-{{ $unit->id }}">
                                        {{ $unit->name_unit }}
                                    </p>
                                    <p class="dm-list-item__meta">
                                        {{ $unit->products_count ?? $unit->products()->count() }} Produk Terkait
                                    </p>
                                </div>
                            </div>
                            <div class="dm-list-item__actions">
                                <form action="{{ route('datamaster.unit.destroy', $unit->id) }}" method="POST" class="dm-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dm-action-btn dm-action-btn--delete" title="Hapus"
                                        onclick="return confirm('Hapus satuan \'{{ $unit->name_unit }}\'?')">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="dm-empty-state">
                            <span class="material-symbols-outlined">inbox</span>
                            <p>Belum ada satuan. Tambahkan yang pertama!</p>
                        </div>
                    @endforelse
                </div>
                <div class="dm-list-fade"></div>
            </div>
        </section>

    </div>
</div>

{{-- Hidden Edit Modal --}}
<div class="dm-modal-overlay" id="editModal">
    <div class="dm-modal">
        <div class="dm-modal__header">
            <h4 class="dm-modal__title" id="editModalTitle">Edit</h4>
            <button class="dm-modal__close" onclick="closeEditModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="editForm" method="POST" class="dm-modal__body">
            @csrf
            @method('PUT')
            <input type="text" id="editInput" name="name_value" class="dm-input" required>
            <div class="dm-modal__footer">
                <button type="button" class="dm-btn dm-btn--ghost" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="dm-btn dm-btn--primary">
                    <span class="material-symbols-outlined">save</span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/datamaster.js'])
    <script>
    (function () {
        const VISIBLE_ITEMS = 5;

        function initScrollList(listId, wrapperId) {
            var list    = document.getElementById(listId);
            var wrapper = document.getElementById(wrapperId);
            if (!list || !wrapper) return;

            var firstItem = list.querySelector('.dm-list-item');
            if (!firstItem) return;

            var style      = window.getComputedStyle(firstItem);
            var marginTop  = parseFloat(style.marginTop)    || 0;
            var marginBot  = parseFloat(style.marginBottom) || 0;
            var itemHeight = firstItem.offsetHeight + marginTop + marginBot;

            var maxH = itemHeight * VISIBLE_ITEMS;
            list.style.maxHeight = maxH + 'px';

            function updateFade() {
                var atBottom = list.scrollTop + list.clientHeight >= list.scrollHeight - 2;
                var hasHidden = list.scrollHeight > list.clientHeight + 2;
                wrapper.classList.toggle('has-more', hasHidden && !atBottom);
            }

            updateFade();
            list.addEventListener('scroll', updateFade, { passive: true });
        }

        document.addEventListener('DOMContentLoaded', function () {
            initScrollList('categoryList', 'categoryWrapper');
            initScrollList('unitList',     'unitWrapper');
        });
    })();
    </script>
@endpush