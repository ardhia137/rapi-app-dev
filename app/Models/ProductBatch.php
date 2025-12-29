<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'batch_number',
        'harga_beli',
        'quantity_masuk',
        'quantity_sekarang',
        'tanggal_masuk',
        'tanggal_expired',
        'supplier',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function refreshStatus(): void
    {
        $status = 'active';
        $today = Carbon::today();
        $expiredAt = $this->tanggal_expired ? Carbon::parse($this->tanggal_expired) : null;

        if ($expiredAt && $expiredAt->lt($today)) {
            $status = 'expired';
        } elseif ((int) $this->quantity_sekarang <= 0) {
            $status = 'sold_out';
        }

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }
    }
}
