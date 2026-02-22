<?php

namespace App\Exports\Reports;


use App\Exports\Reports\SummarySheet;
use App\Exports\Reports\IncomeSheet;
use App\Exports\Reports\ExpenseSheet;
use App\Exports\Reports\TransactionSheet;
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
        return [
            new SummarySheet($this->year, $this->month),
            new IncomeSheet($this->year, $this->month),
            new ExpenseSheet($this->year, $this->month),
            new TransactionSheet($this->year, $this->month),
        ];
    }
}
