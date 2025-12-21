<?php

namespace App\Http\Controllers\Admin; // Harus ada \Admin karena di dalam folder Admin

use App\Http\Controllers\Controller; // Tambahkan ini agar bisa extend Controller

use App\Models\Wisata;
use Illuminate\Http\Request;

class WisataController extends Controller
{
   public function index(Request $request)
{
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

    return view('pages.data-wisata', compact('wisatas'));
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

    public function update(Request $request, $id)
    {
        $wisata = Wisata::findOrFail($id);

        // Validasi: Hapus 'required' pada field yang tidak ada di Modal Edit
        $validated = $request->validate([
            'nama'      => 'required',
            'kategori'  => 'required',
            'deskripsi' => 'required',
            'caption'   => 'required',
            'alamat'    => 'required',
            'lat'       => 'required|numeric',
            'lng'       => 'required|numeric',
            'jarak'     => 'nullable',
            'harga'     => 'nullable',
            // 'telepon' dan 'jam_buka' tidak divalidasi karena tidak ada di form edit
        ]);

        // Ambil data teks dari form
        $data = $request->only(['nama', 'kategori', 'deskripsi', 'caption', 'alamat', 'lat', 'lng', 'jarak', 'harga']);

        // LOGIKA UPDATE GAMBAR UTAMA
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $data['gambar_url'] = 'uploads/' . $filename;
        }

        // LOGIKA UPDATE GALERI (images)
        // Ambil gambar lama yang tidak dihapus (dari hidden input images_old[])
        $gallery = $request->input('images_old', []); 
        
        // Tambah gambar baru jika ada yang diupload
        if ($request->hasFile('images_files')) {
            foreach ($request->file('images_files') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/gallery'), $filename);
                $gallery[] = 'uploads/gallery/' . $filename;
            }
        }
        $data['images'] = $gallery;

        // EKSEKUSI UPDATE KE DATABASE
        $wisata->update($data);

        return redirect()->route('wisata.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(Wisata $wisata)
    {
        $wisata->delete();
        return redirect()->back()->with('success', 'Data wisata berhasil dihapus.');
    }
}