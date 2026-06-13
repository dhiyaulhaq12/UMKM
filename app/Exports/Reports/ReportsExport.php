<?php

namespace App\Exports\Reports;

use App\Exports\Reports\SummarySheet;
use App\Exports\Reports\IncomeSheet;
use App\Exports\Reports\ExpenseSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsExport implements WithMultipleSheets
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year  = $year;
        $this->month = $month;
    }

    public function sheets(): array
    {
        // Mengembalikan susunan tab sheet berurutan: Ringkasan -> Pendapatan -> Pengeluaran
        return [
            new SummarySheet($this->year, $this->month),
            new IncomeSheet($this->year, $this->month),
            new ExpenseSheet($this->year, $this->month),
        ];
    }
}