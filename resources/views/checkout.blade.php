@extends('layouts.public')

@section('title', 'Checkout')

@section('content')
    <div class="px-8 py-8 max-w-6xl mx-auto">

        <header class="mb-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('shop') }}"
                    class="p-2 hover:bg-surface-container rounded-full transition-colors text-on-surface-variant">
                    <span class="material-symbols-outlined text-xl">arrow_back</span>
                </a>
                <h1 class="font-manrope text-2xl font-extrabold text-primary tracking-tight">Checkout</h1>
            </div>
        </header>

        @if (session('success'))
            <div class="mb-6 flex items-center gap-3 bg-[#e8f5e9] text-[#1b5e20] px-5 py-4 rounded-xl text-sm font-medium">
                <span class="material-symbols-outlined text-base"
                    style="font-variation-settings: 'FILL' 1">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 flex items-start gap-3 bg-error-container text-error px-5 py-4 rounded-xl text-sm font-medium">
                <span class="material-symbols-outlined text-base mt-0.5"
                    style="font-variation-settings: 'FILL' 1">error</span>
                <ul class="list-none space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
            @csrf

            <input type="hidden" name="cart_items" id="cartItemsInput">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

                {{-- Kolom Kiri --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- Informasi Penerima --}}
                    <section
                        class="bg-white rounded-xl p-8 transition-all hover:shadow-sm border border-outline-variant/10">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="material-symbols-outlined text-primary">person</span>
                            <h2 class="text-xl font-bold font-manrope">Informasi Penerima</h2>
                        </div>

                        <div class="space-y-6">

                            {{-- name_customer + recipient --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="name_customer"
                                        class="text-xs font-bold uppercase tracking-wider text-on-surface-variant px-1">
                                        Nama Pemesan <span class="text-error">*</span>
                                    </label>
                                    <input id="name_customer" name="name_customer" type="text" required
                                        value="{{ old('name_customer', auth()->user()?->name ?? '') }}"
                                        placeholder="Nama lengkap"
                                        class="w-full bg-gray-100 border-none rounded-lg px-4 py-3 focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 transition-all text-sm @error('name_customer') ring-2 ring-error @enderror" />
                                </div>

                                <div class="space-y-2">
                                    <label for="recipient"
                                        class="text-xs font-bold uppercase tracking-wider text-on-surface-variant px-1">
                                        Nama Penerima
                                        <span class="text-on-surface-variant font-normal normal-case">(jika berbeda)</span>
                                    </label>
                                    <input id="recipient" name="recipient" type="text" value="{{ old('recipient') }}"
                                        placeholder="Kosongkan jika sama"
                                        class="w-full bg-gray-100 border-none rounded-lg px-4 py-3 focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 transition-all text-sm" />
                                </div>
                            </div>

                            {{-- no_telephone --}}
                            <div class="space-y-2">
                                <label for="no_telephone"
                                    class="text-xs font-bold uppercase tracking-wider text-on-surface-variant px-1">
                                    Nomor Telepon
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-base">phone</span>
                                    </span>
                                    <input id="no_telephone" name="no_telephone" type="tel" inputmode="numeric"
                                        pattern="[0-9]*" maxlength="20"
                                        value="{{ old('no_telephone') }}" placeholder="081234567890"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        class="w-full bg-gray-100 border-none rounded-lg pl-11 pr-4 py-3 focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 transition-all text-sm" />
                                </div>
                            </div>

                            {{-- address --}}
                            <div class="space-y-2">
                                <label for="address"
                                    class="text-xs font-bold uppercase tracking-wider text-on-surface-variant px-1">
                                    Alamat Pengiriman
                                </label>
                                <textarea id="address" name="address" rows="3" placeholder="Jl. Sudirman No. 45, Kelurahan, Kecamatan, Kota"
                                    class="w-full bg-gray-100 border-none rounded-lg px-4 py-3 focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 transition-all text-sm resize-none">{{ old('address') }}</textarea>
                            </div>

                        </div>
                    </section>

                    {{-- Metode Pembayaran --}}
                    <section class="bg-white rounded-xl p-8 border border-outline-variant/10">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
                            <h2 class="text-xl font-bold font-manrope">Metode Pembayaran</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="paymentOptions">

                            <label
                                class="payment-card relative flex flex-col p-5 rounded-xl cursor-pointer ring-2 ring-primary
                                transition-all shadow-[0_20px_40px_rgba(25,28,29,0.04)] border border-outline-variant/10"
                                data-value="tunai">
                                <input class="sr-only" name="payment_method" type="radio" value="tunai"
                                    {{ old('payment_method', 'tunai') === 'tunai' ? 'checked' : '' }} required />
                                <div class="flex justify-between items-start mb-4">
                                    <span class="material-symbols-outlined text-primary text-3xl">payments</span>
                                    <span class="check-icon material-symbols-outlined text-primary"
                                        style="font-variation-settings: 'FILL' 1">check_circle</span>
                                </div>
                                <span class="font-bold text-on-surface font-manrope">Tunai</span>
                                <span class="text-xs text-on-surface-variant mt-1">Bayar langsung saat terima barang</span>
                            </label>

                            <label
                                class="payment-card relative flex flex-col p-5 bg-surface-container-highest/50 rounded-xl cursor-pointer hover:bg-surface-container-highest
                                transition-all shadow-[0_20px_40px_rgba(25,28,29,0.04)] border border-outline-variant/10" data-value="transfer">
                                <input class="sr-only" name="payment_method" type="radio" value="transfer"
                                    {{ old('payment_method') === 'transfer' ? 'checked' : '' }} />
                                <div class="flex justify-between items-start mb-4">
                                    <span
                                        class="material-symbols-outlined text-on-surface-variant text-3xl">account_balance</span>
                                    <span class="check-icon material-symbols-outlined text-primary hidden"
                                        style="font-variation-settings: 'FILL' 1">check_circle</span>
                                </div>
                                <span class="font-bold text-on-surface-variant font-manrope">Transfer Bank</span>
                                <span class="text-xs text-on-surface-variant mt-1">BCA / BRI / BNI</span>
                            </label>
                        </div>

                        {{-- Pilihan Bank (muncul jika Transfer Bank dipilih) --}}
                        <div id="bankOptions" class="hidden mt-4 space-y-3">
                            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant px-1 mb-1">
                                Pilih Bank Tujuan Transfer
                            </p>

                            <label class="bank-option relative flex items-center justify-between gap-4 p-4 rounded-xl cursor-pointer bg-surface-container-highest/50 hover:bg-surface-container-highest transition-all border border-outline-variant/10"
                                data-value="BNI">
                                <input class="sr-only" name="bank_name" type="radio" value="BNI"
                                    {{ old('bank_name') === 'BNI' ? 'checked' : '' }} />
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-lg flex items-center justify-center font-extrabold text-white text-[11px] tracking-tight flex-shrink-0"
                                        style="background:#F37021">BNI</div>
                                    <span class="font-bold text-on-surface-variant font-manrope text-sm truncate">Bank BNI</span>
                                    <span class="check-icon-bank material-symbols-outlined text-primary hidden text-lg flex-shrink-0"
                                        style="font-variation-settings: 'FILL' 1">check_circle</span>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-bold text-on-surface text-sm tracking-wide">2020 1395 70</p>
                                    <p class="text-[11px] text-on-surface-variant">a.n. NANDANG</p>
                                </div>
                            </label>

                            <label class="bank-option relative flex items-center justify-between gap-4 p-4 rounded-xl cursor-pointer bg-surface-container-highest/50 hover:bg-surface-container-highest transition-all border border-outline-variant/10"
                                data-value="BRI">
                                <input class="sr-only" name="bank_name" type="radio" value="BRI"
                                    {{ old('bank_name') === 'BRI' ? 'checked' : '' }} />
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-lg flex items-center justify-center font-extrabold text-white text-[11px] tracking-tight flex-shrink-0"
                                        style="background:#00529C">BRI</div>
                                    <span class="font-bold text-on-surface-variant font-manrope text-sm truncate">Bank BRI</span>
                                    <span class="check-icon-bank material-symbols-outlined text-primary hidden text-lg flex-shrink-0"
                                        style="font-variation-settings: 'FILL' 1">check_circle</span>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-bold text-on-surface text-sm tracking-wide">4411 0100 4012 507</p>
                                    <p class="text-[11px] text-on-surface-variant">a.n. ASEP SHOLAHUDIN</p>
                                </div>
                            </label>

                            <label class="bank-option relative flex items-center justify-between gap-4 p-4 rounded-xl cursor-pointer bg-surface-container-highest/50 hover:bg-surface-container-highest transition-all border border-outline-variant/10"
                                data-value="BCA">
                                <input class="sr-only" name="bank_name" type="radio" value="BCA"
                                    {{ old('bank_name') === 'BCA' ? 'checked' : '' }} />
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-lg flex items-center justify-center font-extrabold text-white text-[11px] tracking-tight flex-shrink-0"
                                        style="background:#005CAB">BCA</div>
                                    <span class="font-bold text-on-surface-variant font-manrope text-sm truncate">Bank BCA</span>
                                    <span class="check-icon-bank material-symbols-outlined text-primary hidden text-lg flex-shrink-0"
                                        style="font-variation-settings: 'FILL' 1">check_circle</span>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-bold text-on-surface text-sm tracking-wide">377 0291 479</p>
                                    <p class="text-[11px] text-on-surface-variant">a.n. LILI JULIANSYAH</p>
                                </div>
                            </label>
                        </div>
                    </section>

                    {{-- Catatan --}}
                    <section class="bg-white rounded-xl p-8 border border-outline-variant/10">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="material-symbols-outlined text-primary">sticky_note_2</span>
                            <h2 class="text-xl font-bold font-manrope">Catatan Pesanan</h2>
                        </div>
                        <div class="space-y-2">
                            <label for="notes"
                                class="text-xs font-bold uppercase tracking-wider text-on-surface-variant px-1">
                                Catatan <span class="text-on-surface-variant font-normal normal-case">(opsional)</span>
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                placeholder="Contoh: tolong kirim sebelum jam 12 siang, atau instruksi khusus lainnya..."
                               class="w-full bg-gray-100 border-none rounded-lg pl-11 pr-4 py-3 focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 transition-all text-sm">{{ old('notes') }}</textarea>
                        </div>
                        
                    </section>

                </div>

                {{-- Kolom Kanan --}}
                <aside class="space-y-6 lg:sticky absolute lg:top-24">
                    <div
                        class="bg-surface-container-lowest rounded-xl p-8 shadow-[0_20px_40px_rgba(25,28,29,0.04)] border border-outline-variant/10">

                        <h2 class="text-xl font-bold font-manrope mb-6">Ringkasan Pesanan</h2>

                        {{-- Cart Items --}}
                        <div id="orderItemsList" class="space-y-4 mb-6 min-h-[80px]">
                            <div class="flex items-center justify-center py-6 text-on-surface-variant" id="cartEmptyMsg">
                                <span class="material-symbols-outlined text-2xl opacity-30 mr-2">shopping_basket</span>
                                <span class="text-sm">Keranjang kosong</span>
                            </div>
                        </div>

                        {{-- Cost Breakdown --}}
                        <div class="space-y-3 pt-6 border-t border-outline-variant/20">
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant">Subtotal</span>
                                <span class="text-on-surface font-medium" id="summarySubtotal">Rp 0</span>
                            </div>
                            <div class="flex justify-between pt-4 mt-2 border-t border-outline-variant/30">
                                <span class="text-base font-bold text-on-surface font-manrope">Total</span>
                                <span class="text-xl font-extrabold text-primary font-manrope" id="summaryTotal">Rp
                                    0</span>
                            </div>
                        </div>

                        <input type="hidden" name="total_amount" id="totalAmountInput" value="0">

                        {{-- Submit --}}
                        <button type="submit" id="submitBtn"
                            class="w-full mt-8 py-4 px-6 rounded-xl bg-gradient-to-br from-[#00342b] to-[#004d40] text-white font-bold text-base hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed disabled:translate-y-0">
                            <span class="material-symbols-outlined">receipt_long</span>
                            <span>Proses Pesanan</span>
                            <span class="material-symbols-outlined text-sm opacity-70">arrow_forward</span>
                        </button>
                    </div>
                </aside>

            </div>
        </form>

    </div>

    {{-- Background --}}
    <div
        class="fixed top-0 right-0 -z-10 w-1/2 h-full bg-gradient-to-bl from-primary/5 to-transparent pointer-events-none">
    </div>
    <div
        class="fixed bottom-0 left-64 -z-10 w-96 h-96 bg-secondary-container/20 blur-[120px] rounded-full pointer-events-none">
    </div>
@endsection

@push('scripts')
    <script>
        function formatRp(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
            }).format(value);
        }

        function loadCartFromSession() {
            let cart = {};
            try {
                const raw = sessionStorage.getItem('pendingCart');
                if (raw) cart = JSON.parse(raw);
            } catch (e) {
                console.warn('[CHECKOUT] Could not parse cart from sessionStorage');
            }
            return cart;
        }

        function renderOrderSummary(cart) {
            const list = document.getElementById('orderItemsList');
            const emptyMsg = document.getElementById('cartEmptyMsg');
            const subtotalEl = document.getElementById('summarySubtotal');
            const totalEl = document.getElementById('summaryTotal');
            const totalInput = document.getElementById('totalAmountInput');
            const cartInput = document.getElementById('cartItemsInput');
            const submitBtn = document.getElementById('submitBtn');

            const items = Object.entries(cart);

            if (items.length === 0) {
                emptyMsg?.classList.remove('hidden');
                submitBtn?.setAttribute('disabled', 'true');
                return;
            }

            emptyMsg?.classList.add('hidden');

            let total = 0;

            const html = items.map(([id, item]) => {
                const lineTotal = item.price * item.qty;
                total += lineTotal;
                return `
      <div class="flex items-start gap-3">
        ${item.image
          ? `<img src="${item.image}" alt="${item.name}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0 bg-surface-container" />`
          : `<div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center flex-shrink-0">
                           <span class="material-symbols-outlined text-on-surface-variant text-lg">inventory_2</span>
                         </div>`
        }
        <div class="flex-1 min-w-0">
          <p class="text-sm font-bold text-on-surface truncate">${item.name}</p>
          <p class="text-xs text-on-surface-variant">${item.qty} × ${formatRp(item.price)}</p>
        </div>
        <span class="text-sm font-bold text-on-surface flex-shrink-0">${formatRp(lineTotal)}</span>
      </div>
    `;
            }).join('');

            list.innerHTML = html;
            subtotalEl.textContent = formatRp(total);
            totalEl.textContent = formatRp(total);
            totalInput.value = total;
            cartInput.value = JSON.stringify(cart);
        }

        function toggleBankOptions(show) {
            const bankOptions = document.getElementById('bankOptions');
            const bankRadios = document.querySelectorAll('input[name="bank_name"]');
            if (!bankOptions) return;

            if (show) {
                bankOptions.classList.remove('hidden');
                bankRadios.forEach(r => r.required = true);
            } else {
                bankOptions.classList.add('hidden');
                bankRadios.forEach(r => {
                    r.required = false;
                    r.checked = false;
                });
                document.querySelectorAll('.bank-option').forEach(opt => {
                    opt.classList.remove('ring-2', 'ring-primary', 'bg-surface-container-lowest');
                    opt.classList.add('bg-surface-container-highest/50');
                    opt.querySelector('.check-icon-bank')?.classList.add('hidden');
                });
            }
        }

        function setupBankOptions() {
            const options = document.querySelectorAll('.bank-option');

            options.forEach(opt => {
                const radio = opt.querySelector('input[type="radio"]');
                const check = opt.querySelector('.check-icon-bank');

                // Sync initial state
                if (radio.checked) {
                    opt.classList.add('ring-2', 'ring-primary', 'bg-surface-container-lowest');
                    opt.classList.remove('bg-surface-container-highest/50');
                    check?.classList.remove('hidden');
                }

                opt.addEventListener('click', () => {
                    options.forEach(o => {
                        o.classList.remove('ring-2', 'ring-primary', 'bg-surface-container-lowest');
                        o.classList.add('bg-surface-container-highest/50');
                        o.querySelector('.check-icon-bank')?.classList.add('hidden');
                        o.querySelector('input[type="radio"]').checked = false;
                    });

                    radio.checked = true;
                    opt.classList.add('ring-2', 'ring-primary', 'bg-surface-container-lowest');
                    opt.classList.remove('bg-surface-container-highest/50');
                    check?.classList.remove('hidden');
                });
            });
        }

        function setupPaymentCards() {
            const cards = document.querySelectorAll('.payment-card');

            cards.forEach(card => {
                const radio = card.querySelector('input[type="radio"]');
                const check = card.querySelector('.check-icon');

                // Sync initial state
                if (radio.checked) {
                    card.classList.add('ring-2', 'ring-primary', 'bg-surface-container-lowest');
                    card.classList.remove('bg-surface-container-highest/50');
                    check?.classList.remove('hidden');
                }

                card.addEventListener('click', () => {
                    // Reset all
                    cards.forEach(c => {
                        c.classList.remove('ring-2', 'ring-primary', 'bg-surface-container-lowest');
                        c.classList.add('bg-surface-container-highest/50');
                        c.querySelector('.check-icon')?.classList.add('hidden');
                        c.querySelector('input[type="radio"]').checked = false;

                        // Reset text colors
                        c.querySelectorAll('.font-bold').forEach(el => {
                            el.classList.remove('text-on-surface');
                            el.classList.add('text-on-surface-variant');
                        });
                        c.querySelector('.material-symbols-outlined:first-child')?.classList
                            .replace('text-primary', 'text-on-surface-variant');
                    });

                    // Activate clicked
                    radio.checked = true;
                    card.classList.add('ring-2', 'ring-primary', 'bg-surface-container-lowest');
                    card.classList.remove('bg-surface-container-highest/50');
                    check?.classList.remove('hidden');
                    card.querySelectorAll('.font-bold').forEach(el => {
                        el.classList.add('text-on-surface');
                        el.classList.remove('text-on-surface-variant');
                    });

                    // Show/hide bank list based on selected method
                    toggleBankOptions(card.dataset.value === 'transfer');
                });
            });

            // Sync bank list visibility with initially checked method (e.g. after validation error)
            const checkedCard = document.querySelector('.payment-card input:checked')?.closest('.payment-card');
            toggleBankOptions(checkedCard?.dataset.value === 'transfer');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const cart = loadCartFromSession();
            renderOrderSummary(cart);
            setupPaymentCards();
            setupBankOptions();
        });
    </script>
@endpush