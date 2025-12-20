<?php

namespace App\Http\Controllers\Admin; // Harus ada \Admin karena di dalam folder Admin

use App\Http\Controllers\Controller; // Tambahkan ini agar bisa extend Controller

use App\Models\Wisata;
use Illuminate\Http\Request;

class WisataController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. LOGIKA STATISTIK DASHBOARD ---
        $totalWisata = Wisata::count();
        $totalDesaWisata = Wisata::where('kategori', 'Desa Wisata')->count();
        $totalKuliner = Wisata::where('kategori', 'Kuliner')->count();
        $totalPenginapan = Wisata::where('kategori', 'Penginapan')->count();

        // --- 2. LOGIKA FILTER & SEARCH ---
        $query = Wisata::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('alamat', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        $wisatas = $query->orderBy('created_at', 'desc')->paginate(6)->withQueryString();

        return view('pages.data-wisata', compact(
            'wisatas', 'totalWisata', 'totalDesaWisata', 'totalKuliner', 'totalPenginapan'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'deskripsi' => 'required',
            'caption' => 'required',
            'jarak' => 'nullable',
            'harga' => 'nullable',
            'gambar_url' => 'required',
            'images' => 'nullable|array',
            'alamat' => 'required',
            'telepon' => 'nullable',
            'jam_buka' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        Wisata::create($validated);
        return redirect()->back()->with('success', 'Data wisata berhasil ditambahkan.');
    }

    public function update(Request $request, Wisata $wisata)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'deskripsi' => 'required',
            'caption' => 'required',
            'jarak' => 'nullable',
            'harga' => 'nullable',
            'gambar_url' => 'required',
            'images' => 'nullable|array',
            'alamat' => 'required',
            'telepon' => 'nullable',
            'jam_buka' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $wisata->update($validated);
        return redirect()->back()->with('success', 'Data wisata berhasil diperbarui.');
    }

    public function destroy(Wisata $wisata)
    {
        $wisata->delete();
        return redirect()->back()->with('success', 'Data wisata berhasil dihapus.');
    }
}