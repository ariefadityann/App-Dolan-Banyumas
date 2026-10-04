<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // <-- Import Hash
use Illuminate\Validation\Rule;       // <-- Import Rule

class UserController extends Controller
{
    /**
     * Menampilkan halaman daftar pengguna (KHUSUS ROLE USER).
     */
    public function index()
    {
        // --- PERUBAHAN: Filter hanya role 'user' ---
        $users = User::select('id', 'username', 'email', 'no_wa', 'role')
                     ->where('role', 'user') // <--- Hanya tampilkan user biasa
                     ->orderBy('email')
                     ->get();

        return view('pages.data-user', compact('users')); 
    }

    /**
     * Menyimpan pengguna baru (OTOMATIS JADI USER).
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'no_wa' => 'required|string|max:20',
            // Role kita hapus dari validasi input, karena kita set otomatis di bawah
            'password' => 'required|string|min:8|confirmed',
        ]);

        // --- PERUBAHAN: Set role otomatis jadi 'user' ---
        $validatedData['role'] = 'user';

        // Hash Password
        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        return redirect()->route('pages.data-user')->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Mengupdate data pengguna.
     */
    public function update(Request $request, User $user)
    {
        // Keamanan: Pastikan yang diedit adalah role 'user' (bukan admin)
        if ($user->role !== 'user') {
            return redirect()->route('pages.data-user')
                ->withErrors(['error' => 'Anda hanya dapat mengedit data User di halaman ini.']);
        }

        $validatedData = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'no_wa' => 'required|string|max:20',
            // Role tidak diizinkan diubah lewat form ini
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // --- Logika Update Password ---
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // Pastikan role tidak berubah (tetap user)
        unset($validatedData['role']);

        $user->update($validatedData);

        return redirect()->route('pages.data-user')->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Menghapus data pengguna.
     */
    public function destroy(User $user)
    {
        // Keamanan: Pastikan yang dihapus adalah role 'user'
        if ($user->role !== 'user') {
            return redirect()->route('pages.data-user')
                ->withErrors(['error' => 'Anda hanya dapat menghapus data User di halaman ini.']);
        }
        
        // Cek agar user tidak bisa hapus diri sendiri (jika diperlukan)
        if (auth()->id() == $user->id) {
             return redirect()->route('pages.data-user')
                ->withErrors(['error' => 'Gagal! Anda tidak dapat menghapus akun Anda sendiri.']);
        }


        $user->delete();
        
        return redirect()->route('pages.data-user')->with('success', 'Data user berhasil dihapus.');
    }
}