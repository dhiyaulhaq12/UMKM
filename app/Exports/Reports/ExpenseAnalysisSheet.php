<?php

namespace App\Exports\Reports;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpenseAnalysisSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping, ShouldAutoSize
{
    use BaseSheet;

    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        return Transaction::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->whereYear('transaction_date', $this->year)
            ->whereMonth('transaction_date', $this->month)
            ->selectRaw('category, COUNT(*) as frekuensi, SUM(amount) as total_biaya')
            ->groupBy('category')
            ->orderByDesc('total_biaya') 
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->category,
            $row->frekuensi . ' Kali',
            $row->total_biaya,
        ];
    }

    public function headings(): array
    {
        return ['Kategori Anggaran / Beban', 'Frekuensi', 'Total Pengeluaran'];
    }

    public function title(): string
    {
        return 'Alokasi Biaya Pengeluaran';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        // Kolom C otomatis terformat angka ribuan tanpa "Rp" berkat pembaruan Trait BaseSheet kita
        $this->applyStyle($sheet, $lastRow, 'C', 'DC2626');
    }
}