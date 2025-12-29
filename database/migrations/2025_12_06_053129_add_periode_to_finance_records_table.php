<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('finance_records', function (Blueprint $table) {
            $table->string('periode', 7)->nullable()->after('tanggal'); // Format: YYYY-MM
            $table->index('periode');
        });
    }

    public function down()
    {
        Schema::table('finance_records', function (Blueprint $table) {
            $table->dropColumn('periode');
        });
    }
};
