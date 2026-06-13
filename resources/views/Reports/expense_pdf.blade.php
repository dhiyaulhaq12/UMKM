<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Pengeluaran - {{ $businessName }}</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .header-table td { border: none; padding: 0; vertical-align: middle; }
    .logo-container { width: 70px; }
    .logo-img { width: 65px; height: 65px; border-radius: 50%; object-fit: cover; }
    .title-container { text-align: left; padding-left: 15px; }
    .main-title { font-size: 18px; font-weight: bold; color: #1e3a8a; margin: 0; letter-spacing: 0.5px; }
    .business-name { font-size: 13px; font-weight: bold; color: #4b5563; margin-top: 3px; }
    .periode-text { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .summary-box { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .summary-box td { border: 1px solid #e5e7eb; padding: 10px; width: 25%; background-color: #f9fafb; border-radius: 6px; }
    .summary-label { font-size: 9px; color: #6b7280; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
    .summary-value { font-size: 13px; font-weight: bold; }
    h4 { font-size: 12px; color: #111827; margin: 15px 0 6px 0; border-left: 3px solid #dc2626; padding-left: 6px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    th, td { border: 1px solid #e5e7eb; padding: 6px 8px; }
    th { background: #f3f4f6; color: #374151; font-weight: bold; font-size: 10px; text-transform: uppercase; text-align: left; }
    .right { text-align: right; }
    .center { text-align: center; }
    .total-row { font-weight: bold; background: #f9fafb; }
</style>
</head>
<body>

    <table class="header-table">
        <tr>
            @if($logoBase64)
                <td class="logo-container">
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                </td>
            @endif
            <td class="title-container">
                <div class="main-title" style="color: #dc2626;">LAPORAN ALOKASI PENGELUARAN BIAYA</div>
                <div class="business-name">{{ $businessName }}</div>
                <div class="periode-text">Periode: {{ $monthCarbon->translatedFormat('F Y') }}</div>
            </td>
        </tr>
    </table>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin-bottom: 15px;">

    <table class="summary-box">
        <tr>
            <td>
                <div class="summary-label">Pendapatan</div>
                <div class="summary-value" style="color: #16a34a;">Rp {{ number_format($totalIncome,0,',','.') }}</div>
            </td>
            <td>
                <div class="summary-label">Pengeluaran</div>
                <div class="summary-value" style="color: #dc2626;">Rp {{ number_format($totalExpense,0,',','.') }}</div>
            </td>
            <td>
                <div class="summary-label">Keuntungan</div>
                <div class="summary-value" style="color: #2563eb;">Rp {{ number_format($profit,0,',','.') }}</div>
            </td>
            <td>
                <div class="summary-label">Jumlah Transaksi</div>
                <div class="summary-value" style="color: #4b5563;">{{ $totalTransactions }}</div>
            </td>
        </tr>
    </table>

    <h4>Rekapitulasi Alokasi Biaya per Kategori</h4>
    <table>
        <thead>
            <tr>
                <th>Kategori Anggaran</th>
                <th class="center" style="width: 35%;">Frekuensi Alokasi</th>
                <th class="right" style="width: 35%;">Total Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            @php $expenseTotal = 0; $expenseCount = 0; @endphp
            @foreach($expenseByCategory as $row)
                @php $expenseTotal += $row->total; $expenseCount += $row->count; @endphp
                <tr>
                    <td style="font-weight: 500;">{{ $row->category }}</td>
                    <td class="center">{{ $row->count }} Transaksi</td>
                    <td class="right" style="font-weight: bold;">Rp {{ number_format($row->total,0,',','.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Pengeluaran</td>
                <td class="center">{{ $expenseCount }} Kali Transaksi</td>
                <td class="right" style="color: #dc2626;">Rp {{ number_format($expenseTotal,0,',','.') }}</td>
            </tr>
        </tbody>
    </table>

    <h4>Rincian Log Buku Pengeluaran Mandiri</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 22%; text-align: center;">Tanggal & Waktu</th>
                <th style="width: 25%;">Kebutuhan Alokasi</th>
                <th style="width: 38%;">Keterangan Catatan Pengeluaran</th>
                <th style="width: 15%; text-align: right;">Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenseDetails as $detail)
            <tr>
                <td class="center" style="font-size: 10px;">
                    {{ \Carbon\Carbon::parse($detail->transaction_date)->format('d/m/Y') }}
                    <span style="color: #888; font-size: 9px; display: block; margin-top: 2px;">
                        ({{ $detail->transaction_time ? \Carbon\Carbon::parse($detail->transaction_time)->format('H:i') : '00:00' }} WIB)
                    </span>
                </td>
                <td style="font-weight: 500;">{{ $detail->category }}</td>
                <td>{{ $detail->note ?? '-' }}</td>
                <td class="right" style="font-weight: bold; color: #dc2626;">{{ number_format($detail->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="center style-amount" style="color: #9ca3af; font-style: italic;">Belum ada rekaman log rincian pengeluaran harian.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>