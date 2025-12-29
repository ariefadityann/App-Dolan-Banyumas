@extends('layout.home')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Data Wisata</h1>
        <button data-modal-target="add-modal" data-modal-toggle="add-modal" class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5">
            + Tambah Wisata
        </button>
    </div>

    {{-- FILTER --}}
    <form action="{{ route('wisata.index') }}" method="GET" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="lg:col-span-6">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cari Wisata</label>
                <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 border border-gray-300 text-sm rounded-lg w-full p-2.5 dark:bg-gray-700 dark:text-white" placeholder="Nama wisata atau alamat...">
            </div>
            <div class="lg:col-span-4">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori</label>
                <select name="kategori" class="bg-gray-50 border border-gray-300 text-sm rounded-lg w-full p-2.5 dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Kategori</option>
                    <option value="Desa Wisata" {{ request('kategori') == 'Desa Wisata' ? 'selected' : '' }}>Desa Wisata</option>
                    <option value="Kuliner" {{ request('kategori') == 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
                    <option value="Penginapan" {{ request('kategori') == 'Penginapan' ? 'selected' : '' }}>Penginapan</option>
                    <option value="Oleh-Oleh" {{ request('kategori') == 'Oleh-Oleh' ? 'selected' : '' }}>Oleh-Oleh</option>
                    <option value="Wisata" {{ request('kategori') == 'Wisata' ? 'selected' : '' }}>Wisata Umum</option>
                </select>
            </div>
            <div class="lg:col-span-2 flex gap-2">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 px-5 py-2.5 rounded-lg text-sm w-full">Filter</button>
            </div>
        </div>
    </form>
</div>

{{-- TABEL --}}
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3">Gambar</th>
                <th class="px-6 py-3">Nama Wisata</th>
                <th class="px-6 py-3">Kategori</th>
                <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($wisatas as $w)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4">
                    <img src="{{ asset($w->gambar_url) }}" class="w-16 h-12 object-cover rounded shadow">
                </td>
                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $w->nama }}</td>
                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $w->kategori }}</td>
                <td class="px-6 py-4 text-center">
                    <button data-modal-target="edit-modal-{{ $w->id }}" data-modal-toggle="edit-modal-{{ $w->id }}" class="text-blue-600 hover:underline mr-2">Edit</button>
                    <form action="{{ route('wisata.destroy', $w->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus data ini?')" class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>

            {{-- MODAL EDIT --}}
            <div id="edit-modal-{{ $w->id }}" tabindex="-1" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50">
                <div class="flex items-start justify-center min-h-screen p-4 pt-80">
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl p-6 pt-8">
                        <h3 class="text-xl font-bold mb-4 dark:text-white border-b pb-2">Edit Data: {{ $w->nama }}</h3>
                        <form action="{{ route('wisata.update', $w->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div><label class="block text-sm font-medium dark:text-white">Nama</label><input type="text" name="nama" value="{{ $w->nama }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                                <div>
                                    <label class="block text-sm font-medium dark:text-white">Kategori</label>
                                    <select name="kategori" id="edit-kategori-{{ $w->id }}" required 
                                        onchange="updateOptions('edit-kategori-{{ $w->id }}', 'edit-deskripsi-{{ $w->id }}')"
                                        class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white">
                                        <option value="Desa Wisata" {{ $w->kategori == 'Desa Wisata' ? 'selected' : '' }}>Desa Wisata</option>
                                        <option value="Wisata" {{ $w->kategori == 'Wisata' ? 'selected' : '' }}>Wisata</option>
                                        <option value="Kuliner" {{ $w->kategori == 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
                                        <option value="Oleh-Oleh" {{ $w->kategori == 'Oleh-Oleh' ? 'selected' : '' }}>Oleh-Oleh</option>
                                        <option value="Penginapan" {{ $w->kategori == 'Penginapan' ? 'selected' : '' }}>Penginapan</option>
                                    </select>
                                </div>
                                <div><label class="block text-sm font-medium dark:text-white">Harga</label><input type="text" name="harga" value="{{ $w->harga }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                                
                                <div><label class="block text-sm font-medium dark:text-white">Telepon</label><input type="text" name="telepon" value="{{ $w->telepon }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                                <div><label class="block text-sm font-medium dark:text-white">Jam Buka</label><input type="text" name="jam_buka" value="{{ $w->jam_buka }}" required class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                                <div>
                                    <label class="block text-sm font-medium dark:text-white">Deskripsi</label>
                                    <select name="deskripsi" id="edit-deskripsi-{{ $w->id }}" required data-selected="{{ $w->deskripsi }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></select>
                                </div>

                                <div class="col-span-3">
                                    <label class="block text-sm font-medium dark:text-white">Gambar Utama</label>
                                    <div class="flex gap-3 items-center mt-1">
                                        <img id="prev-main-{{ $w->id }}" src="{{ asset($w->gambar_url) }}" class="w-20 h-16 object-cover rounded border">
                                        <input type="file" name="image_file" accept="image/*" onchange="previewImage(this, 'prev-main-{{ $w->id }}')" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 dark:text-gray-400">
                                    </div>
                                </div>

                                <div class="col-span-3"><label class="block text-sm font-medium dark:text-white">Alamat</label><input type="text" name="alamat" value="{{ $w->alamat }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                                
                                {{-- GALERI FOTO - EDIT (3 INPUT TERPISAH) --}}
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium dark:text-white mb-2">Galeri Foto (Maksimal 3)</label>
                                    
                                    {{-- Galeri 1 --}}
                                    <div class="mb-3 p-3 bg-gray-50 rounded dark:bg-gray-700">
                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Galeri 1</label>
                                        <div class="flex gap-3 items-center">
                                            @if(isset($w->images[0]))
                                                <img id="prev-gal1-{{ $w->id }}" src="{{ asset($w->images[0]) }}" class="w-16 h-14 object-cover rounded border">
                                                <input type="hidden" name="images_old_1" value="{{ $w->images[0] }}">
                                            @else
                                                <img id="prev-gal1-{{ $w->id }}" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='70'%3E%3Crect fill='%23e5e7eb' width='80' height='70'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='12' x='50%25' y='50%25' text-anchor='middle' dy='.3em'%3EKosong%3C/text%3E%3C/svg%3E" class="w-16 h-14 object-cover rounded border">
                                            @endif
                                            <input type="file" name="gallery_1" accept="image/*" 
                                                   onchange="previewImage(this, 'prev-gal1-{{ $w->id }}')" 
                                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-400">
                                        </div>
                                    </div>

                                    {{-- Galeri 2 --}}
                                    <div class="mb-3 p-3 bg-gray-50 rounded dark:bg-gray-700">
                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Galeri 2</label>
                                        <div class="flex gap-3 items-center">
                                            @if(isset($w->images[1]))
                                                <img id="prev-gal2-{{ $w->id }}" src="{{ asset($w->images[1]) }}" class="w-16 h-14 object-cover rounded border">
                                                <input type="hidden" name="images_old_2" value="{{ $w->images[1] }}">
                                            @else
                                                <img id="prev-gal2-{{ $w->id }}" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='70'%3E%3Crect fill='%23e5e7eb' width='80' height='70'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='12' x='50%25' y='50%25' text-anchor='middle' dy='.3em'%3EKosong%3C/text%3E%3C/svg%3E" class="w-16 h-14 object-cover rounded border">
                                            @endif
                                            <input type="file" name="gallery_2" accept="image/*" 
                                                   onchange="previewImage(this, 'prev-gal2-{{ $w->id }}')" 
                                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-400">
                                        </div>
                                    </div>

                                    {{-- Galeri 3 --}}
                                    <div class="mb-3 p-3 bg-gray-50 rounded dark:bg-gray-700">
                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Galeri 3</label>
                                        <div class="flex gap-3 items-center">
                                            @if(isset($w->images[2]))
                                                <img id="prev-gal3-{{ $w->id }}" src="{{ asset($w->images[2]) }}" class="w-16 h-14 object-cover rounded border">
                                                <input type="hidden" name="images_old_3" value="{{ $w->images[2] }}">
                                            @else
                                                <img id="prev-gal3-{{ $w->id }}" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='70'%3E%3Crect fill='%23e5e7eb' width='80' height='70'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='12' x='50%25' y='50%25' text-anchor='middle' dy='.3em'%3EKosong%3C/text%3E%3C/svg%3E" class="w-16 h-14 object-cover rounded border">
                                            @endif
                                            <input type="file" name="gallery_3" accept="image/*" 
                                                   onchange="previewImage(this, 'prev-gal3-{{ $w->id }}')" 
                                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-400">
                                        </div>
                                    </div>
                                    
                                    <p class="text-xs text-gray-500 italic dark:text-gray-400">*Kosongkan jika tidak ingin mengubah gambar</p>
                                </div>

                                <div class="col-span-3"><label class="block text-sm font-medium dark:text-white">Caption</label><textarea name="caption" rows="2" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white">{{ $w->caption }}</textarea></div>
                                
                                <div><label class="block text-sm font-medium dark:text-white">Lat</label><input type="text" name="lat" value="{{ $w->lat }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                                <div><label class="block text-sm font-medium dark:text-white">Lng</label><input type="text" name="lng" value="{{ $w->lng }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                                <div><label class="block text-sm font-medium dark:text-white">Jarak</label><input type="text" name="jarak" value="{{ $w->jarak }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                            </div>
                            <div class="flex justify-end mt-6 gap-2">
                                <button type="button" data-modal-hide="edit-modal-{{ $w->id }}" class="bg-gray-200 px-5 py-2 rounded-lg text-sm">Batal</button>
                                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm">Update Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

{{-- MODAL TAMBAH --}}
<div id="add-modal" tabindex="-1" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50">
    <div class="flex items-start justify-center min-h-screen p-4 pt-20">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl p-6">
            <h3 class="text-xl font-bold mb-4 dark:text-white border-b pb-2">Tambah Wisata Baru</h3>
            <form action="{{ route('wisata.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div><label class="block text-sm font-medium dark:text-white">Nama</label><input type="text" name="nama" required class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                    <div>
                        <label class="block text-sm font-medium dark:text-white">Kategori</label>
                        <select name="kategori" id="add-kategori" required onchange="updateOptions('add-kategori', 'add-deskripsi')"
                            class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white">
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <option value="Desa Wisata">Desa Wisata</option>
                            <option value="Wisata">Wisata</option>
                            <option value="Kuliner">Kuliner</option>
                            <option value="Oleh-Oleh">Oleh-Oleh</option>
                            <option value="Penginapan">Penginapan</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium dark:text-white">Harga</label><input type="text" name="harga" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                    
                    <div><label class="block text-sm font-medium dark:text-white">Telepon</label><input type="text" name="telepon" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                    <div><label class="block text-sm font-medium dark:text-white">Jam Buka</label><input type="text" name="jam_buka" required placeholder="Contoh: 08:00 - 17:00" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                    <div>
                        <label class="block text-sm font-medium dark:text-white">Deskripsi</label>
                        <select name="deskripsi" id="add-deskripsi" required class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white">
                            <option value="">-- Pilih Kategori Dahulu --</option>
                        </select>
                    </div>

                    <div class="col-span-3">
                        <label class="block text-sm font-medium dark:text-white">Gambar Utama</label>
                        <div class="flex gap-3 items-center mt-1">
                            <img id="prev-add-main" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='120'%3E%3Crect fill='%23ddd' width='150' height='120'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='14' x='50%25' y='50%25' text-anchor='middle' dy='.3em'%3ENo Image%3C/text%3E%3C/svg%3E" class="w-20 h-16 object-cover rounded border">
                            <input type="file" name="image_file" accept="image/*" required onchange="previewImage(this, 'prev-add-main')" class="block w-full text-sm text-gray-500 file:mr-4 dark:text-gray-400">
                        </div>
                    </div>

                    <div class="col-span-3"><label class="block text-sm font-medium dark:text-white">Alamat</label><input type="text" name="alamat" required class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>

                    {{-- GALERI FOTO - TAMBAH (3 INPUT TERPISAH) --}}
                    <div class="col-span-3">
                        <label class="block text-sm font-medium dark:text-white mb-2">Galeri Foto (Opsional, Maks 3)</label>
                        
                        {{-- Galeri 1 --}}
                        <div class="mb-3 p-3 bg-gray-50 rounded dark:bg-gray-700">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Galeri 1</label>
                            <div class="flex gap-3 items-center">
                                <img id="prev-add-gal1" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='70'%3E%3Crect fill='%23e5e7eb' width='80' height='70'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='12' x='50%25' y='50%25' text-anchor='middle' dy='.3em'%3EKosong%3C/text%3E%3C/svg%3E" class="w-16 h-14 object-cover rounded border">
                                <input type="file" name="gallery_1" accept="image/*" 
                                       onchange="previewImage(this, 'prev-add-gal1')" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-400">
                            </div>
                        </div>

                        {{-- Galeri 2 --}}
                        <div class="mb-3 p-3 bg-gray-50 rounded dark:bg-gray-700">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Galeri 2</label>
                            <div class="flex gap-3 items-center">
                                <img id="prev-add-gal2" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='70'%3E%3Crect fill='%23e5e7eb' width='80' height='70'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='12' x='50%25' y='50%25' text-anchor='middle' dy='.3em'%3EKosong%3C/text%3E%3C/svg%3E" class="w-16 h-14 object-cover rounded border">
                                <input type="file" name="gallery_2" accept="image/*" 
                                       onchange="previewImage(this, 'prev-add-gal2')" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-400">
                            </div>
                        </div>

                        {{-- Galeri 3 --}}
                        <div class="mb-3 p-3 bg-gray-50 rounded dark:bg-gray-700">
                            <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Galeri 3</label>
                            <div class="flex gap-3 items-center">
                                <img id="prev-add-gal3" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='70'%3E%3Crect fill='%23e5e7eb' width='80' height='70'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='12' x='50%25' y='50%25' text-anchor='middle' dy='.3em'%3EKosong%3C/text%3E%3C/svg%3E" class="w-16 h-14 object-cover rounded border">
                                <input type="file" name="gallery_3" accept="image/*" 
                                       onchange="previewImage(this, 'prev-add-gal3')" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-400">
                            </div>
                        </div>
                    </div>

                    <div class="col-span-3"><label class="block text-sm font-medium dark:text-white">Caption</label><textarea name="caption" rows="2" required class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></textarea></div>
                    
                    <div><label class="block text-sm font-medium dark:text-white">Lat</label><input type="text" name="lat" required class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                    <div><label class="block text-sm font-medium dark:text-white">Lng</label><input type="text" name="lng" required class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                    <div><label class="block text-sm font-medium dark:text-white">Jarak</label><input type="text" name="jarak" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                </div>
                <div class="flex justify-end mt-6 gap-2">
                    <button type="button" data-modal-hide="add-modal" class="bg-gray-200 px-5 py-2 rounded-lg text-sm">Batal</button>
                    <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-lg text-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="mt-4">{{ $wisatas->links() }}</div>

<script>
const originalSrcs = {};

function previewImage(input, targetId) {
    const preview = document.getElementById(targetId);
    if (!originalSrcs[targetId]) originalSrcs[targetId] = preview.src;

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => preview.src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = originalSrcs[targetId];
    }
}

function updateOptions(katId, deskId) {
    const kategori = document.getElementById(katId).value;
    const deskSelect = document.getElementById(deskId);
    const selectedOld = deskSelect.getAttribute('data-selected');
    
    deskSelect.innerHTML = '';
    let options = [];

    if (kategori === "Desa Wisata") options = ["Desa Wisata"];
    else if (kategori === "Wisata") options = ["Wisata Alam", "Wisata Buatan"];
    else if (kategori === "Kuliner") options = ["Kafe", "Resto"];
    else if (kategori === "Oleh-Oleh") options = ["Pakaian", "Makanan"];
    else if (kategori === "Penginapan") options = ["Hotel"];

    options.forEach(val => {
        const opt = document.createElement('option');
        opt.value = val;
        opt.textContent = val;
        if (val === selectedOld) opt.selected = true;
        deskSelect.appendChild(opt);
    });
}

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Update dropdown deskripsi di modal edit
    document.querySelectorAll('[id^="edit-kategori-"]').forEach(select => {
        const id = select.id.replace('edit-kategori-', '');
        updateOptions(select.id, 'edit-deskripsi-' + id);
    });
});
</script>
@endsection