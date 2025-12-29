<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Import ini tidak apa-apa, tapi tidak lagi digunakan

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_name',
        'user_email',
        'wisata_name',
        'visit_date',
        'total_tickets',
        'total_price',
        'status',
        'snap_token'
    ];

    /**
     * Cast attributes to proper data types
     */
    protected $casts = [
        'total_tickets' => 'integer',
        'total_price' => 'float',
        'visit_date' => 'date',
    ];

   /*
    * HAPUS ATAU KOMENTARI FUNGSI INI KARENA TIDAK ADA KOLOM 'id_user'
    *
    * public function user()
    * {
    * return $this->belongsTo(User::class, 'id_user'); 
    * }
    */
}