@extends('layout.home')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Data Wisata</h1>
        <button data-modal-target="add-modal" data-modal-toggle="add-modal" class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5">
            + Tambah Wisata
        </button>
    </div>

    {{-- STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="p-4 rounded-lg shadow-md bg-blue-600 text-white flex justify-between items-center">
            <div><p class="text-sm opacity-80">Total Wisata</p><p class="text-2xl font-bold">{{ $totalWisata }}</p></div>
            <div class="p-3 bg-white/20 rounded-lg"><i class="fas fa-map-marked-alt text-xl"></i></div>
        </div>
        <div class="p-4 rounded-lg shadow-md bg-emerald-600 text-white flex justify-between items-center">
            <div><p class="text-sm opacity-80">Desa Wisata</p><p class="text-2xl font-bold">{{ $totalDesaWisata }}</p></div>
            <div class="p-3 bg-white/20 rounded-lg"><i class="fas fa-home text-xl"></i></div>
        </div>
        <div class="p-4 rounded-lg shadow-md bg-orange-500 text-white flex justify-between items-center">
            <div><p class="text-sm opacity-80">Kuliner</p><p class="text-2xl font-bold">{{ $totalKuliner }}</p></div>
            <div class="p-3 bg-white/20 rounded-lg"><i class="fas fa-utensils text-xl"></i></div>
        </div>
        <div class="p-4 rounded-lg shadow-md bg-purple-600 text-white flex justify-between items-center">
            <div><p class="text-sm opacity-80">Penginapan</p><p class="text-2xl font-bold">{{ $totalPenginapan }}</p></div>
            <div class="p-3 bg-white/20 rounded-lg"><i class="fas fa-bed text-xl"></i></div>
        </div>
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
                <div class="flex items-start justify-center min-h-screen p-4 pt-20"> {{-- Tambah pt-20 untuk Margin Top --}}
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl p-6">
                        <h3 class="text-xl font-bold mb-4 dark:text-white border-b pb-2">Edit Data: {{ $w->nama }}</h3>
                        <form action="{{ route('wisata.update', $w->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div><label class="block text-sm font-medium dark:text-white">Nama</label><input type="text" name="nama" value="{{ $w->nama }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                                <div><label class="block text-sm font-medium dark:text-white">Kategori</label><input type="text" name="kategori" value="{{ $w->kategori }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                                <div><label class="block text-sm font-medium dark:text-white">Harga</label><input type="text" name="harga" value="{{ $w->harga }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                                
                                {{-- GAMBAR UTAMA EDIT --}}
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium dark:text-white">Gambar Utama</label>
                                    <div class="flex gap-3 items-center mt-1">
                                        <img id="prev-main-{{ $w->id }}" src="{{ asset($w->gambar_url) }}" class="w-20 h-16 object-cover rounded border">
                                        <input type="file" name="image_file" accept="image/*" onchange="previewImage(this, 'prev-main-{{ $w->id }}')" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-400">
                                    </div>
                                    <input type="hidden" name="gambar_url" value="{{ $w->gambar_url }}">
                                </div>

                                <div class="col-span-3"><label class="block text-sm font-medium dark:text-white">Alamat</label><input type="text" name="alamat" value="{{ $w->alamat }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                                
                                {{-- GALERI FOTO EDIT --}}
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium dark:text-white mb-2">Galeri Foto</label>
                                    <div id="edit-images-container-{{ $w->id }}" class="space-y-3">
                                        @if($w->images)
                                            @foreach($w->images as $index => $img)
                                            <div class="flex gap-2 items-center">
                                                <img src="{{ asset($img) }}" class="w-12 h-10 object-cover rounded border">
                                                <span class="text-xs text-gray-500 truncate flex-1 dark:text-gray-400">{{ $img }}</span>
                                                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2">✕</button>
                                                <input type="hidden" name="images_old[]" value="{{ $img }}">
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" onclick="addImageInput('edit-images-container-{{ $w->id }}')" class="mt-2 text-sm text-blue-500 font-semibold">+ Tambah File Galeri</button>
                                </div>

                                <div class="col-span-3"><label class="block text-sm font-medium dark:text-white">Deskripsi</label><input type="text" name="deskripsi" value="{{ $w->deskripsi }}" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
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
    <div class="flex items-start justify-center min-h-screen p-4 pt-20"> {{-- pt-20 Margin Top --}}
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl p-6">
            <h3 class="text-xl font-bold mb-4 dark:text-white border-b pb-2">Tambah Wisata Baru</h3>
            <form action="{{ route('wisata.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div><label class="block text-sm font-medium dark:text-white">Nama</label><input type="text" name="nama" required class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                    <div><label class="block text-sm font-medium dark:text-white">Kategori</label><input type="text" name="kategori" required class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                    <div><label class="block text-sm font-medium dark:text-white">Harga</label><input type="text" name="harga" class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
                    
                    {{-- GAMBAR UTAMA TAMBAH --}}
                    <div class="col-span-3">
                        <label class="block text-sm font-medium dark:text-white">Gambar Utama</label>
                        <div class="flex gap-3 items-center mt-1">
                            <img id="prev-add-main" src="https://via.placeholder.com/150" class="w-20 h-16 object-cover rounded border">
                            <input type="file" name="image_file" accept="image/*" required onchange="previewImage(this, 'prev-add-main')" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-400">
                        </div>
                    </div>

                    <div class="col-span-3"><label class="block text-sm font-medium dark:text-white">Alamat</label><input type="text" name="alamat" required class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>

                    {{-- GALERI FOTO TAMBAH --}}
                    <div class="col-span-3">
                        <label class="block text-sm font-medium dark:text-white mb-2">Galeri Foto</label>
                        <div id="add-images-container" class="space-y-3">
                            </div>
                        <button type="button" onclick="addImageInput('add-images-container')" class="mt-2 text-sm text-blue-500 font-semibold">+ Tambah File Galeri</button>
                    </div>

                    <div class="col-span-3"><label class="block text-sm font-medium dark:text-white">Deskripsi</label><input type="text" name="deskripsi" required class="w-full border rounded-lg p-2 mt-1 dark:bg-gray-700 dark:text-white"></div>
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
    // Fungsi Preview Gambar
    function previewImage(input, targetId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(targetId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Fungsi Tambah Input File Galeri secara Dinamis
    function addImageInput(containerId) {
        const container = document.getElementById(containerId);
        const uniqueId = Date.now();
        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center bg-gray-50 p-2 rounded dark:bg-gray-700';
        div.innerHTML = `
            <img id="prev-gal-${uniqueId}" src="https://via.placeholder.com/80" class="w-12 h-10 object-cover rounded border">
            <input type="file" name="images_files[]" accept="image/*" required onchange="previewImage(this, 'prev-gal-${uniqueId}')" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2 font-bold">✕</button>
        `;
        container.appendChild(div);
    }
</script>
@endsection