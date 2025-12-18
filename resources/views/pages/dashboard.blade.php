@extends('layout.home') 

@section('content')
    
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Admin</h1>
        <p class="text-gray-500 dark:text-gray-400">Ringkasan data transaksi wisata dan parkir.</p>
    </div>

    {{-- === BAGIAN 1: KARTU STATISTIK (WARNA-WARNI) === --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        {{-- Kartu Biru: Total Transaksi --}}
        <div class="p-5 rounded-xl bg-blue-500 text-white shadow-lg flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-blue-100 mb-1">Total Transaksi</p>
                <h3 class="text-3xl font-bold">{{ $totalTransaksi }}</h3>
            </div>
            <div class="p-3 bg-white/20 rounded-lg">
                {{-- Icon Dokumen/List --}}
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
        </div>

        {{-- Kartu Hijau: Total Terbayar (Sukses) --}}
        <div class="p-5 rounded-xl bg-green-500 text-white shadow-lg flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-green-100 mb-1">Total Terbayar</p>
                <h3 class="text-3xl font-bold">{{ $totalSukses }}</h3>
            </div>
            <div class="p-3 bg-white/20 rounded-lg">
                {{-- Icon User/Orang --}}
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
        </div>

        {{-- Kartu Ungu: Total Pending --}}
        <div class="p-5 rounded-xl bg-purple-600 text-white shadow-lg flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-purple-100 mb-1">Total Pending</p>
                <h3 class="text-3xl font-bold">{{ $totalPending }}</h3>
            </div>
            <div class="p-3 bg-white/20 rounded-lg">
                {{-- Icon Calendar/Jam --}}
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        {{-- Kartu Orange: Total Batal --}}
        <div class="p-5 rounded-xl bg-orange-500 text-white shadow-lg flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-orange-100 mb-1">Total Batal</p>
                <h3 class="text-3xl font-bold">{{ $totalBatal }}</h3>
            </div>
            <div class="p-3 bg-white/20 rounded-lg">
                {{-- Icon X / Batal --}}
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
        </div>
    </div>


    {{-- === BAGIAN 2: TABEL TRANSAKSI TERBARU (KIRI & KANAN) === --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- TABEL KIRI: Transaksi Wisata (4 Terbaru) --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </span>
                    Transaksi Wisata Terbaru
                </h2>
                <a href="{{ route('transaksi.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua →</a>
            </div>
            
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-3 py-2">Order ID</th>
                            <th scope="col" class="px-3 py-2">User</th>
                            <th scope="col" class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestTransactions as $trx)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $trx->order_id }}</td>
                                <td class="px-3 py-2">{{ Str::limit($trx->user_name, 10) }}</td>
                                <td class="px-3 py-2">
                                    @if($trx->status == 'sukses' || $trx->status == 'success')
                                        <span class="text-green-600 font-semibold">Sukses</span>
                                    @elseif($trx->status == 'pending')
                                        <span class="text-yellow-600 font-semibold">Pending</span>
                                    @else
                                        <span class="text-red-600 font-semibold">Batal</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL KANAN: Booking Parkir (4 Terbaru) --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="p-2 bg-purple-100 text-purple-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </span>
                    Parkir Terbaru
                </h2>
                <a href="{{ route('parkir.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua →</a>
            </div>

            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-3 py-2">Plat Nomor</th>
                            <th scope="col" class="px-3 py-2">Tipe</th>
                            <th scope="col" class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestParkings as $park)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $park->plat_nomor }}</td>
                                <td class="px-3 py-2">{{ $park->parking_type }}</td>
                                <td class="px-3 py-2">
                                    @if($park->status == 'sukses' || $park->status == 'success')
                                        <span class="text-green-600 font-semibold">Sukses</span>
                                    @elseif($park->status == 'pending')
                                        <span class="text-yellow-600 font-semibold">Pending</span>
                                    @else
                                        <span class="text-red-600 font-semibold">Batal</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

@endsection