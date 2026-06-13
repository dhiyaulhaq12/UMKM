<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\CustomCategory;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        /* ===============================
            SUMMARY (BULAN) - ASLI UTUH
        =============================== */
        $month = $request->get('summary_month', now()->format('Y-m'));
        [$year, $monthNumber] = explode('-', $month);
        $monthCarbon = \Carbon\Carbon::createFromDate($year, $monthNumber, 1);

        $summaryQuery = Transaction::where('user_id', Auth::id())
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $monthNumber);

        $totalIncome = (clone $summaryQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $summaryQuery)->where('type', 'expense')->sum('amount');
        $profit = $totalIncome - $totalExpense;
        $totalTransactions = (clone $summaryQuery)->count();

        /* ===============================
            RIWAYAT TRANSAKSI (SUBQUERY GROUPING DENGAN FILTRASI WAKTU)
        =============================== */
        $subQuery = Transaction::where('user_id', Auth::id());

        if ($request->filled('search')) {
            $subQuery->where('category', 'ILIKE', '%' . $request->search . '%');
        }
        if ($request->filled('start_date')) {
            $subQuery->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $subQuery->whereDate('transaction_date', '<=', $request->end_date);
        }

        $groupedIds = (clone $subQuery)
            ->selectRaw("MAX(id) as id")
            ->groupBy(\DB::raw("COALESCE(session_code, 'OLD-' || id)"))
            ->pluck('id');

        $transactions = Transaction::whereIn('id', $groupedIds)
            ->selectRaw("
                id,
                user_id,
                session_code,
                type,
                category,
                amount,
                transaction_date,
                transaction_time,
                note,
                image,
                quantity,
                unit,
                created_at,
                (
                    SELECT SUM(t2.amount) 
                    FROM transactions t2 
                    WHERE COALESCE(t2.session_code, 'OLD-' || t2.id) = COALESCE(transactions.session_code, 'OLD-' || transactions.id)
                ) as total_amount,
                (
                    SELECT STRING_AGG(t3.category || ' (' || COALESCE(t3.quantity, 1) || ' ' || COALESCE(t3.unit, 'pcs') || ')', ', ') 
                    FROM transactions t3 
                    WHERE COALESCE(t3.session_code, 'OLD-' || t3.id) = COALESCE(transactions.session_code, 'OLD-' || transactions.id)
                ) as items_summary
            ")
            ->orderByDesc('transaction_date') // 🟢 Urutkan Tanggal paling baru
            ->orderByDesc('transaction_time'); // 🟢 Urutkan Jam paling baru dalam hari tersebut

        // Menangani pagination
        if ($request->filled('search') || $request->filled('start_date') || $request->filled('end_date')) {
            $transactions = $transactions->get(); 
        } else {
            $transactions = $transactions->paginate(10)->withQueryString();
        }

        $customIncomeCategories = CustomCategory::where('user_id', Auth::id())
            ->where('type', 'income')
            ->get();

        return view('transactions.index', compact(
            'transactions', 'totalIncome', 'totalExpense', 'profit', 
            'totalTransactions', 'month', 'monthCarbon', 'customIncomeCategories'
        ));
    }

    public function getGroupItems($id)
    {
        $mainTransaction = Transaction::findOrFail($id);
        if ($mainTransaction->user_id !== Auth::id()) { return response()->json([], 403); }

        if ($mainTransaction->session_code) {
            $items = Transaction::where('session_code', $mainTransaction->session_code)
                ->where('user_id', Auth::id())
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $items = collect([$mainTransaction]);
        }

        return response()->json([
            'main' => $mainTransaction,
            'items' => $items
        ]);
    }

    public function create()
    {
        $customIncomeCategories = CustomCategory::where('user_id', Auth::id())
            ->where('type', 'income')
            ->get();

        return view('transactions.create', compact('customIncomeCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'transaction_date' => 'required|date',
            'note' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.quantity' => 'nullable|numeric',
            'items.*.unit' => 'nullable|string',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $realPath = $file->getRealPath() ?: $file->getPathname();
                $uploaded = (new UploadApi())->upload($realPath, [
                    'folder' => 'transactions',
                    'resource_type' => 'image',
                ]);
                $imageUrl = $uploaded['secure_url'] ?? null;
            } catch (\Exception $e) {
                Log::error('Cloudinary upload error', ['error' => $e->getMessage()]);
                return back()->withErrors(['image' => 'Gagal mengupload gambar ke Cloudinary']);
            }
        }

        $sessionCode = 'TRX-' . strtoupper(Str::random(8)) . '-' . time();
        $currentTime = now()->format('H:i:s'); // 🟢 Capture otomatis jam sistem saat simpan transaksi baru

        foreach ($request->items as $item) {
            Transaction::create([
                'user_id' => Auth::id(),
                'session_code' => $sessionCode,
                'type' => $request->type,
                'category' => $item['category'],
                'amount' => $item['amount'],
                'transaction_date' => $request->transaction_date,
                'transaction_time' => $currentTime, // 🟢 Disimpan otomatis ke database
                'note' => $request->note,
                'image' => $imageUrl,
                'quantity' => $request->type === 'income' ? ($item['quantity'] ?? 1) : null,
                'unit' => $request->type === 'income' ? ($item['unit'] ?? 'pcs') : null,
            ]);
        }

        return redirect()->route('transactions.index')->with('success', 'Transaksi multi-item berhasil disimpan!');
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) { abort(403); }

        $request->validate([
            'type' => 'required|in:income,expense',
            'transaction_date' => 'required|date',
            'note' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.quantity' => 'nullable|numeric',
            'items.*.unit' => 'nullable|string',
        ]);

        $imageUrl = $transaction->image;
        if ($request->filled('remove_image')) { $imageUrl = null; }

        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $realPath = $file->getRealPath() ?: $file->getPathname();
                $uploaded = (new UploadApi())->upload($realPath, [
                    'folder' => 'transactions',
                    'resource_type' => 'image',
                ]);
                $imageUrl = $uploaded['secure_url'] ?? null;
            } catch (\Exception $e) {
                Log::error('Cloudinary upload error (update)', ['error' => $e->getMessage()]);
                return back()->withErrors(['image' => 'Gagal mengupload gambar ke Cloudinary']);
            }
        }

        $sessionCode = $transaction->session_code ?? 'TRX-' . strtoupper(Str::random(8)) . '-' . time();
        
        // 🟢 Pertahankan jam transaksi asli bawaan grup agar tidak berubah saat diedit, jika data lama kosong di-fallback ke jam saat ini
        $transactionTime = $transaction->transaction_time ?? now()->format('H:i:s');

        if ($transaction->session_code) {
            Transaction::where('session_code', $transaction->session_code)->where('user_id', Auth::id())->delete();
        } else {
            $transaction->delete();
        }

        foreach ($request->items as $item) {
            Transaction::create([
                'user_id' => Auth::id(),
                'session_code' => $sessionCode,
                'type' => $request->type,
                'category' => $item['category'],
                'amount' => $item['amount'],
                'transaction_date' => $request->transaction_date,
                'transaction_time' => $transactionTime, // 🟢 Tetap memakai jam transaksi awal
                'note' => $request->note,
                'image' => $imageUrl,
                'quantity' => $request->type === 'income' ? ($item['quantity'] ?? 1) : null,
                'unit' => $request->type === 'income' ? ($item['unit'] ?? 'pcs') : null,
            ]);
        }

        return back()->with('success', 'Seluruh rangkaian grup transaksi berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) { abort(403); }

        if ($transaction->session_code) {
            Transaction::where('session_code', $transaction->session_code)->where('user_id', auth()->id())->delete();
        } else {
            $transaction->delete();
        }

        return back()->with('success', 'Satu grup transaksi berhasil dihapus!');
    }
}