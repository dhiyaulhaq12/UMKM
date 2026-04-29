@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h2>
    </div>

{{-- FORM SEARCH --}}
<form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2">
    <div class="relative">
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Cari berdasarkan nama..." 
               class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm w-64 transition">
    </div>
    <button type="submit" class="bg-[#24294b] text-white px-6 py-2 rounded-xl text-sm font-bold hover:bg-blue-800 transition shadow-sm">
        Cari
    </button>
    
    @if(request('search'))
        <a href="{{ route('admin.users.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-200 transition">
            Reset
        </a>
    @endif
</form>
</div>

{{-- TABEL USER --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 font-semibold uppercase text-[11px] tracking-wider">
                <tr>
                    <th class="px-6 py-4">Nama & Bisnis</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Status Akun</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/50 transition">
                    {{-- ... (isi <td> sama seperti sebelumnya) ... --}}
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900">{{ $user->name }}</div>
                        <div class="text-xs text-blue-600 font-medium">{{ $user->business_name }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @if($user->is_active)
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-bold uppercase">Aktif</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-bold uppercase">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $user->is_active ? 'bg-red-50 text-red-600 hover:bg-red-600 hover:text-white' : 'bg-green-50 text-green-600 hover:bg-green-600 hover:text-white' }}">
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan Akun' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">User tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- NAVIGASI PAGINATION --}}
    <div class="p-6 bg-gray-50 border-t border-gray-100">
        {{ $users->links() }}
    </div>
</div>
@endsection