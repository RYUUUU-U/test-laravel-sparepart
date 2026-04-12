<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table      = 'supplier';
    protected $primaryKey = 'id_supplier';
    public    $timestamps  = false;

    protected $fillable = [
        'nama_supplier',
        'no_telp',
        'alamat',
    ];

    /**
     * Relasi ke BarangMasuk
     */
    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_supplier', 'id_supplier');
    }
}
