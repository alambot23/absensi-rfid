<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AdminDosenController extends Controller
{
    public function index()
    {
        $dosen = User::where('role', 'dosen')->latest()->get();
        return Inertia::render('Admin/Dosen', [
            'daftarDosen' => $dosen
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim_nidn' => 'required|string|unique:users,nim_nidn',
            'email' => 'required|string|email|max:255|unique:users,email',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'), 
            'role' => 'dosen',
            'nim_nidn' => $request->nim_nidn,
        ]);

        return redirect()->back()->with('success', 'Data Dosen berhasil ditambahkan!');
    }

    // --- TAMBAHAN BARU: FUNGSI EDIT ---
    public function update(Request $request, $id)
    {
        $dosen = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            // Rule unique diubah agar mengabaikan ID dosen yang sedang diedit
            'nim_nidn' => 'required|string|unique:users,nim_nidn,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ]);

        $dosen->update([
            'name' => $request->name,
            'nim_nidn' => $request->nim_nidn,
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', 'Data Dosen berhasil diperbarui!');
    }

    // --- TAMBAHAN BARU: FUNGSI HAPUS ---
    public function destroy($id)
    {
        $dosen = User::findOrFail($id);
        $dosen->delete();

        return redirect()->back()->with('success', 'Data Dosen berhasil dihapus!');
    }
}