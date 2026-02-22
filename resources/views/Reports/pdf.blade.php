<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Keuangan</title>
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
    }
    h1 {
        text-align: center;
        margin-bottom: 2px;
    }
    .business {
        text-align: center;
        margin-bottom: 12px;
    }
    .summary div {
        margin-bottom: 4px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 18px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 6px;
    }
    th {
        background: #f3f4f6;
        text-align: left;
    }
    .right {
        text-align: right;
    }
    .total-row {
        font-weight: bold;
        background: #fafafa;
    }
</style>
</head>
<body>

<h1>LAPORAN KEUANGAN</h1>

<div class="business">
    {{ $businessName }}<br>
    Periode {{ $monthCarbon->translatedFormat('F Y') }}
</div>

{{-- ===================== --}}
{{-- SUMMARY (INI TETAP) --}}
{{-- ===================== --}}
<div class="summary">
    <div><strong>Pendapatan:</strong> Rp {{ number_format($totalIncome,0,',','.') }}</div>
    <div><strong>Pengeluaran:</strong> Rp {{ number_format($totalExpense,0,',','.') }}</div>
    <div><strong>Keuntungan:</strong> Rp {{ number_format($profit,0,',','.') }}</div>
    <div><strong>Jumlah Transaksi:</strong> {{ $totalTransactions }}</div>
</div>

{{-- ===================== --}}
{{-- TABEL PENDAPATAN --}}
{{-- ===================== --}}
<h4>Pendapatan per Kategori</h4>
<table>
    <thead>
        <tr>
            <th>Kategori</th>
            <th class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @php $incomeTotal = 0; @endphp
        @foreach($incomeByCategory as $row)
            @php $incomeTotal += $row->total; @endphp
            <tr>
                <td>{{ $row->category }}</td>
                <td class="right">
                    Rp {{ number_format($row->total,0,',','.') }}
                </td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td>Total</td>
            <td class="right">
                Rp {{ number_format($incomeTotal,0,',','.') }}
            </td>
        </tr>
    </tbody>
</table>

{{-- ===================== --}}
{{-- TABEL PENGELUARAN --}}
{{-- ===================== --}}
<h4>Pengeluaran per Kategori</h4>
<table>
    <thead>
        <tr>
            <th>Kategori</th>
            <th class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @php $expenseTotal = 0; @endphp
        @foreach($expenseByCategory as $row)
            @php $expenseTotal += $row->total; @endphp
            <tr>
                <td>{{ $row->category }}</td>
                <td class="right">
                    Rp {{ number_format($row->total,0,',','.') }}
                </td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td>Total</td>
            <td class="right">
                Rp {{ number_format($expenseTotal,0,',','.') }}
            </td>
        </tr>
    </tbody>
</table>

</body>
</html>
