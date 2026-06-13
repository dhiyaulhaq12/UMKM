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

class ProductAnalysisSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping, ShouldAutoSize
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
            ->where('type', 'income')
            ->whereYear('transaction_date', $this->year)
            ->whereMonth('transaction_date', $this->month)
            ->selectRaw('category, COUNT(*) as frekuensi, SUM(COALESCE(quantity, 1)) as total_qty, MAX(unit) as unit_name, SUM(amount) as total_omset')
            ->groupBy('category')
            ->orderByDesc('total_omset') // Otomatis terurut dari omset terbesar
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->category,
            $row->frekuensi,
            $row->total_qty . ' ' . ($row->unit_name ?? 'pcs'),
            $row->total_omset,
        ];
    }

    public function headings(): array
    {
        return ['Item',
                'Frekuensi Transaksi',
                'Total Kuantitas Terjual',
                'Akumulasi Omset (Rp)'];
    }

    public function title(): string
    {
        return 'Rekapitulasi Sumber Pendapatan';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $this->applyStyle($sheet, $lastRow, 'D', '16A34A');
    }
}