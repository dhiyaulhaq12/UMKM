<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class IncomeReportExport implements WithMultipleSheets
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
            new SummaryIncomeSheet($this->year, $this->month),
            new ProductAnalysisSheet($this->year, $this->month),
            new IncomeJournalSheet($this->year, $this->month),
        ];
    }
}