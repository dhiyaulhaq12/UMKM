<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite('resources/css/app.css')
</head>


<body class="bg-[#f6f7fb]">

<div class="min-h-screen flex">

    {{-- OVERLAY (MOBILE) --}}
    <div id="sidebarOverlay"
         class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>

    {{-- SIDEBAR --}}
    <aside id="sidebar"
       class="fixed md:static inset-y-0 left-0
              z-40 md:z-10
              transform -translate-x-full md:translate-x-0
              transition-all duration-300
              bg-gradient-to-b from-blue-800 to-blue-600 text-white
              w-64 px-4 pt-2 flex flex-col">


    {{-- HEADER --}}
    <div class="relative flex justify-center mb-2">

        {{-- LOGO --}}
        <img src="{{ asset('icons/umkm.png') }}"
              class="sidebar-logo w-[120px] transition-all duration-300">

        {{-- CLOSE (MOBILE) --}}
        <button id="closeSidebar"
                class="md:hidden absolute right-2 top-1 text-white text-xl">
            ✕
        </button>

        {{-- MINIMIZE (DESKTOP) --}}
        <button id="toggleDesktopSidebar"
                type="button"
                class="hidden md:flex
                    absolute -right-3 top-6
                    bg-blue-700 text-white
                    w-7 h-7 rounded-full
                    items-center justify-center
                    shadow-lg hover:bg-blue-600
                    z-50 pointer-events-auto">
            ‹
        </button>

    </div>

    {{-- MENU DIUBAH MENJADI MODEREN ACCORDION --}}
    @php
        $active = 'bg-white text-blue-700 shadow font-semibold';
        $inactive = 'text-white/80 hover:text-white hover:bg-white/10';
        
        $subActive = 'bg-white/10 text-white font-medium';
        $subInactive = 'text-white/60 hover:text-white hover:bg-white/5';

        // Cek apakah user sedang membuka halaman transaksi atau custom kategori
        $isTrxGroup = request()->is('transactions*') || request()->routeIs('custom-categories.*');
    @endphp

    <nav class="space-y-1 mt-4 text-sm">

        {{-- DASHBOARD --}}
        <a href="/dashboard" class="menu-item block px-3 py-2 rounded-lg {{ request()->is('dashboard*') ? $active : $inactive }}">
            <span class="menu-text">Dashboard</span>
        </a>

        {{-- PARENT MENU: TRANSAKSI (DENGAN TOMBOL ARAH BAWAH) --}}
        <div class="relative">
            <button id="btnDropdownTrx" type="button" 
                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all {{ $isTrxGroup ? 'bg-white/10 text-white font-semibold' : $inactive }}">
                <span class="menu-text">Transaksi</span>
                
                {{-- Icon Panah Bawah Dinamis (Bisa Berputar dengan Animasi) --}}
                <svg id="arrowIcon" xmlns="http://www.w3.org/2000/svg" 
                     class="w-4 h-4 transition-transform duration-200 menu-text {{ $isTrxGroup ? 'rotate-180' : '' }}" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- WRAPPER SUB-MENU (Bisa Sembunyi/Muncul Semut) --}}
            <div id="subMenuTrxWrapper" class="{{ $isTrxGroup ? '' : 'hidden' }} mt-1 space-y-1 pl-4 transition-all duration-300">
                
                {{-- Menu 1: Daftar Riwayat --}}
                <a href="{{ route('transactions.index') }}" 
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('transactions.index') ? 'bg-blue-600 text-white' : 'text-gray hover:bg-gray-100' }}">
                    <span>Daftar Transaksi</span>
                </a>

                {{-- Menu 2: Input Baru --}}
                <a href="{{ route('transactions.create') }}" 
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('transactions.create') ? 'bg-blue-600 text-white' : 'text-gray hover:bg-gray-100' }}">
                    <span>Tambah Transaksi</span>
                </a>

                {{-- Sub-Menu 2: Kelola Menu Produk --}}
                <a href="{{ route('custom-categories.index') }}" class="block px-3 py-1.5 rounded-md text-xs transition {{ request()->routeIs('custom-categories.*') ? $active : $subInactive }}">
                    <span>Kelola Sumber Pendapatan</span>
                </a>
            </div>
        </div>

        {{-- ASET --}}
        <a href="/assets" class="menu-item block px-3 py-2 rounded-lg {{ request()->is('asset*') ? $active : $inactive }}">
            <span class="menu-text">Aset</span>
        </a>

        {{-- LAPORAN --}}
        {{-- PARENT MENU: LAPORAN (DENGAN DROPDOWN) --}}
        @php
            // Cek apakah user sedang membuka rumpun menu laporan
            $isReportGroup = request()->is('laporan*');
        @endphp
        <div class="relative">
            <button id="btnDropdownReport" type="button" 
                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all {{ $isReportGroup ? 'bg-white/10 text-white font-semibold' : $inactive }}">
                <div class="flex items-center gap-3">
                    <span class="menu-text">Laporan</span>
                </div>
                {{-- Panah Dropdown --}}
                <svg id="arrowIconReport" xmlns="http://www.w3.org/2000/svg" 
                    class="w-4 h-4 transition-transform duration-200 menu-text {{ $isReportGroup ? 'rotate-180' : '' }}" 
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- WRAPPER SUB-MENU LAPORAN --}}
            <div id="subMenuReportWrapper" class="{{ $isReportGroup ? '' : 'hidden' }} mt-1 space-y-1 pl-4 transition-all duration-300">
                
                {{-- Sub-Menu 1: Laporan Pendapatan --}}
                <a href="/laporan/income" class="block px-3 py-1.5 rounded-md text-xs transition {{ request()->is('laporan/pendapatan*') ? $active : $subInactive }}">
                    <span>Laporan Pendapatan</span>
                </a>

                {{-- Sub-Menu 2: Laporan Pengeluaran --}}
                <a href="/laporan/expense" class="block px-3 py-1.5 rounded-md text-xs transition {{ request()->is('laporan/pengeluaran*') ? $active : $subInactive }}">
                    <span>Laporan Pengeluaran</span>
                </a>
            </div>
        </div>

        {{-- PREDIKSI --}}
        <a href="/prediksi" class="menu-item block px-3 py-2 rounded-lg {{ request()->is('prediksi*') ? $active : $inactive }}">
            <span class="menu-text">Prediksi</span>
        </a>

        {{-- AKUN --}}
        <a href="/akun" class="menu-item block px-3 py-2 rounded-lg {{ request()->is('akun*') ? $active : $inactive }}">
            <span class="menu-text">Akun</span>
        </a>

    </nav>
