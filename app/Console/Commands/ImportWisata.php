<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Wisata;
use Illuminate\Support\Facades\Storage;

class ImportWisata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-wisata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = storage_path('app/wisata.json');

        if (!file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return;
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            $this->error('JSON tidak valid atau bukan array');
            return;
        }

        foreach ($data as $item) {
            Wisata::create([
                'nama'       => $item['nama'],
                'kategori'   => $item['kategori'],
                'deskripsi'  => $item['deskripsi'],
                'caption'    => $item['caption'],
                'jarak'      => $item['jarak'],
                'harga'      => $item['harga'],
                'gambar_url' => $item['gambar_url'],
                'images'     => $item['images'],
                'alamat'     => $item['alamat'],
                'telepon'    => $item['telepon'],
                'jam_buka'   => $item['jam_buka'],
                'lat'        => $item['lat'],
                'lng'        => $item['lng'],
            ]);
        }

        $this->info('Import wisata berhasil!');
    }
}
