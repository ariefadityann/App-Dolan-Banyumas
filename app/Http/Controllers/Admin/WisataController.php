<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'alamat' => 'required',
            'jam_buka' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        
        // Upload gambar utama
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $validated['gambar_url'] = 'uploads/' . $filename;
        }
        
        // Upload galeri (3 input terpisah)
        $gallery = [];
        
        for ($i = 1; $i <= 3; $i++) {
            if ($request->hasFile("gallery_$i")) {
                $file = $request->file("gallery_$i");
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/gallery'), $filename);
                $gallery[] = 'uploads/gallery/' . $filename;
            }
        }
        
        $validated['images'] = $gallery;
        
        Wisata::create($validated);
        return redirect()->back()->with('success', 'Data wisata berhasil ditambahkan.');
    }
    
    public function update(Request $request, $id)
    {
        $wisata = Wisata::findOrFail($id);
        
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'deskripsi' => 'required',
            'caption' => 'required',
            'alamat' => 'required',
            'jam_buka' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        
        $data = $request->only(['nama', 'kategori', 'deskripsi', 'caption', 'alamat', 'lat', 'lng', 'jarak', 'harga', 'telepon', 'jam_buka']);
        
        // Update Gambar Utama
        if ($request->hasFile('image_file')) {
            if ($wisata->gambar_url && file_exists(public_path($wisata->gambar_url))) {
                @unlink(public_path($wisata->gambar_url));
            }
            
            $file = $request->file('image_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $data['gambar_url'] = 'uploads/' . $filename;
        }
        
        // UPDATE GALERI (LOGIKA SAMA SEPERTI GAMBAR UTAMA)
        $gallery = [];
        
        // Proses 3 input galeri
        for ($i = 1; $i <= 3; $i++) {
            // Jika ada file baru yang diupload
            if ($request->hasFile("gallery_$i")) {
                $file = $request->file("gallery_$i");
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                
                // Pastikan folder ada
                if (!file_exists(public_path('uploads/gallery'))) {
                    mkdir(public_path('uploads/gallery'), 0755, true);
                }
                
                $file->move(public_path('uploads/gallery'), $filename);
                $gallery[] = 'uploads/gallery/' . $filename;
            } 
            // Jika tidak ada file baru, gunakan yang lama (jika ada)
            elseif ($request->has("images_old_$i")) {
                $gallery[] = $request->input("images_old_$i");
            }
        }
        
        // Simpan array galeri (bisa kosong jika semua dihapus)
        $data['images'] = array_values(array_filter($gallery));
        
        $wisata->update($data);
        
        return redirect()->route('wisata.index')->with('success', 'Data berhasil diperbarui!');
    }
    
    public function destroy(Wisata $wisata)
    {
        // Hapus gambar utama
        if ($wisata->gambar_url && file_exists(public_path($wisata->gambar_url))) {
            @unlink(public_path($wisata->gambar_url));
        }
        
        // Hapus galeri
        if ($wisata->images && is_array($wisata->images)) {
            foreach ($wisata->images as $img) {
                if (file_exists(public_path($img))) {
                    @unlink(public_path($img));
                }
            }
        }
        
        $wisata->delete();
        return redirect()->back()->with('success', 'Data wisata berhasil dihapus.');
    }
}