<?php

namespace App\Exports\Reports;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpenseJournalSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping, ShouldAutoSize
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
            ->orderByDesc('transaction_date')
            ->orderByDesc('transaction_time')
            ->get();
    }

    public function map($trx): array
    {
        return [
            Carbon::parse($trx->transaction_date)->format('d/m/Y'),
            $trx->transaction_time ? Carbon::parse($trx->transaction_time)->format('H:i') . ' WIB' : '00:00 WIB',
            $trx->category,
            $trx->note ?? '-',
            $trx->amount,
        ];
    }

    public function headings(): array
    {
        return ['Tanggal', 'Waktu', 'Kebutuhan Operasional', 'Keterangan Catatan', 'Subtotal Biaya'];
    }

    public function title(): string
    {
        return 'Rincian Pengeluaran';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        // Kolom E otomatis terformat angka ribuan tanpa "Rp" berkat pembaruan Trait BaseSheet kita
        $this->applyStyle($sheet, $lastRow, 'E', 'DC2626');
    }
}