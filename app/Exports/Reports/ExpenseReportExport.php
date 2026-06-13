<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExpenseReportExport implements WithMultipleSheets
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
            new SummaryExpenseSheet($this->year, $this->month),
            new ExpenseAnalysisSheet($this->year, $this->month),
            new ExpenseJournalSheet($this->year, $this->month),
        ];
    }
}