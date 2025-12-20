<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Wisata;

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
