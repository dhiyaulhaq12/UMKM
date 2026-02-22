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

class SummarySheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
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
        $query = Transaction::where('user_id', Auth::id())
            ->whereYear('transaction_date', $this->year)
            ->whereMonth('transaction_date', $this->month);

        $income = (clone $query)->where('type', 'income')->sum('amount');
        $expense = (clone $query)->where('type', 'expense')->sum('amount');

        return [
            ['LAPORAN KEUANGAN'],
            ['Periode', Carbon::create($this->year, $this->month)->translatedFormat('F Y')],
            ['Pendapatan', $income],
            ['Pengeluaran', $expense],
            ['Keuntungan', $income - $expense],
            ['Jumlah Transaksi', (clone $query)->count()],
        ];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:B1');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        $sheet->getStyle('A1:B10')->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center')
            ->setWrapText(true);

        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('readonly');
    }
}
