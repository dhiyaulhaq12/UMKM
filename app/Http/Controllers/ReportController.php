<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\Reports\IncomeReportExport;
use App\Exports\Reports\ExpenseReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function incomeReport(Request $request)
    {
        /* ===============================
            BULAN AKTIF (SATU SUMBER)
        =============================== */
        $month = $request->get(
            'month',
            $request->get('summary_month', now()->format('Y-m'))
        );

        [$year, $monthNumber] = explode('-', $month);
        $monthCarbon = Carbon::createFromDate($year, $monthNumber, 1);

        $baseQuery = Transaction::where('user_id', Auth::id())
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $monthNumber);

        /* ===============================
            TOTAL RINGKASAN (UNTUK LAYOUT SUMMARY)
        =============================== */
        $totalIncome = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $baseQuery)->where('type', 'expense')->sum('amount');
        $profit = $totalIncome - $totalExpense;
        $totalIncomeTransactions = (clone $baseQuery)->where('type', 'income')->count();
        $totalTransactions = (clone $baseQuery)->count();

        /* ===============================
            METRIK (UNTUK LAYOUT SUMMARY)
        =============================== */
        $profitMargin = $totalIncome > 0 ? round(($profit / $totalIncome) * 100, 1) : 0;
        $ratio = $totalIncome > 0 ? round(($totalExpense / $totalIncome) * 100, 1) : 0;
        $health = $profitMargin >= 30 ? 'Sehat' : ($profitMargin >= 10 ? 'Cukup' : 'Kurang');

        /* ===============================
            LOGIKA KHUSUS HALAMAN PENDAPATAN
        =============================== */
        // 1. Rekapitulasi per kelompok Sumber Pendapatan (Chart/List atas)
        $incomesByCategory = (clone $baseQuery)
            ->where('type', 'income')
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        // 2. Rincian Buku/Jurnal Pendapatan per baris transaksi (Beban pagination 10 item)
        $incomeDetails = (clone $baseQuery)
            ->where('type', 'income')
            // 🟢 Diubah agar select memuat transaction_time dan diurutkan dari yang paling baru berdasarkan tanggal & jam otomatis
            ->select('id', 'category', 'amount', 'transaction_date', 'transaction_time', 'quantity', 'unit', 'note')
            ->orderByDesc('transaction_date')
            ->orderByDesc('transaction_time')
            ->paginate(10)
            ->withQueryString(); 

        return view('reports.income', compact(
            'month',
            'monthCarbon',
            'totalIncome',
            'totalExpense',
            'profit',
            'totalTransactions',
            'totalIncomeTransactions',
            'profitMargin',
            'ratio',
            'health',
            'incomesByCategory',
            'incomeDetails'
        ));
    }

    /* ===============================================================
        2. LAPORAN PENGELUARAN (EXPENSE REPORT)
    =============================================================== */
    public function expenseReport(Request $request)
    {
        /* ===============================
            BULAN AKTIF (SATU SUMBER)
        =============================== */
        $month = $request->get(
            'month',
            $request->get('summary_month', now()->format('Y-m'))
        );

        [$year, $monthNumber] = explode('-', $month);
        $monthCarbon = Carbon::createFromDate($year, $monthNumber, 1);

        $baseQuery = Transaction::where('user_id', Auth::id())
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $monthNumber);

        /* ===============================
            TOTAL RINGKASAN (UNTUK LAYOUT SUMMARY)
        =============================== */
        $totalIncome = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $baseQuery)->where('type', 'expense')->sum('amount');
        $profit = $totalIncome - $totalExpense;
        $totalTransactions = (clone $baseQuery)->count();

        /* ===============================
            METRIK (UNTUK LAYOUT SUMMARY)
        =============================== */
        $profitMargin = $totalIncome > 0 ? round(($profit / $totalIncome) * 100, 1) : 0;
        $ratio = $totalIncome > 0 ? round(($totalExpense / $totalIncome) * 100, 1) : 0;
        $health = $profitMargin >= 30 ? 'Sehat' : ($profitMargin >= 10 ? 'Cukup' : 'Kurang');

        /* ===============================
            LOGIKA KHUSUS HALAMAN PENGELUARAN
        =============================== */
        // 1. Rekapitulasi alokasi biaya pengeluaran per kategori anggaran
        $expensesByCategory = (clone $baseQuery)
        ->where('type', 'expense')
        ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
        ->groupBy('category')
        ->get();

        // 2. Rincian Buku Pengeluaran harian per baris transaksi (Beban pagination 10 item)
        $expenseDetails = (clone $baseQuery)
            ->where('type', 'expense')
            // 🟢 Diubah agar select memuat transaction_time dan diurutkan dari yang paling baru berdasarkan tanggal & jam otomatis
            ->select('id', 'category', 'amount', 'transaction_date', 'transaction_time', 'quantity', 'unit', 'note')
            ->orderByDesc('transaction_date')
            ->orderByDesc('transaction_time')
            ->paginate(10)
            ->withQueryString(); 

        return view('reports.expense', compact(
            'month',
            'monthCarbon',
            'totalIncome',
            'totalExpense',
            'profit',
            'totalTransactions',
            'profitMargin',
            'ratio',
            'health',
            'expensesByCategory',
            'expenseDetails'
        ));
    }

 /* ===============================================================
       🟢 EKSPOR PDF LAPORAN PENDAPATAN (INCOME PDF REPORT)
    =============================================================== */
    public function exportIncomePdf(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $monthNumber] = explode('-', $month);
        $monthCarbon = Carbon::createFromDate($year, $monthNumber, 1);

        $baseQuery = Transaction::where('user_id', Auth::id())
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $monthNumber);

        // Metrik Ringkasan Atas
        $totalIncome = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $baseQuery)->where('type', 'expense')->sum('amount');
        $profit = $totalIncome - $totalExpense;
        $totalTransactions = (clone $baseQuery)->where('type', 'income')->count();

        // 🟢 AMBIL DATA REKAPITULASI DENGAN KUMULATIF KUANTITAS PRODUK TERJUAL
        $incomeByCategoryQuery = (clone $baseQuery)
            ->where('type', 'income')
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count, SUM(COALESCE(quantity, 1)) as total_qty, MAX(unit) as unit_name');

        // 🟢 LOGIKA SORTING DINAMIS SESUAI REQUEST DROPDOWN USER
        $sort = $request->get('sort', 'amount_desc');
        switch ($sort) {
            case 'amount_asc':
                $incomeByCategoryQuery->orderBy('total', 'asc');
                break;
            case 'count_desc':
                $incomeByCategoryQuery->orderBy('count', 'desc');
                break;
            case 'count_asc':
                $incomeByCategoryQuery->orderBy('count', 'asc');
                break;
            case 'amount_desc':
            default:
                $incomeByCategoryQuery->orderBy('total', 'desc');
                break;
        }

        $incomeByCategory = $incomeByCategoryQuery->groupBy('category')->get();

        // Data Rincian Tabel Bawah
        $incomeDetails = (clone $baseQuery)
            ->where('type', 'income')
            ->orderByDesc('transaction_date')
            ->orderByDesc('transaction_time')
            ->get();

        $businessName = Auth::user()->business_name ?? 'Nama Usaha';

        // LOGO PROFIL USER TO BASE64
        $logoBase64 = null;
        $userPhoto = Auth::user()->photo;
        if ($userPhoto) {
            try {
                $imageData = file_get_contents($userPhoto);
                if ($imageData !== false) {
                    $logoBase64 = 'data:image/' . pathinfo($userPhoto, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageData);
                }
            } catch (\Exception $e) {
                Log::error('Gagal memuat foto profil di PDF Pendapatan: ' . $e->getMessage());
            }
        }

        $pdf = Pdf::loadView('reports.income_pdf', compact(
            'monthCarbon', 'businessName', 'totalIncome', 'totalExpense', 'profit',
            'totalTransactions', 'incomeByCategory', 'incomeDetails', 'logoBase64'
        ));

        return $pdf->download('Laporan_Pendapatan_' . $monthCarbon->format('F_Y') . '.pdf');
    }

    /* ===============================================================
       🟢 EKSPOR PDF LAPORAN PENGELUARAN (EXPENSE PDF REPORT)
    =============================================================== */
    public function exportExpensePdf(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $monthNumber] = explode('-', $month);
        $monthCarbon = Carbon::createFromDate($year, $monthNumber, 1);

        $baseQuery = Transaction::where('user_id', Auth::id())
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $monthNumber);

        // Metrik Ringkasan Atas
        $totalIncome = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $baseQuery)->where('type', 'expense')->sum('amount');
        $profit = $totalIncome - $totalExpense;
        $totalTransactions = (clone $baseQuery)->where('type', 'expense')->count();

        // 🟢 AMBIL DATA REKAPITULASI BIAYA ANGGARAN PENGELUARAN
        $expenseByCategoryQuery = (clone $baseQuery)
            ->where('type', 'expense')
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count, SUM(COALESCE(quantity, 1)) as total_qty, MAX(unit) as unit_name');

        // 🟢 LOGIKA SORTING DINAMIS SESUAI REQUEST DROPDOWN USER
        $sort = $request->get('sort', 'amount_desc');
        switch ($sort) {
            case 'amount_asc':
                $expenseByCategoryQuery->orderBy('total', 'asc');
                break;
            case 'count_desc':
                $expenseByCategoryQuery->orderBy('count', 'desc');
                break;
            case 'count_asc':
                $expenseByCategoryQuery->orderBy('count', 'asc');
                break;
            case 'amount_desc':
            default:
                $expenseByCategoryQuery->orderBy('total', 'desc');
                break;
        }

        $expenseByCategory = $expenseByCategoryQuery->groupBy('category')->get();

        // Data Rincian Tabel Bawah
        $expenseDetails = (clone $baseQuery)
            ->where('type', 'expense')
            ->orderByDesc('transaction_date')
            ->orderByDesc('transaction_time')
            ->get();

        $businessName = Auth::user()->business_name ?? 'Nama Usaha';

        // LOGO PROFIL USER TO BASE64
        $logoBase64 = null;
        $userPhoto = Auth::user()->photo;
        if ($userPhoto) {
            try {
                $imageData = file_get_contents($userPhoto);
                if ($imageData !== false) {
                    $logoBase64 = 'data:image/' . pathinfo($userPhoto, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageData);
                }
            } catch (\Exception $e) {
                Log::error('Gagal memuat foto profil di PDF Pengeluaran: ' . $e->getMessage());
            }
        }

        $pdf = Pdf::loadView('reports.expense_pdf', compact(
            'monthCarbon', 'businessName', 'totalIncome', 'totalExpense', 'profit',
            'totalTransactions', 'expenseByCategory', 'expenseDetails', 'logoBase64'
        ));

        return $pdf->download('Laporan_Pengeluaran_' . $monthCarbon->format('F_Y') . '.pdf');
    }

    public function exportIncomeExcel(Request $request)
    {
        $month = $request->get('month', $request->get('summary_month', now()->format('Y-m')));
        [$year, $monthNumber] = explode('-', $month);

        return Excel::download(
            new IncomeReportExport($year, $monthNumber),
            "Laporan_Omset_Pendapatan_{$year}_{$monthNumber}.xlsx"
        );
    }

    /* ===============================================================
       🟢 EKSPOR EXCEL SEKTOR PENGELUARAN (EXPENSE EXCEL MUTIPLE-SHEETS)
    =============================================================== */
    public function exportExpenseExcel(Request $request)
    {
        $month = $request->get('month', $request->get('summary_month', now()->format('Y-m')));
        [$year, $monthNumber] = explode('-', $month);

        return Excel::download(
            new ExpenseReportExport($year, $monthNumber),
            "Laporan_Analisis_Pengeluaran_{$year}_{$monthNumber}.xlsx"
        );
    }
}