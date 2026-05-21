<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AdminMahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = User::where('role', 'mahasiswa')->latest()->get();
        return Inertia::render('Admin/Mahasiswa', [
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim_nidn' => 'required|string|unique:users,nim_nidn',
            'rfid_uid' => 'required|string|unique:users,rfid_uid', // Wajib unik
        ]);

        User::create([
            'name' => $request->name,
            'email' => strtolower($request->nim_nidn) . '@mahasiswa.ac.id', // Auto generate email
            'password' => Hash::make('password123'), // Password default
            'role' => 'mahasiswa',
            'nim_nidn' => $request->nim_nidn,
            'rfid_uid' => $request->rfid_uid,
        ]);

        return redirect()->back()->with('success', 'Mahasiswa dan Kartu RFID berhasil didaftarkan!');
    }

    // Fungsi Edit Mahasiswa
    public function update(Request $request, $id)
    {
        $mahasiswa = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nim_nidn' => 'required|string|unique:users,nim_nidn,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'rfid_uid' => 'required|string|unique:users,rfid_uid,' . $id,
        ]);

        $mahasiswa->update([
            'name' => $request->name,
            'nim_nidn' => $request->nim_nidn,
            'email' => $request->email,
            'rfid_uid' => $request->rfid_uid,
        ]);

        return redirect()->back()->with('success', 'Data Mahasiswa berhasil diperbarui!');
    }

    // Fungsi Hapus Mahasiswa
    public function destroy($id)
    {
        $mahasiswa = User::findOrFail($id);
        $mahasiswa->delete();

        return redirect()->back()->with('success', 'Data Mahasiswa berhasil dihapus!');
    }
}