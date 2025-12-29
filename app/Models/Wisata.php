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

    /**
     * Append these attributes to JSON responses
     */
    protected $appends = ['image_url', 'images_urls'];

    /**
     * Get the full URL for the main image
     * Handles both old format (images/filename.jpg) and new format (uploads/filename.jpg)
     */
   public function getImageUrlAttribute()
{
    if (empty($this->gambar_url)) return null;
    // Gunakan path asli dari database tanpa menambah prefix 'images/'
    return url(ltrim($this->gambar_url, '/'));
}

public function getImagesUrlsAttribute()
{
    if (empty($this->images) || !is_array($this->images)) return [];
    return array_map(function($imagePath) {
        if (empty($imagePath)) return null;
        return url(ltrim($imagePath, '/'));
    }, $this->images);
}
}
