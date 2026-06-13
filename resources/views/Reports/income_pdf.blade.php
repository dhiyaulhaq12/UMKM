<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Pendapatan - {{ $businessName }}</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .header-table td { border: none; padding: 0; vertical-align: middle; }
    .logo-container { width: 70px; }
    .logo-img { width: 65px; height: 65px; border-radius: 50%; object-fit: cover; }
    .title-container { text-align: left; padding-left: 15px; }
    .main-title { font-size: 18px; font-weight: bold; color: #16a34a; margin: 0; letter-spacing: 0.5px; }
    .business-name { font-size: 13px; font-weight: bold; color: #4b5563; margin-top: 3px; }
    .periode-text { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .summary-box { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .summary-box td { border: 1px solid #e5e7eb; padding: 10px; width: 25%; background-color: #f9fafb; }
    .summary-label { font-size: 9px; color: #6b7280; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
    .summary-value { font-size: 13px; font-weight: bold; }
    h4 { font-size: 12px; color: #111827; margin: 15px 0 6px 0; border-left: 3px solid #16a34a; padding-left: 6px; }
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
                <div class="main-title">LAPORAN REKAPITULASI PENDAPATAN</div>
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
                <div class="summary-value style-amount" style="color: #16a34a;">Rp {{ number_format($totalIncome,0,',','.') }}</div>
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
                <div class="summary-value" style="color: #4b5563;">{{ $totalTransactions }} Transaksi</div>
            </td>
        </tr>
    </table>

    <h4>Rekapitulasi Kuantitas Produk Terjual</h4>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="center" style="width: 20%;">Frekuensi Transaksi</th>
                <th class="center" style="width: 20%;">Volume Terjual</th>
                <th class="right" style="width: 25%;">Total Omset</th>
            </tr>
        </thead>
        <tbody>
            @php $incomeTotal = 0; $qtyTotal = 0; @endphp
            @foreach($incomeByCategory as $row)
                @php $incomeTotal += $row->total; $qtyTotal += $row->total_qty; @endphp
                <tr>
                    <td style="font-weight: 500;">{{ $row->category }}</td>
                    <td class="center">{{ $row->count }} Kali</td>
                    <td class="center font-semibold" style="color: #4b5563;">{{ $row->total_qty }} {{ $row->unit_name ?? 'pcs' }}</td>
                    <td class="right">Rp {{ number_format($row->total,0,',','.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Keseluruhan</td>
                <td class="center">-</td>
                <td class="center" style="color: #2563eb;">{{ $qtyTotal }} Item</td>
                <td class="right" style="color: #16a34a;">Rp {{ number_format($incomeTotal,0,',','.') }}</td>
            </tr>
        </tbody>
    </table>

    <h4>Rincian Buku Jurnal Pendapatan</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 22%; text-align: center;">Tanggal & Waktu</th>
                <th style="width: 25%;">Item</th>
                <th style="width: 15%; text-align: center;">Kuantitas</th>
                <th style="width: 23%;">Catatan</th>
                <th style="width: 15%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomeDetails as $detail)
            <tr>
                <td class="center" style="font-size: 10px;">
                    {{ \Carbon\Carbon::parse($detail->transaction_date)->format('d/m/Y') }}
                    <span style="color: #888; font-size: 9px; display: block; margin-top: 2px;">
                        ({{ $detail->transaction_time ? \Carbon\Carbon::parse($detail->transaction_time)->format('H:i') : '00:00' }} WIB)
                    </span>
                </td>
                <td style="font-weight: 500;">{{ $detail->category }}</td>
                <td class="center">{{ $detail->quantity ?? 1 }} {{ $detail->unit ?? 'pcs' }}</td>
                <td>{{ $detail->note ?? '-' }}</td>
                <td class="right" style="font-weight: bold; color: #16a34a;">{{ number_format($detail->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="center style-amount" style="color: #9ca3af; font-style: italic;">Belum ada rekaman log rincian pendapatan harian.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>