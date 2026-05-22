<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-badge">
                <img src="/assets/pictures/koperasi.png" alt="Logo">
            </div>
            <div class="logo-text">
                <h2 class="logo-title">KOPERASI</h2>
                <p class="logo-subtitle">Bismillah Indonesia Sejahtera</p>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}"
            class="nav-item {{ Route::currentRouteName() === 'dashboard' ? 'active' : '' }}">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="{{ route('storage') }}" class="nav-item {{ Route::currentRouteName() === 'storage' ? 'active' : '' }}">
            <span class="material-symbols-outlined"
                style="font-variation-settings: &quot;FILL&quot; 1">inventory_2</span>
            <span class="nav-label">Gudang</span>
        </a>
        <a href="{{ route('order') }}" class="nav-item {{ Route::currentRouteName() === 'order' ? 'active' : '' }}">
            <span class="material-symbols-outlined">shopping_bag</span>
            <span class="nav-label">Pesanan</span>
        </a>
        <a href="{{ route('prediction') }}"
            class="nav-item {{ Route::currentRouteName() === 'prediction' ? 'active' : '' }}">
            <span class="material-symbols-outlined text-outline-variant">insights</span>
            <span class="nav-label">Prediksi</span>
        </a>
        {{-- <a href="{{ route('shop') }}" class="nav-item {{ Route::currentRouteName() === 'shop' ? 'active' : '' }}">
        <span class="material-symbols-outlined">storefront</span>
        <span class="nav-label">Shop</span>
        </a> --}}
    </nav>
</aside>

<style>
    .logo-badge {
        width: 50px;
        height: 50px;
        background: green-700;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2px;
    }

    .logo-badge img {
        width: 90%;
        height: 90%;
        object-fit: contain;
    }
</style>
