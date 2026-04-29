<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title', 'Dashboard')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-[#f6f7fb]">

<div class="min-h-screen flex">
    {{-- SIDEBAR ADMIN --}}
    <aside class="w-64 bg-[#24294b] text-white flex flex-col shadow-xl">
        {{-- LOGO AREA --}}
        <div class="p-8 text-center">
            <div class="bg-white/10 p-4 rounded-xl mb-3 inline-block">
                {{-- Logo Chart sesuai gambar --}}
                <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
            </div>
            <h1 class="text-xl font-bold tracking-[0.2em]">ADMIN</h1>
        </div>

        {{-- MENU --}}
        <nav class="flex-1 px-4 space-y-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition {{ request()->is('admin/dashboard*') ? 'bg-white text-blue-900 font-bold shadow-lg' : 'hover:bg-white/10 text-white/70' }}">
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.landing.edit') }}" 
            class="flex items-center px-4 py-3 rounded-xl transition {{ request()->is('admin/landing-page*') ? 'bg-white text-blue-900 font-bold shadow-lg' : 'hover:bg-white/10 text-white/70' }}">
                <span>Landing Page</span>
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition {{ request()->is('admin/users*') ? 'bg-white text-blue-900 font-bold shadow-lg' : 'hover:bg-white/10 text-white/70' }}">
                <span>Users</span>
            </a>

            <a href="{{ route('admin.account.index') }}" 
                class="flex items-center px-4 py-3 rounded-xl transition {{ request()->is('admin/akun*') ? 'bg-white text-blue-900 font-bold shadow-lg' : 'hover:bg-white/10 text-white/70' }}">
                <span>Akun</span>
            </a>
        </nav>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col">
{{-- TOPBAR --}}
<header class="bg-white h-16 flex items-center justify-end px-8 shadow-sm">
    <div x-data="{ open: false }" class="relative">
        {{-- Klik Foto/Nama untuk buka dropdown --}}
        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-3 focus:outline-none group">
            <span class="text-sm font-semibold text-gray-600 group-hover:text-blue-700 transition">
                {{ auth()->user()->name }}
            </span>
            <img src="{{ auth()->user()->photo ? auth()->user()->photo : asset('icons/account.png') }}" 
                 class="w-10 h-10 rounded-full border border-gray-200 shadow-sm object-cover group-hover:border-blue-300 transition">
        </button>

        {{-- Dropdown Menu (Hanya Logout) --}}
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 mt-2 w-40 bg-white border border-gray-100 rounded-xl shadow-xl py-2 z-50">
            
             <form action="{{ route('admin.logout') }}" method="POST">                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

        {{-- CONTENT AREA --}}
        <main class="p-8">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>