@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- HAPUS FORM FILTER DI SINI --}}
    <div class="flex justify-between items-center">
        <h1 class="text-lg font-semibold text-gray-800">Dashboard</h1>
    </div>

    {{-- Filter sekarang otomatis muncul dari sini --}}
    @include('layouts.summary', [
        'totalPendapatan' => $totalIncome, 
        'totalPengeluaran' => $totalExpense,
        'keuntungan' => $profit, 
        'totalTerjual' => $totalTransactions
    ])

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Trend Chart (Tahunan) --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-md font-bold text-gray-800">Trend Keuangan {{ $monthCarbon->format('Y') }}</h3>
                <div class="flex gap-4 text-[10px] font-bold uppercase">
                    <span class="flex items-center gap-1"><div class="w-2 h-2 bg-indigo-500 rounded-full"></div> Pendapatan</span>
                    <span class="flex items-center gap-1"><div class="w-2 h-2 bg-cyan-400 rounded-full"></div> Keuntungan</span>
                    <span class="flex items-center gap-1"><div class="w-2 h-2 bg-rose-500 rounded-full"></div> Pengeluaran</span>
                </div>
            </div>
            <div class="h-[320px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Distribution Chart (Kategori) --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
            <h3 class="text-md font-bold text-gray-800 mb-1">Distribusi Keuangan</h3>
            <p class="text-xs text-gray-400 mb-6">{{ $monthCarbon->translatedFormat('F Y') }}</p>
            <div class="h-[200px] flex items-center justify-center mb-8">
                <canvas id="distributionChart"></canvas>
            </div>
            <div class="space-y-3 flex-1 overflow-y-auto max-h-[150px] pr-2 custom-scrollbar">
                @php $colors = ['#4F46E5', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#F97316', '#6366F1', '#D946EF']; @endphp
                @foreach($distributionData as $index => $item)
                <div class="flex items-center justify-between text-xs font-semibold">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $colors[$index] ?? '#cbd5e1' }}"></div>
                        <span class="text-gray-500">{{ $item->category }}</span>
                    </div>
                    <span class="text-gray-800">{{ $item->percentage }}%</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Metrik Kinerja --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-md font-bold text-gray-800 mb-4">Metrik Kinerja Bisnis</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 border rounded-xl bg-gray-50/50">
                <h4 class="text-xl font-bold text-green-600">{{ $profitMargin }}%</h4>
                <p class="text-[10px] text-gray-400 uppercase font-bold mt-1">Margin Keuntungan</p>
            </div>
            <div class="text-center p-4 border rounded-xl bg-gray-50/50">
                <h4 class="text-xl font-bold text-indigo-600">{{ number_format($ratio, 2) }}</h4>
                <p class="text-[10px] text-gray-400 uppercase font-bold mt-1">Rasio Pendapatan/Biaya</p>
            </div>
            <div class="text-center p-4 border rounded-xl bg-gray-50/50">
                <h4 class="text-xl font-bold uppercase {{ $health == 'Untung' ? 'text-green-600' : 'text-red-500' }}">
                    {{ $health }}
                </h4>
                <p class="text-[10px] text-gray-400 uppercase font-bold mt-1">Status Bisnis</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const palette = ['#4F46E5', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#F97316', '#6366F1', '#D946EF'];

    // Trend Chart Animation
    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
            datasets: [
                { label: 'Pendapatan', data: @json($chartDataIncome), backgroundColor: '#6366f1', borderRadius: 5 },
                { label: 'Keuntungan', data: @json($chartDataProfit), backgroundColor: '#22d3ee', borderRadius: 5 },
                { label: 'Pengeluaran', data: @json($chartDataExpense), backgroundColor: '#f43f5e', borderRadius: 5 }
            ]
        },
        options: {
            animation: { duration: 1500, easing: 'easeOutQuart' },
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { grid: { borderDash: [5, 5], drawBorder: false } }, x: { grid: { display: false } } }
        }
    });

    // Distribution Chart Animation
    new Chart(document.getElementById('distributionChart'), {
        type: 'doughnut',
        data: {
            labels: @json($distributionData->pluck('category')),
            datasets: [{
                data: @json($distributionData->pluck('total')),
                backgroundColor: palette,
                borderWidth: 0
            }]
        },
        options: {
            animation: { animateRotate: true, animateScale: true, duration: 1500 },
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush