@extends('layout.home') {{-- Sesuaikan dengan nama layout Anda --}}

@section('content')

    <div class="flex justify-between items-center mb-4">
        {{-- UBAH JUDUL --}}
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Daftar Admin</h1>

        {{-- UBAH MODAL TARGET --}}
        <button 
            type="button" 
            data-modal-target="tambah-admin-modal" 
            data-modal-toggle="tambah-admin-modal"
            class="inline-flex items-center py-2.5 px-5 text-base font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
            <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
            </svg>
            Tambah Admin Baru {{-- UBAH TEXT --}}
        </button>
    </div>

    {{-- Alert Jika Ada Pesan Sukses --}}
    @if(session('success'))
        <div id="success-alert" 
             class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200" 
             role="alert">
            <span class="font-medium">Berhasil!</span> {{ session('success') }}
        </div>
    @endif

    {{-- Alert Jika Ada Pesan Error (Validasi atau Hapus) --}}
    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200" role="alert">
            <span class="font-medium">Validasi Gagal!</span>
            <ul class="mt-1.5 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- TABEL ADMIN --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">

            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">No</th>
                    <th scope="col" class="px-6 py-3">Username</th>
                    <th scope="col" class="px-6 py-3">Email</th>
                    <th scope="col" class="px-6 py-3">No. WA</th>
                    {{-- <th scope="col" class="px-6 py-3">Role</th> --}} {{-- HILANGKAN ROLE --}}
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                {{-- UBAH VARIABLE LOOP --}}
                @forelse ($admins as $admin)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $admin->username }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $admin->email }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $admin->no_wa }}
                        </td>
                        
                        {{-- HILANGKAN KOLOM ROLE --}}
                        {{-- <td class="px-6 py-4"> ... </span> </td> --}}

                        {{-- UBAH ROUTE & MODAL TARGET --}}
                        <td class="px-6 py-4">
                            <button type="button" 
                                    data-modal-target="edit-admin-modal-{{ $admin->id }}" 
                                    data-modal-toggle="edit-admin-modal-{{ $admin->id }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                Edit
                            </button>
                            
                            <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus admin ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline ms-3">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- ============================================= --}}
                    {{-- MODAL EDIT ADMIN (Di dalam loop)            --}}
                    {{-- ============================================= --}}
                    {{-- UBAH ID, MODAL-HIDE, DAN ROUTE --}}
                    <div id="edit-admin-modal-{{ $admin->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full">
                        <div class="relative p-4 w-full max-w-lg max-h-full">
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Admin: {{ $admin->username }}</h3>
                                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-admin-modal-{{ $admin->id }}">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                                        <span class="sr-only">Tutup modal</span>
                                    </button>
                                </div>
                                <form action="{{ route('admins.update', $admin->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="p-4 md:p-5 grid grid-cols-2 gap-4">
                                        <div class="col-span-1">
                                            <label for="username-edit-{{ $admin->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                                            <input type="text" name="username" id="username-edit-{{ $admin->id }}" value="{{ old('username', $admin->username) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                                        </div>
                                        <div class="col-span-1">
                                            <label for="email-edit-{{ $admin->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                            <input type="email" name="email" id="email-edit-{{ $admin->id }}" value="{{ old('email', $admin->email) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                                        </div>
                                        <div class="col-span-2"> {{-- Buat jadi full-width --}}
                                            <label for="no_wa-edit-{{ $admin->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. WA</UBAHlabel>
                                            <input type="text" name="no_wa" id="no_wa-edit-{{ $admin->id }}" value="{{ old('no_wa', $admin->no_wa) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                                        </div>
                                        
                                        {{-- HILANGKAN FIELD ROLE --}}
                                        {{-- <div class="col-span-1"> ... </select> </div> --}}

                                        <hr class="col-span-2 my-2 border-gray-200 dark:border-gray-600">
                                        <div class="col-span-1">
                                            <label for="password-edit-{{ $admin->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password Baru</label>
                                            <input type="password" name="password" id="password-edit-{{ $admin->id }}" placeholder="Kosongkan jika tidak ganti" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                        </div>
                                         <div class="col-span-1">
                                            <label for="password_confirmation-edit-{{ $admin->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi Password</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation-edit-{{ $admin->id }}" placeholder="Ulangi password baru" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update Data</button>
                                        <button type="button" data-modal-hide="edit-admin-modal-{{ $admin->id }}" class="ms-3 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        {{-- UBAH COLSPAN KARENA KOLOM ROLE HILANG --}}
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada admin terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>


    {{-- ============================================= --}}
    {{-- MODAL TAMBAH ADMIN BARU (Di luar loop)      --}}
    {{-- ============================================= --}}
    {{-- UBAH ID, MODAL-HIDE, DAN ROUTE --}}
    <div id="tambah-admin-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full">
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Tambah Admin Baru</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="tambah-admin-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                        <span class="sr-only">Tutup modal</span>
                    </button>
                </div>
                <form action="{{ route('admins.store') }}" method="POST">
                    @csrf
                    <div class="p-4 md:p-5 grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <label for="username-tambah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                            <input type="text" name="username" id="username-tambah" value="{{ old('username') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                        </div>
                        <div class="col-span-1">
                            <label for="email-tambah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                            <input type="email" name="email" id="email-tambah" value="{{ old('email') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                        </div>
                        <div class="col-span-2"> {{-- Buat jadi full-width --}}
                            <label for="no_wa-tambah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. WA</label>
                            <input type="text" name="no_wa" id="no_wa-tambah" value="{{ old('no_wa') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                        </div>
                        
                        {{-- HILANGKAN FIELD ROLE --}}
                        {{-- <div class="col-span-1"> ... </select> </div> --}}
                        
                        <hr class="col-span-2 my-2 border-gray-200 dark:border-gray-600">
                        <div class="col-span-1">
                            <label for="password-tambah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input type="password" name="password" id="password-tambah" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                        </div>
                         <div class="col-span-1">
                            <label for="password_confirmation-tambah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation-tambah" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                        </div>
                    </div>
                    <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan Admin</Monitor>
                        <button type="button" data-modal-hide="tambah-admin-modal" class="ms-3 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Hilangkan alert sukses setelah 3 detik --}}
    <script>
        const successAlert = document.getElementById("success-alert");
        if (successAlert) {
            setTimeout(() => successAlert.classList.add("hidden"), 3000);
        }
    </script>

@endsection