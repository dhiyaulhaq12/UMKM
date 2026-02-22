<?php

namespace App\Exports\Reports;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpenseSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
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
            ->selectRaw("
                transaction_date as Tanggal,
                'Pengeluaran' as Tipe,
                category as Kategori,
                amount as Jumlah
            ")
            ->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Tipe', 'Kategori', 'Jumlah'];
    }

    public function title(): string
    {
        return 'Pengeluaran';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->freezePane('A2');

        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
                'wrapText' => true,
            ],
        ]);

        $sheet->getStyle('A2:D1000')->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center')
            ->setWrapText(true);

        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('readonly');
    }
}
