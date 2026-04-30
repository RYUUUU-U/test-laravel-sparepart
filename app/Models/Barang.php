<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table      = 'barang';
    protected $primaryKey = 'id_barang';
    public    $timestamps  = false;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'harga_beli',
        'harga_jual',
        'stok',
        'satuan',
        'deskripsi',
        'image_url',
        'is_active',
        'min_order',
        'berat_gram',
        'views',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'image_url'  => 'array',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'berat_gram' => 'decimal:2',
    ];

    /**
     * Relasi ke BarangMasuk
     */
    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_barang', 'id_barang');
    }

    /**
     * Relasi ke BarangKeluar
     */
    public function barangKeluar(): HasMany
    {
        return $this->hasMany(BarangKeluar::class, 'id_barang', 'id_barang');
    }

    /**
     * Relasi ke OrderItem (e-commerce orders)
     * Foreign key: order_items.barang_id → barang.id_barang
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'barang_id', 'id_barang');
    }
}
