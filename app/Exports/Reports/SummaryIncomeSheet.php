<?php

namespace App\Exports\Reports;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SummaryIncomeSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year  = $year;
        $this->month = $month;
    }

    public function array(): array
    {
        $businessName = Auth::user()->business_name ?? 'Nama Usaha';
        $query = Transaction::where('user_id', Auth::id())
            ->whereYear('transaction_date', $this->year)
            ->whereMonth('transaction_date', $this->month);

        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $query)->where('type', 'expense')->sum('amount');
        $profit = $totalIncome - $totalExpense;
        
        $incomeCount = (clone $query)->where('type', 'income')->count();
        $averageIncome = $incomeCount > 0 ? ($totalIncome / $incomeCount) : 0;

        // Hitung Net Profit Margin (%)
        $profitMargin = $totalIncome > 0 ? round(($profit / $totalIncome) * 100, 1) : 0;

        // Cari Kategori/Produk Terlaris (Top Product)
        $topProductRow = (clone $query)->where('type', 'income')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();
        $topProduct = $topProductRow ? $topProductRow->category . ' (Rp ' . number_format($topProductRow->total, 0, ',', '.') . ')' : '-';

        return [
            ['ANALISIS KINERJA PENDAPATAN & OMSET'],
            ['Nama Usaha', $businessName],
            ['Periode', Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y')],
            ['Total Omset Pendapatan', $totalIncome],
            ['Total Pengeluaran', $totalExpense],
            ['Keuntungan Bersih (Profit)', $profit],
            ['Net Profit Margin (%)', $profitMargin . '%'],
            ['Volume Transaksi', $incomeCount . ' Transaksi'], // 🟢 Perbaikan spasi teks
            ['Rata-rata Pendapatan/Transaksi', $averageIncome],
            ['Item Penyumbang Terbesar', $topProduct],
        ];
    }

    public function title(): string
    {
        return 'Ringkasan Kinerja';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '16A34A']], 
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // 🟢 PERBAIKAN UTAMA: Paksa isi kolom A dan B dari baris 2-10 menjadi Rata Kiri (Left) agar rapi sejajar
        $sheet->getStyle('A2:B10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // Format angka Rupiah untuk baris finansial di Kolom B
        $sheet->getStyle('B4:B6')->getNumberFormat()->setFormatCode('"Rp" #,##0');
        $sheet->getStyle('B9')->getNumberFormat()->setFormatCode('"Rp" #,##0');
        
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('readonly');
    }
}