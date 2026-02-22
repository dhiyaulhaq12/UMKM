<?php

namespace App\Exports\Reports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait BaseSheet
{
    public function applyStyle(Worksheet $sheet, $lastRow, $lastCol)
    {
        // HEADER
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
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
                'vertical'   => 'center',
            ],
            'protection' => [
                'locked' => true,
            ],
        ]);

        // ISI
        $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical'   => 'center',
                'wrapText'   => true,
            ],
            'protection' => [
                'locked' => false,
            ],
        ]);

        $sheet->freezePane('A2');
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('readonly');
    }
}
