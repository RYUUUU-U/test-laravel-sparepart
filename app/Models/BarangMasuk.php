<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table      = 'barang_masuk';
    protected $primaryKey = 'id_masuk';
    public    $timestamps  = false;

    protected $fillable = [
        'id_barang',
        'id_supplier',
        'tanggal_masuk',
        'jumlah_masuk',
    ];

    /**
     * Relasi ke Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    /**
     * Relasi ke Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }
}
