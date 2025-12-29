<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'user_id',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
        'tanggal_setor',
        'ongkos_kirim',
        'diskon',
        'grand_total',
        'metode_pembayaran',
        'status_pembayaran',
        'status_setor',
        'bukti_setor',
        'alasan_cancel',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function surat_jalan()
    {
        return $this->hasOne(SuratJalan::class, 'invoice_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }


    protected static function boot()
    {
        parent::boot();
        static::saved(function (Invoice $invoice) {
            try {
                $suratJalans = \App\Models\SuratJalan::where('invoice_id', $invoice->id)->get();
                foreach ($suratJalans as $sj) {
                    $sj->update([
                        'grand_total' => ((float) ($invoice->grand_total ?? 0)) + ((float) ($sj->ongkos_kirim ?? 0)),
                        'status_pembayaran' => $invoice->status_pembayaran,
                    ]);
                }
            } catch (\Throwable $e) {
                // silently ignore sync errors
            }
        });
    }
    
}
