<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Menampilkan daftar semua user kecuali Admin
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        $users = \App\Models\User::where('role', '!=', 'admin')
            ->when($search, function ($query, $search) {
                // Mengubah kolom 'name' dan input '$search' menjadi lowercase
                return $query->whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($search) . "%"]);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    
        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Fungsi untuk mengubah status is_active (Toggle)
     */
    public function toggleStatus(User $user)
    {
        // Jika tadinya true (1) jadi false (0), dan sebaliknya
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "User {$user->name} berhasil {$status}!");
    }
}