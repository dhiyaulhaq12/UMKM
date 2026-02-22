<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\Reports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;





class ReportController extends Controller
{
    public function index(Request $request)
{
    /* ===============================
       BULAN AKTIF (SATU SUMBER)
    =============================== */

    // PRIORITAS:
    // 1. month (laporan utama)
    // 2. summary_month (jika ada)
    // 3. bulan sekarang
    $month = $request->get(
        'month',
        $request->get('summary_month', now()->format('Y-m'))
    );

    [$year, $monthNumber] = explode('-', $month);

    $monthCarbon = \Carbon\Carbon::createFromDate($year, $monthNumber, 1);

    $baseQuery = Transaction::where('user_id', Auth::id())
        ->whereYear('transaction_date', $year)
        ->whereMonth('transaction_date', $monthNumber);

    /* ===============================
       TOTAL
    =============================== */

    $totalIncome = (clone $baseQuery)
        ->where('type', 'income')
        ->sum('amount');

    $totalExpense = (clone $baseQuery)
        ->where('type', 'expense')
        ->sum('amount');

    $profit = $totalIncome - $totalExpense;
    $totalTransactions = (clone $baseQuery)->count();

    /* ===============================
       RINCIAN PENGELUARAN
    =============================== */

    $expensesByCategory = (clone $baseQuery)
        ->where('type', 'expense')
        ->selectRaw('category, SUM(amount) as total')
        ->groupBy('category')
        ->get();

    /* ===============================
       METRIK
    =============================== */

    $profitMargin = $totalIncome > 0
        ? round(($profit / $totalIncome) * 100, 1)
        : 0;

    $ratio = $totalIncome > 0
        ? round(($totalExpense / $totalIncome) * 100, 1)
        : 0;

    $health =
        $profitMargin >= 30 ? 'Sehat' :
        ($profitMargin >= 10 ? 'Cukup' : 'Kurang');

    return view('reports.index', compact(
        'month',
        'monthCarbon',
        'totalIncome',
        'totalExpense',
        'profit',
        'totalTransactions',
        'expensesByCategory',
        'profitMargin',
        'ratio',
        'health'
    ));
}

public function exportPdf(Request $request)
{
    $month = $request->get('month', now()->format('Y-m'));
    [$year, $monthNumber] = explode('-', $month);

    $monthCarbon = Carbon::createFromDate($year, $monthNumber, 1);

    $baseQuery = Transaction::where('user_id', Auth::id())
        ->whereYear('transaction_date', $year)
        ->whereMonth('transaction_date', $monthNumber);

    $totalIncome = (clone $baseQuery)->where('type', 'income')->sum('amount');
    $totalExpense = (clone $baseQuery)->where('type', 'expense')->sum('amount');
    $profit = $totalIncome - $totalExpense;
    $totalTransactions = (clone $baseQuery)->count();

    $incomeByCategory = (clone $baseQuery)
        ->where('type', 'income')
        ->selectRaw('category, SUM(amount) as total')
        ->groupBy('category')
        ->get();

    $expenseByCategory = (clone $baseQuery)
        ->where('type', 'expense')
        ->selectRaw('category, SUM(amount) as total')
        ->groupBy('category')
        ->get();

    
    $businessName = Auth::user()->business_name ?? 'Nama Usaha';

    $pdf = Pdf::loadView('reports.pdf', compact(
        'monthCarbon',
        'businessName',
        'totalIncome',
        'totalExpense',
        'profit',
        'totalTransactions',
        'incomeByCategory',
        'expenseByCategory'
    ));
    
    

    return $pdf->download(
        'Laporan_Keuangan_' . $monthCarbon->format('F_Y') . '.pdf'
    );
}

public function exportExcel(Request $request)
    {
        $month = $request->get('summary_month', now()->format('Y-m'));
        [$year, $monthNumber] = explode('-', $month);

        return Excel::download(
            new ReportsExport($year, $monthNumber),
            "Laporan_Keuangan_{$year}_{$monthNumber}.xlsx"
        );
    }
}
