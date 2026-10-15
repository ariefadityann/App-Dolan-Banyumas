@extends('layout.home')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Data Komentar & Ulasan Wisata</h1>

        {{-- ALERT NOTIFIKASI --}}
        @if (session('success'))
            <div id="success-alert" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200" role="alert">
                <span class="font-medium">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        {{-- === KARTU STATISTIK KOMENTAR WISATA === --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- KARTU 1: Total Komentar --}}
            <div class="p-4 rounded-lg shadow-md bg-blue-500 text-white flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium opacity-80">Total Ulasan Masuk</p>
                    <p class="text-2xl font-bold">{{ $totalKomentar }} <span class="text-sm font-normal">Ulasan</span></p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.502 49.19 49.19 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v7.518Z" />
                    </svg>
                </div>
            </div>

            {{-- KARTU 2: Rata-Rata Rating --}}
            <div class="p-4 rounded-lg shadow-md bg-amber-500 text-white flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium opacity-80">Rata-Rata Rating</p>
                    <p class="text-2xl font-bold">⭐ {{ $rataRataRating }} <span class="text-sm font-normal">/ 5.0</span></p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.398 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.353c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                    </svg>
                </div>
            </div>

            {{-- KARTU 3: Bintang 5 --}}
            <div class="p-4 rounded-lg shadow-md bg-green-500 text-white flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium opacity-80">Rating Bintang 5</p>
                    <p class="text-2xl font-bold">{{ $totalBintang5 }} <span class="text-sm font-normal">Ulasan</span></p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>

            {{-- KARTU 4: Komentar Hari Ini --}}
            <div class="p-4 rounded-lg shadow-md bg-purple-500 text-white flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium opacity-80">Komentar Hari Ini</p>
                    <p class="text-2xl font-bold">{{ $komentarHariIni }} <span class="text-sm font-normal">Ulasan</span></p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- FORM FILTER & PENCARIAN --}}
        <form action="{{ route('komentar.index') }}" method="GET" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                {{-- Search --}}
                <div class="lg:col-span-4">
                    <label for="search" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cari Komentar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                               placeholder="Cari user, wisata, atau isi teks...">
                    </div>
                </div>

                {{-- Filter Wisata --}}
                <div class="lg:col-span-3">
                    <label for="wisata_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Destinasi Wisata</label>
                    <select name="wisata_id" id="wisata_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Wisata</option>
                        @foreach ($wisataList as $w)
                            <option value="{{ $w->id }}" {{ request('wisata_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Rating --}}
                <div class="lg:col-span-2">
                    <label for="rating" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rating</label>
                    <select name="rating" id="rating" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Rating</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4)</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ (3)</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐ (2)</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ (1)</option>
                    </select>
                </div>

                {{-- Tombol Aksi --}}
                <div class="lg:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2.5 text-center">
                        Filter
                    </button>
                    <a href="{{ route('komentar.index') }}" class="text-gray-700 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center dark:bg-gray-700 dark:text-gray-300">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- TABEL DATA KOMENTAR --}}
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">User Pengirim</th>
                        <th scope="col" class="px-6 py-3">Destinasi Wisata</th>
                        <th scope="col" class="px-6 py-3 text-center">Rating</th>
                        <th scope="col" class="px-6 py-3">Isi Komentar / Ulasan</th>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comments as $index => $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $comments->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $item->user->username ?? 'User Dihapus' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->user->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded dark:bg-blue-900 dark:text-blue-300">
                                    {{ $item->wisata->nama ?? 'Wisata Dihapus' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center text-amber-500 font-bold">
                                    <span>⭐ {{ $item->rating }}</span>
                                    <span class="text-xs text-gray-400 font-normal ml-1">/5</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-xs text-gray-900 dark:text-gray-200">
                                <p class="line-clamp-2" title="{{ $item->comment }}">{{ $item->comment }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs whitespace-nowrap">
                                <div>{{ $item->created_at ? $item->created_at->format('d M Y, H:i') : '-' }}</div>
                                <div class="text-gray-400">{{ $item->created_at ? $item->created_at->diffForHumans() : '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <button type="button" data-modal-target="delete-modal-{{ $item->id }}" data-modal-toggle="delete-modal-{{ $item->id }}"
                                        class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                    Hapus
                                </button>
                            </td>
                        </tr>

                        {{-- MODAL HAPUS KOMENTAR --}}
                        <div id="delete-modal-{{ $item->id }}" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative w-full max-w-md max-h-full">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="delete-modal-{{ $item->id }}">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                    </button>
                                    <div class="p-4 md:p-5 text-center">
                                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                        </svg>
                                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                                            Yakin ingin menghapus komentar dari <strong>"{{ $item->user->username ?? 'User' }}"</strong> untuk wisata <strong>"{{ $item->wisata->nama ?? '' }}"</strong>?
                                        </h3>
                                        <form action="{{ route('komentar.destroy', $item->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                                                Ya, Hapus
                                            </button>
                                        </form>
                                        <button data-modal-hide="delete-modal-{{ $item->id }}" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                Belum ada komentar / ulasan wisata yang masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $comments->links() }}
        </div>
    </div>

    <script>
        const successAlert = document.getElementById("success-alert");
        if (successAlert) {
            setTimeout(() => successAlert.classList.add("hidden"), 3000);
        }
    </script>
@endsection
