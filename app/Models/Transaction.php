<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_id',
        'surat_jalan_id',
        'tanggal_pembayaran',
        'jumlah_bayar',
        'metode_pembayaran',
        'bukti_pembayaran'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }
}
