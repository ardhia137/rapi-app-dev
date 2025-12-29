<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'nama_customer',
        'no_hp',
        'email',
        'alamat',
        'point',
        'kategori_pelanggan',
    ];

    public function invoice()
    {
        return $this->hasMany(Invoice::class);
    }

    public function surat_jalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }
     public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }
}
