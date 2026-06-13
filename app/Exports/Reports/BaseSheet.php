<?php

namespace App\Exports\Reports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait BaseSheet
{
    public function applyStyle(Worksheet $sheet, $lastRow, $lastCol, $headerColor = '2563EB')
    {
        // HEADER
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => $headerColor],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical'   => 'center',
            ],
        ]);

        // ISI DATA
        $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical'   => 'center',
                'wrapText'   => true,
            ],
        ]);

        // 🟢 PERBAIKAN: Format kolom finansial akhir menjadi pemisah ribuan saja tanpa teks "Rp"
        $sheet->getStyle("{$lastCol}2:{$lastCol}{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $sheet->freezePane('A2');
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('readonly');
    }
}