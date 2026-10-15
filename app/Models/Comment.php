<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wisata_id',
        'comment',
        'rating',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'wisata_id' => 'integer',
        'rating' => 'integer',
    ];

    /**
     * Relasi ke User pembuat komentar
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Objek Wisata yang dikomentari
     */
    public function wisata()
    {
        return $this->belongsTo(Wisata::class);
    }
}
