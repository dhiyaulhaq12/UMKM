<?php

namespace App\Exports\Reports;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithTitle,
    WithStyles,
    WithMapping,
    ShouldAutoSize
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionSheet implements
    FromCollection,
    WithHeadings,
    WithTitle,
    WithStyles,
    WithMapping,
    ShouldAutoSize
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year  = $year;
        $this->month = $month;
    }

    public function collection()
    {
        return Transaction::where('user_id', Auth::id())
            ->whereYear('transaction_date', $this->year)
            ->whereMonth('transaction_date', $this->month)
            ->orderBy('transaction_date')
            ->get();
    }

    /**
     * ⬅️ TANGGAL DIKIRIM SEBAGAI CARBON (BUKAN STRING)
     */
    public function map($trx): array
    {
        return [
            Carbon::parse($trx->transaction_date), // ← PENTING
            $trx->type === 'income' ? 'Pendapatan' : 'Pengeluaran',
            $trx->category,
            $trx->note,
            $trx->amount,
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Tipe',
            'Kategori',
            'Catatan',
            'Jumlah (Rp)',
        ];
    }

    public function title(): string
    {
        return 'Detail Transaksi';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        /* HEADER */
        $sheet->getStyle('A1:E1')->applyFromArray([
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
            ],
            'protection' => [
                'locked' => true,
            ],
        ]);

        /* ISI */
        $sheet->getStyle("A2:E{$lastRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'wrapText' => true,
            ],
            'protection' => [
                'locked' => false,
            ],
        ]);

        /* FORMAT TANGGAL → 19/12/2025 */
        $sheet->getStyle("A2:A{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('dd/mm/yyyy');

        /* FREEZE HEADER */
        $sheet->freezePane('A2');

        /* PROTECT */
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('readonly');
    }
}
