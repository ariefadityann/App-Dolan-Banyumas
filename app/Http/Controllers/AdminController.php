<?php

namespace App\Http\Controllers;

use App\Models\User; // <-- Tetap menggunakan Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Menampilkan halaman daftar admin.
     */
    public function index()
    {
        // Ambil user, TAPI HANYA YANG ROLE-NYA ADMIN
        $admins = User::select('id', 'username', 'email', 'no_wa', 'role')
                       ->where('role', 'admin') // <-- FILTER UTAMA
                       ->orderBy('email')
                       ->get();

        // Menggunakan view baru 'pages.data-admin'
        return view('pages.data-admin', compact('admins'));
    }

    /**
     * Menyimpan admin baru.
     */
    public function store(Request $request)
    {
        // Validasi data (tanpa 'role', karena akan di-set otomatis)
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'no_wa' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // HASH PASSWORD
        $validatedData['password'] = Hash::make($validatedData['password']);
        
        // SET ROLE OTOMATIS KE 'ADMIN'
        $validatedData['role'] = 'admin';

        User::create($validatedData);

        // Redirect ke route 'admins.index'
        return redirect()->route('pages.data-admin')->with('success', 'Admin baru berhasil ditambahkan.');
    }

    /**
     * Mengupdate data admin.
     * * Catatan: $admin adalah model User, tapi kita pastikan dia admin.
     */
    public function update(Request $request, User $admin)
    {
        // Pastikan user yang di-edit adalah admin
        if ($admin->role !== 'admin') {
            return redirect()->route('pages.data-admin')->withErrors(['error' => 'User ini bukan admin.']);
        }

        $validatedData = $request->validate([
            'username' => [
                'required', 'string', 'max:255',
                Rule::unique('users')->ignore($admin->id),
            ],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users')->ignore($admin->id),
            ],
            'no_wa' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            // 'role' tidak di-update dari sini, akan tetap 'admin'
        ]);

        // Logika Update Password
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // Pastikan 'role' tidak diubah
        unset($validatedData['role']);

        $admin->update($validatedData);

        return redirect()->route('pages.data-admin')->with('success', 'Data admin berhasil diperbarui.');
    }

    /**
     * Menghapus data admin.
     */
    public function destroy(User $admin)
    {
        // Pastikan user yang dihapus adalah admin
        if ($admin->role !== 'admin') {
            return redirect()->route('pages.data-admin')->withErrors(['error' => 'User ini bukan admin.']);
        }

        // Mencegah admin menghapus diri sendiri
        if (auth()->id() == $admin->id) {
            return redirect()->route('pages.data-admin')
                ->withErrors(['error' => 'Gagal! Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $admin->delete();
        
        return redirect()->route('pages.data-admin')->with('success', 'Data admin berhasil dihapus.');
    }
}