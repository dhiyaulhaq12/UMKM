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

class SummaryExpenseSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
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
        
        $expenseCount = (clone $query)->where('type', 'expense')->count();

        // Hitung Rasio Beban Operasional / Cost-to-Income Ratio (%)
        $costRatio = $totalIncome > 0 ? round(($totalExpense / $totalIncome) * 100, 1) : 0;

        // Cari Pos Pengeluaran Paling Boros (Top Expense)
        $topExpenseRow = (clone $query)->where('type', 'expense')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();
        
        // Hapus lambang Rp dari string teks informasi kategori tertinggi agar rapi
        $topExpense = $topExpenseRow ? $topExpenseRow->category . ' (' . number_format($topExpenseRow->total, 0, ',', '.') . ')' : '-';

        return [
            ['ANALISIS EFISIENSI BIAYA & OPERASIONAL'],
            ['Nama Usaha', $businessName],
            ['Periode', Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y')],
            ['Total Biaya Pengeluaran', $totalExpense],
            ['Total Omset', $totalIncome],
            ['Sisa Kas Bersih', $profit],
            ['Cost-to-Income Ratio (%)', $costRatio . '%'],
            ['Total Alokasi Pengeluaran', $expenseCount . ' Transaksi'],
            ['Kategori Pengeluaran tertinggi', $topExpense],
        ];
    }

    public function title(): string
    {
        return 'Ringkasan Pengeluaran';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DC2626']], // Merah Pengeluaran
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // 🟢 PERBAIKAN UTAMA: Sejajarkan kolom A & B rata kiri penuh agar tidak mencong
        $sheet->getStyle('A2:B10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // 🟢 PERBAIKAN FORMAT: Format angka ribuan ribuan TANPA lambang "Rp" di depannya
        $sheet->getStyle('B4:B6')->getNumberFormat()->setFormatCode('#,##0');
        
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('readonly');
    }
}