<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'barcode',
        'kategori',
        'harga',
        'satuan',
        'foto_produk',
        'min_stok_alert',
        'status',
    ];

    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }
    public function latestBatch()
    {
        return $this->hasOne(ProductBatch::class)
            ->latestOfMany('created_at'); 
    }

    public function refreshAvailability(): void
    {
        $totalStok = (int) $this->batches()->sum('quantity_sekarang');
        $newStatus = $totalStok > 0 ? 'available' : 'unavailable';
        if ($this->status !== $newStatus) {
            $this->update(['status' => $newStatus]);
        }
    }
    
}
