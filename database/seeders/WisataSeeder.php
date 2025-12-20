<?php

namespace Database\Seeders;

use App\Models\Wisata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class WisataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data dari file JSON
        $json = File::get(database_path('data/wisata.json'));
        $data = json_decode($json, true);

        foreach ($data as $item) {
            Wisata::create([
                'nama'       => $item['nama'],
                'kategori'   => $item['kategori'],
                'deskripsi'  => $item['deskripsi'],
                'caption'    => $item['caption'],
                'jarak'      => $item['jarak'],
                'harga'      => $item['harga'],
                'alamat'     => $item['alamat'],
                'telepon'    => $item['telepon'] === "N/A" ? null : $item['telepon'],
                'images'     => $item['images'], // Otomatis jadi JSON di DB
                'lat'        => $item['lat'],
                'lng'        => $item['lng'],
                
                // MAPPING: Menghubungkan camelCase JSON ke snake_case DB
                'gambar_url' => $item['gambarUrl'], 
                'jam_buka'   => $item['jamBuka'],
            ]);
        }
    }
}