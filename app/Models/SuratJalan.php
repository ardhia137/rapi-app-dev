<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    protected $table = 'surat_jalans';

    protected $fillable = [
        'nomor_surat_jalan',
        'invoice_id',
        'customer_id',
        'tanggal',
        'status',
        'bukti_pengiriman',
        'alasan_cancel',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Tampilkan grand_total sebagai total invoice + ongkos_kirim saat dibaca (untuk UI)
    public function getGrandTotalAttribute($value)
    {
        $base = $this->invoice ? (float) ($this->invoice->grand_total ?? 0) : (float) ($value ?? 0);
        $shipping = (float) ($this->ongkos_kirim ?? 0);
        return $base + $shipping;
    }
}
