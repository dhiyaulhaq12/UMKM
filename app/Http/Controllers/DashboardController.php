<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil dari 'summary_month' sesuai dengan yang ada di file summary.blade Anda
        $month = $request->get('summary_month', now()->format('Y-m'));
        
        [$year, $monthNumber] = explode('-', $month);
        $monthCarbon = Carbon::createFromDate($year, $monthNumber, 1);

        // Query Bulanan
        $baseQuery = Transaction::where('user_id', Auth::id())
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $monthNumber);

        $totalIncome = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $baseQuery)->where('type', 'expense')->sum('amount');
        $profit = $totalIncome - $totalExpense;
        $totalTransactions = (clone $baseQuery)->count();

        // Metrik
        $profitMargin = $totalIncome > 0 ? round(($profit / $totalIncome) * 100, 1) : 0;
        $ratio = $totalExpense > 0 ? round(($totalIncome / $totalExpense), 2) : 0;
        $health = $profit > 0 ? 'Untung' : ($profit < 0 ? 'Rugi' : 'Imbang');

        // Distribusi Keuangan (Donat)
        $distributionData = (clone $baseQuery)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $totalAll = $distributionData->sum('total');
        $distributionData->transform(function($item) use ($totalAll) {
            $item->percentage = $totalAll > 0 ? round(($item->total / $totalAll) * 100, 1) : 0;
            return $item;
        });

        // Trend Tahunan (Ambil Tahun saja)
        $monthlyIncome = Transaction::where('user_id', Auth::id())->where('type', 'income')->whereYear('transaction_date', $year)
            ->selectRaw('EXTRACT(MONTH FROM transaction_date) as month, SUM(amount) as total')
            ->groupBy('month')->pluck('total', 'month')->all();

        $monthlyExpense = Transaction::where('user_id', Auth::id())->where('type', 'expense')->whereYear('transaction_date', $year)
            ->selectRaw('EXTRACT(MONTH FROM transaction_date) as month, SUM(amount) as total')
            ->groupBy('month')->pluck('total', 'month')->all();

        $chartDataIncome = []; $chartDataExpense = []; $chartDataProfit = [];
        for ($m = 1; $m <= 12; $m++) {
            $inc = $monthlyIncome[$m] ?? 0; $exp = $monthlyExpense[$m] ?? 0;
            $chartDataIncome[] = $inc; $chartDataExpense[] = $exp; $chartDataProfit[] = $inc - $exp;
        }

        return view('dashboard.index', compact(
            'month', 'monthCarbon', 'totalIncome', 'totalExpense', 'profit', 
            'totalTransactions', 'profitMargin', 'ratio', 'health',
            'chartDataIncome', 'chartDataExpense', 'chartDataProfit', 'distributionData'
        ));
    }
}