</aside>


    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- TOP BAR --}}
        <header class="bg-white shadow-sm px-4 md:px-6 py-2 flex items-center justify-between z-20">
            <div class="flex items-center">
                <button id="openSidebar" class="md:hidden text-gray-700 text-xl mr-3">
                    ☰
                </button>
                <h1 class="text-sm font-semibold text-gray-800 hidden md:block">
                </h1>
            </div>

            <div class="relative" id="profileDropdownContainer">
                <button id="profileDropdownBtn" class="flex items-center focus:outline-none group">
                    <img 
                        src="{{ auth()->user()->photo ? auth()->user()->photo : asset('icons/account.png') }}" 
                        class="w-9 h-9 rounded-full border border-gray-200 object-cover group-hover:brightness-90 transition shadow-sm"
                        alt="Profile"
                    >
                </button>

                <div id="logoutDropdown" 
                     class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-lg shadow-lg z-50 overflow-hidden">
                    <div class="p-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md transition font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 md:px-6 py-4">
            @yield('content')
        </main>
    </div>

{{-- ========================= --}}
{{-- SCRIPT INTERAKTIF ACCORDION --}}
{{-- ========================= --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btnDropdownTrx = document.getElementById('btnDropdownTrx');
    const subMenuTrxWrapper = document.getElementById('subMenuTrxWrapper');
    const arrowIcon = document.getElementById('arrowIcon');

    // Toggle buka-tutup sub-menu saat tombol Transaksi di-klik
    btnDropdownTrx?.addEventListener('click', () => {
        const isHidden = subMenuTrxWrapper.classList.contains('hidden');
        
        if (isHidden) {
            subMenuTrxWrapper.classList.remove('hidden');
            arrowIcon.classList.add('rotate-180');
        } else {
            subMenuTrxWrapper.classList.add('hidden');
            arrowIcon.classList.remove('rotate-180');
        }
    });

    // Letakkan ini di dalam DOMContentLoaded script sidebar-mu bersama dropdown transaksi
const btnDropdownReport = document.getElementById('btnDropdownReport');
const subMenuReportWrapper = document.getElementById('subMenuReportWrapper');
const arrowIconReport = document.getElementById('arrowIconReport');

btnDropdownReport?.addEventListener('click', () => {
    const isHidden = subMenuReportWrapper.classList.contains('hidden');
    if (isHidden) {
        subMenuReportWrapper.classList.remove('hidden');
        arrowIconReport.classList.add('rotate-180');
    } else {
        subMenuReportWrapper.classList.add('hidden');
        arrowIconReport.classList.remove('rotate-180');
    }
});

// Pastikan di dalam fungsi collapse() desktop-mu juga menyembunyikan sub-menu laporan:
// if (subMenuReportWrapper) subMenuReportWrapper.classList.add('hidden');
});
</script>

{{-- SCRIPT RESPONSIVE & COLLAPSE LAINNYA (TETAP UTUH) --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const openBtn = document.getElementById('openSidebar');
    const closeBtn = document.getElementById('closeSidebar');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    openBtn?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const toggleDesktop = document.getElementById('toggleDesktopSidebar');
    const menuTexts = document.querySelectorAll('.menu-text');
    const logo = document.querySelector('.sidebar-logo');
    const subMenuWrapper = document.getElementById('subMenuTrxWrapper');

    if (!sidebar || !toggleDesktop) return;

    if (localStorage.getItem('sidebar-collapsed') === 'true') {
        collapse();
    }

    toggleDesktop.addEventListener('click', (e) => {
        e.stopPropagation();
        sidebar.classList.contains('w-20') ? expand() : collapse();
    });

    function collapse() {
        sidebar.classList.remove('w-64');
        sidebar.classList.add('w-20');
        menuTexts.forEach(el => el.classList.add('hidden'));
        logo.classList.add('hidden');
        if (subMenuWrapper) subMenuWrapper.classList.add('hidden'); // Sembunyikan sub-menu jika menu mengecil (w-20)
        toggleDesktop.innerHTML = '›';
        localStorage.setItem('sidebar-collapsed', 'true');
    }

    function expand() {
        sidebar.classList.remove('w-20');
        sidebar.classList.add('w-64');
        menuTexts.forEach(el => el.classList.remove('hidden'));
        logo.classList.remove('hidden');
        
        // Kembalikan sub-menu jika memang link grup aktif
        const isTrxGroup = {{ request()->is('transactions*') || request()->routeIs('custom-categories.*') ? 'true' : 'false' }};
        if (isTrxGroup && subMenuWrapper) subMenuWrapper.classList.remove('hidden');
        
        toggleDesktop.innerHTML = '‹';
        localStorage.setItem('sidebar-collapsed', 'false');
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('profileDropdownBtn');
    const dropdown = document.getElementById('logoutDropdown');

    btn?.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
});
</script>
@stack('scripts')
</body>
</html>