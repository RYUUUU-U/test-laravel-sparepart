<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table      = 'barang_keluar';
    protected $primaryKey = 'id_keluar';
    public    $timestamps  = false;

    protected $fillable = [
        'id_barang',
        'tanggal_keluar',
        'jumlah_keluar',
        'total_harga',
    ];

    /**
     * Relasi ke Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
