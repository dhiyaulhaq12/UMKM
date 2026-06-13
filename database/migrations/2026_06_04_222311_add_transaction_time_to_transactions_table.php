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
    Schema::table('transactions', function (Blueprint $table) {
        // Menambahkan kolom jam, defaultnya diisi jam sekarang jika data lama kosong
        $table->time('transaction_time')->nullable()->after('transaction_date');
    });
}

public function down()
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->dropColumn('transaction_time');
    });
}
};
