<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - UMKM</title>
    {{-- Hubungkan ke CSS Tailwind Anda --}}
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
            {{-- Bagian Atas --}}
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-[#24294b]">Admin</h2>
                <p class="text-gray-500 mt-2">Manajemen Sistem Prediksi UMKM</p>
            </div>

            {{-- Form Login --}}
            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Admin</label>
                    <input type="email" name="email" 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition" 
                        placeholder="admin@example.com"
                        required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition" 
                        placeholder="••••••••"
                        required>
                </div>

                {{-- Pesan Error --}}
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100">
                        {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit" class="w-full bg-[#24294b] text-white py-3 rounded-xl font-bold hover:bg-blue-800 transform active:scale-95 transition duration-200 shadow-lg shadow-blue-200">
                    Masuk
                </button>
            </form>
        </div>
    </div>

</body>
</html>