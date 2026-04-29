<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KeuanganUMKM - Kelola Keuangan UMKM Anda</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-[#1e1e1e]">

    <nav class="bg-white px-4 md:px-6 h-16 flex justify-between items-center shadow-sm sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <img src="{{ asset('icons/umkm.png') }}" alt="Logo" class="w-100 h-100 md:w-10 md:h-10 object-contain">
            <span class="text-base md:text-lg font-bold tracking-tight">KeuanganUMKM</span>
        </div>
        <div class="flex gap-2 md:gap-4 items-center">
            <a href="{{ route('login') }}" class="text-xs md:text-sm font-semibold hover:text-blue-700 transition">Masuk</a>
            <a href="{{ route('register') }}" class="bg-[#2b25db] text-white px-3 py-1.5 md:px-5 md:py-2 rounded-lg text-xs font-semibold hover:bg-blue-800 transition shadow-md">
                Daftar
            </a>
        </div>
    </nav>

    <section class="bg-[#E9EEFF] pt-12 pb-16 md:pt-24 md:pb-24 px-4 md:px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-3xl md:text-5xl lg:text-7xl font-extrabold leading-tight mb-6 md:mb-8">
                {{-- Menghubungkan Hero Title --}}
                @php
                    $title = $settings['hero_title'] ?? 'Kelola Keuangan UMKM Anda';
                    // Logika untuk memberikan warna biru pada kata 'UMKM' jika ada
                    $formattedTitle = str_replace('UMKM', '<span class="text-[#2b25db]">UMKM</span>', $title);
                @endphp
                {!! $formattedTitle !!}
            </h1>
            <p class="text-gray-500 text-sm md:text-lg lg:text-xl leading-relaxed max-w-3xl mx-auto font-medium">
                {{-- Menghubungkan Hero Description --}}
                {{ $settings['hero_desc'] ?? 'Sistem keuangan yang mudah digunakan untuk membantu bisnis kecil dan menengah mengelola pemasukan, pengeluaran, dan laporan keuangan dengan prediksi keuntungan.' }}
            </p>
        </div>
    </section>

    <section class="bg-white py-12 md:py-20 px-4 md:px-6">
        <div class="max-w-6xl mx-auto">
            
            <div class="text-center mb-10 md:mb-16">
                <h2 class="text-2xl md:text-4xl font-bold mb-3 md:mb-4 text-[#1e1e1e]">Fitur Unggulan Kami</h2>
                <p class="text-gray-500 text-sm md:text-lg font-medium">Semua yang Anda butuhkan untuk mengelola keuangan UMKM dalam satu platform</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                
                {{-- Loop Data Fitur dari Database --}}
                @forelse($features as $feature)
                <div class="bg-white p-6 md:p-12 rounded-[1.5rem] md:rounded-[2.5rem] border border-gray-100 shadow-sm text-center hover:shadow-xl transition duration-300 group">
                    <div class="w-14 h-14 md:w-20 md:h-20 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-6 md:mb-8 shadow-xl transition duration-300 group-hover:scale-110">
                        {{-- Menampilkan Ikon dari Cloudinary --}}
                        <img src="{{ $feature->icon }}" alt="{{ $feature->title }}" class="h-10 w-10 md:h-14 md:w-14 object-contain">
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-2 md:mb-4 text-[#1e1e1e]">{{ $feature->title }}</h3>
                    <p class="text-gray-500 text-xs md:text-base leading-relaxed font-medium">{{ $feature->description }}</p>
                </div>
                @empty
                    {{-- Tampilan Default jika database kosong (Opsional) --}}
                    <p class="text-center col-span-2 text-gray-400">Belum ada fitur yang ditambahkan.</p>
                @endforelse

            </div>
        </div>
    </section>

</body>
</html>