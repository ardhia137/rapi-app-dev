<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetTarget extends Model
{
    protected $fillable = [
        'tanggal',
        'budget_bulanan',
    ];
}
