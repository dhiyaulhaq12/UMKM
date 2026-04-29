<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index(Request $request)
{
    /* ===============================
       SUMMARY (BULAN)
    =============================== */

    // default = bulan sekarang
    $month = $request->get('summary_month', now()->format('Y-m'));

    [$year, $monthNumber] = explode('-', $month);

    $monthCarbon = \Carbon\Carbon::createFromDate($year, $monthNumber, 1);


    $summaryQuery = Transaction::where('user_id', Auth::id())
        ->whereYear('transaction_date', $year)
        ->whereMonth('transaction_date', $monthNumber);

    $totalIncome = (clone $summaryQuery)
        ->where('type', 'income')
        ->sum('amount');

    $totalExpense = (clone $summaryQuery)
        ->where('type', 'expense')
        ->sum('amount');

    $profit = $totalIncome - $totalExpense;

    $totalTransactions = (clone $summaryQuery)->count();

    /* ===============================
       RIWAYAT TRANSAKSI (FILTER SENDIRI)
    =============================== */

    $transactionsQuery = Transaction::where('user_id', Auth::id());

    if ($request->filled('search')) {
        $transactionsQuery->where(
            'category',
            'ILIKE',
            '%' . $request->search . '%'
        );
    }
    

    if ($request->filled('start_date')) {
        $transactionsQuery->whereDate(
            'transaction_date',
            '>=',
            $request->start_date
        );
    }

    if ($request->filled('end_date')) {
        $transactionsQuery->whereDate(
            'transaction_date',
            '<=',
            $request->end_date
        );
    }

    $transactions = $transactionsQuery
        ->orderByDesc('created_at')
        ->paginate(10)
        ->withQueryString();

    return view('transactions.index', compact(
        'transactions',
        'totalIncome',
        'totalExpense',
        'profit',
        'totalTransactions',
        'month',
        'monthCarbon'
    ));
}
public function store(Request $request)
{
    // 1. Bersihkan input amount
    if ($request->has('amount')) {
        $cleanAmount = preg_replace('/[^0-9]/', '', $request->amount);
        $request->merge(['amount' => $cleanAmount]);
    }

    // 2. Ambil tipe bisnis user
    $user = Auth::user();
    $businessType = $user->business_type;

    // 3. Tentukan Kategori Valid berdasarkan Role UMKM
    $validCategories = [];
    if ($request->type === 'income') {
        if ($businessType === 'Mikro') {
            $validCategories = ["Penjualan Produk", "Lainnya"];
        } elseif ($businessType === 'Kecil') {
            $validCategories = ["Penjualan Produk", "Penjualan Jasa", "Hadiah/Bonus", "Lainnya"];
        } else {
            // Menengah: Semua tampil
            $validCategories = ["Penjualan Produk", "Penjualan Jasa", "Investasi", "Sewa Properti", "Royalti", "Bunga Bank", "Hadiah/Bonus", "Lainnya"];
        }
    } else {
        // Untuk expense sementara kita samakan semua dulu sesuai code awalmu
        $validCategories = ["Bahan Baku", "Operasional", "Gaji Karyawan", "Marketing", "Transportasi", "Sewa Tempat", "Utilitas (Listrik, Air, Internet)", "Asuransi", "Maintenance", "Lainnya"];
    }

    // 4. Validasi
    $request->validate([
        'type' => 'required|in:income,expense',
        'category' => 'required|in:' . implode(',', $validCategories), // Validasi ketat di sini
        'amount' => 'required|numeric|min:0',
        'transaction_date' => 'required|date',
        'note' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ], [
        'category.in' => 'Kategori yang dipilih tidak valid untuk skala usaha ' . $businessType
    ]);

        $imageUrl = null;

        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $realPath = $file->getRealPath() ?: $file->getPathname();

                // 🔥 INI SUDAH OTOMATIS PAKAI CLOUDINARY_URL
                $uploaded = (new UploadApi())->upload($realPath, [
                    'folder' => 'transactions',
                    'resource_type' => 'image',
                ]);

                $imageUrl = $uploaded['secure_url'] ?? null;

            } catch (\Exception $e) {
                Log::error('Cloudinary upload error', [
                    'error' => $e->getMessage(),
                ]);

                return back()->withErrors([
                    'image' => 'Gagal mengupload gambar ke Cloudinary',
                ]);
            }
        }

        Transaction::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'category' => $request->category,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'note' => $request->note,
            'image' => $imageUrl,
        ]);

        return back()->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function update(Request $request, Transaction $transaction)
{
    $request->validate([
        'type' => 'required|in:income,expense',
        'category' => 'required|string',
        'amount' => 'required|numeric|min:0',
        'transaction_date' => 'required|date',
        'note' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // default: pakai gambar lama
    $imageUrl = $transaction->image;

    /* ===============================
       HAPUS GAMBAR JIKA DICENTANG
    =============================== */
    if ($request->filled('remove_image')) {
        $imageUrl = null;
    }

    /* ===============================
       UPLOAD GAMBAR BARU (CLOUDINARY)
    =============================== */
    if ($request->hasFile('image')) {
        try {
            $file = $request->file('image');
            $realPath = $file->getRealPath() ?: $file->getPathname();

            // 🔥 SAMA PERSIS DENGAN STORE
            $uploaded = (new UploadApi())->upload($realPath, [
                'folder' => 'transactions',
                'resource_type' => 'image',
            ]);

            $imageUrl = $uploaded['secure_url'] ?? null;

        } catch (\Exception $e) {
            Log::error('Cloudinary upload error (update)', [
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'image' => 'Gagal mengupload gambar ke Cloudinary',
            ]);
        }
    }

    /* ===============================
       UPDATE DATA TRANSAKSI
    =============================== */
    $transaction->update([
        'type' => $request->type,
        'category' => $request->category,
        'amount' => $request->amount,
        'transaction_date' => $request->transaction_date,
        'note' => $request->note,
        'image' => $imageUrl,
    ]);

    return back()->with('success', 'Transaksi berhasil diperbarui');
}




    public function destroy(Transaction $transaction)
{
    // Pastikan hanya pemilik yang bisa hapus
    if ($transaction->user_id !== auth()->id()) {
        abort(403);
    }

    // (opsional) hapus gambar cloudinary di sini

    $transaction->delete();

    return back()->with('success', 'Transaksi berhasil dihapus');
}
}
