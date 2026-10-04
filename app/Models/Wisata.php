<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nama
 * @property string $kategori
 * @property string $deskripsi
 * @property string|null $caption
 * @property string|null $jarak
 * @property string|null $harga
 * @property string|null $gambar_url
 * @property array|null $images
 * @property string|null $alamat
 * @property string|null $telepon
 * @property string|null $jam_buka
 * @property float|null $lat
 * @property float|null $lng
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Wisata extends Model
{
    protected $fillable = [
        'nama','kategori','deskripsi','caption',
        'jarak','harga','gambar_url','images',
        'alamat','telepon','jam_buka','lat','lng'
    ];

    protected $casts = [
        'images' => 'array'
    ];
}
