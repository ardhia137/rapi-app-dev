<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceRecord extends Model
{
    protected $fillable = [
        'tanggal',
        'periode',
        'tipe',
        'kategori',
        'jumlah',
        'foto_nota',
        'deskripsi',
        'created_by'
    ];

    /**
     * Relasi ke User yang membuat record
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
