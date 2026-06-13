<?php

namespace App\Http\Controllers;

use App\Models\CustomCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomCategoryController extends Controller
{
    public function index()
    {
        $categories = CustomCategory::where('user_id', Auth::id())
                                ->orderBy('created_at', 'desc')
                                ->paginate(2);

        return view('transactions.categories', compact('categories'));

        
    }

    public function store(Request $request)
    {
        // Bersihkan input harga dari titik pemisah rupiah jika ada
        if ($request->has('default_price')) {
            $cleanPrice = preg_replace('/[^0-9]/', '', $request->default_price);
            $request->merge(['default_price' => $cleanPrice]);
        }

        $request->validate([
            'type' => 'required|in:income,expense',
            'name' => 'required|string|max:100',
            'default_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
        ]);

        CustomCategory::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'name' => $request->name,
            'default_price' => $request->default_price,
            'unit' => $request->unit,
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
{
    $category = CustomCategory::where('user_id', Auth::id())->findOrFail($id);

    // Bersihkan input harga dari titik pemisah rupiah jika ada
    if ($request->has('default_price')) {
        $cleanPrice = preg_replace('/[^0-9]/', '', $request->default_price);
        $request->merge(['default_price' => $cleanPrice]);
    }

    $request->validate([
        'type' => 'required|in:income,expense',
        'name' => 'required|string|max:100',
        'default_price' => 'required|numeric|min:0',
        'unit' => 'required|string|max:20',
    ]);

    $category->update([
        'type' => $request->type,
        'name' => $request->name,
        'default_price' => $request->default_price,
        'unit' => $request->unit,
    ]);

    return back()->with('success', 'Kategori berhasil diperbarui!');
}

    public function destroy($id)
    {
        $category = CustomCategory::where('user_id', Auth::id())->findOrFail($id);
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}