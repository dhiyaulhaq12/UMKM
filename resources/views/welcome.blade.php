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
                Kelola Keuangan <span class="text-[#2b25db]">UMKM</span>
                <span>Anda</span>
            </h1>
            <p class="text-gray-500 text-sm md:text-lg lg:text-xl leading-relaxed max-w-3xl mx-auto font-medium">
                Sistem keuangan yang mudah digunakan untuk membantu bisnis kecil dan menengah mengelola pemasukan, pengeluaran, dan laporan keuangan dengan prediksi keuntungan.
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
                
                <div class="bg-white p-6 md:p-12 rounded-[1.5rem] md:rounded-[2.5rem] border border-gray-100 shadow-sm text-center hover:shadow-xl transition duration-300 group">
                    <div class="bg-[#2b25db] w-14 h-14 md:w-20 md:h-20 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-6 md:mb-8 shadow-xl shadow-blue-100 group-hover:scale-110 transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 md:h-10 md:w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-2 md:mb-4 text-[#1e1e1e]">Manajemen Transaksi</h3>
                    <p class="text-gray-500 text-xs md:text-base leading-relaxed font-medium">Catat dan kelola semua transaksi pemasukan dan pengeluaran dengan mudah</p>
                </div>

                <div class="bg-white p-6 md:p-12 rounded-[1.5rem] md:rounded-[2.5rem] border border-gray-100 shadow-sm text-center hover:shadow-xl transition duration-300 group">
                    <div class="bg-[#22c55e] w-14 h-14 md:w-20 md:h-20 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-6 md:mb-8 shadow-xl shadow-green-100 group-hover:scale-110 transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 md:h-10 md:w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-2 md:mb-4 text-[#1e1e1e]">Laporan Keuangan</h3>
                    <p class="text-gray-500 text-xs md:text-base leading-relaxed font-medium">Dapatkan insight mendalam tentang kesehatan keuangan bisnis Anda</p>
                </div>

                <div class="bg-white p-6 md:p-12 rounded-[1.5rem] md:rounded-[2.5rem] border border-gray-100 shadow-sm text-center hover:shadow-xl transition duration-300 group">
                    <div class="bg-[#8b5cf6] w-14 h-14 md:w-20 md:h-20 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-6 md:mb-8 shadow-xl shadow-purple-100 group-hover:scale-110 transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 md:h-10 md:w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-2 md:mb-4 text-[#1e1e1e]">Prediksi Keuntungan</h3>
                    <p class="text-gray-500 text-xs md:text-base leading-relaxed font-medium">Memprediksi keuntungan berdasarkan data keuangan</p>
                </div>

                <div class="bg-white p-6 md:p-12 rounded-[1.5rem] md:rounded-[2.5rem] border border-gray-100 shadow-sm text-center hover:shadow-xl transition duration-300 group">
                    <div class="bg-[#f87171] w-14 h-14 md:w-20 md:h-20 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-6 md:mb-8 shadow-xl shadow-red-100 group-hover:scale-110 transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 md:h-10 md:w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-2 md:mb-4 text-[#1e1e1e]">Keamanan Data</h3>
                    <p class="text-gray-500 text-xs md:text-base leading-relaxed font-medium">Menjamin keamanan data keuangan Anda</p>
                </div>
            </div>
        </div>
    </section>

</body>
</html>