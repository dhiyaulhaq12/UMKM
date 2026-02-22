<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PredictionController extends Controller
{
    public function index(Request $request)
    {
        // ===============================
        // BULAN AKTIF (DEFAULT SEKARANG)
        // ===============================
        $month = $request->get('summary_month', now()->format('Y-m'));
        [$year, $monthNumber] = explode('-', $month);
    
        // ===============================
        // DATA DASAR
        // ===============================
        $query = Transaction::where('user_id', Auth::id())
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $monthNumber);
    
        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $query)->where('type', 'expense')->sum('amount');
        $profit = $totalIncome - $totalExpense;
        $totalTransactions = (clone $query)->count();
    
        // ===============================
        // PREDIKSI 3 BULAN KE DEPAN
        // ===============================
        $predictions = [];
    
        for ($i = 1; $i <= 3; $i++) {
            $futureMonth = \Carbon\Carbon::createFromDate($year, $monthNumber, 1)
                ->addMonths($i);
    
            // growth dummy (5% per bulan)
            $growthRate = 1 + (0.05 * $i);
            $predictedProfit = round($profit * $growthRate);
    
            $predictions[] = [
                'month'  => $futureMonth->translatedFormat('F Y'),
                'amount' => $predictedProfit,
                'accuracy' => 70 - ($i * 5), // dummy akurasi
            ];
        }
    
        return view('predictions.index', compact(
            'month',
            'totalIncome',
            'totalExpense',
            'profit',
            'totalTransactions',
            'predictions'
        ));
    }
    


    public function generate(Request $request)
    {
        // ambil 3 bulan terakhir
        $transactions = Transaction::where('user_id', Auth::id())
            ->where('type', 'income')
            ->orderByDesc('transaction_date')
            ->take(90)
            ->get();

        $averageProfit = $transactions->avg('amount') ?? 0;

        // prediksi sederhana (dummy logis)
        $predictedProfit = round($averageProfit * 0.9);
        $accuracy = 70;

        $nextMonth = now()->addMonth()->translatedFormat('F Y');

        return back()->with('prediction', [
            'month' => $nextMonth,
            'value' => $predictedProfit,
            'accuracy' => $accuracy,
        ]);
    }
}
