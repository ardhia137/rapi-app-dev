<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update existing data from 'expanse' to 'expense'
        DB::table('finance_records')
            ->where('tipe', 'expanse')
            ->update(['tipe' => 'expense']);

        // Modify the enum column
        DB::statement("ALTER TABLE finance_records MODIFY COLUMN tipe ENUM('income', 'expense') NOT NULL");
    }

    public function down()
    {
        // Rollback: change back to 'expanse'
        DB::table('finance_records')
            ->where('tipe', 'expense')
            ->update(['tipe' => 'expanse']);

        DB::statement("ALTER TABLE finance_records MODIFY COLUMN tipe ENUM('income', 'expanse') NOT NULL");
    }
};
