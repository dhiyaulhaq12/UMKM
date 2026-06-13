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

class IncomeJournalSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping, ShouldAutoSize
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
            ->orderByDesc('transaction_date')
            ->orderByDesc('transaction_time')
            ->get();
    }

    public function map($trx): array
    {
        $qty = $trx->quantity ?? 1;
        // Kalkulasi Harga Satuan otomatis
        $hargaSatuan = $trx->amount / $qty;

        return [
            Carbon::parse($trx->transaction_date)->format('d/m/Y'),
            $trx->transaction_time ? Carbon::parse($trx->transaction_time)->format('H:i') . ' WIB' : '00:00 WIB',
            $trx->category,
            $hargaSatuan, 
            $qty . ' ' . ($trx->unit ?? 'pcs'),
            $trx->note ?? '-',
            $trx->amount,
        ];
    }

    public function headings(): array
    {
        return ['Tanggal', 'Waktu', 'Item Produk', 'Harga Satuan', 'Kuantitas', 'Catatan', 'Subtotal'];
    }

    public function title(): string
    {
        return 'Rincian Pendapatan';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Terapkan style dasar dari Trait BaseSheet
        $this->applyStyle($sheet, $lastRow, 'G', '16A34A');

        // 🟢 PERBAIKAN: Format angka ribuan tanpa teks "Rp" untuk Harga Satuan (Kolom D)
        $sheet->getStyle("D2:D{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // 🟢 PERBAIKAN: Format angka ribuan tanpa teks "Rp" untuk Subtotal (Kolom G)
        $sheet->getStyle("G2:G{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0');
    }
}