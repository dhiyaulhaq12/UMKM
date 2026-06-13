<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['income', 'expense']); // Pendapatan atau Pengeluaran
            $table->string('name');                      // Nama Kategori/Produk (ex: Nasi Goreng)
            $table->decimal('default_price', 15, 2)->default(0); // Harga satuan (ex: 15000)
            $table->string('unit')->default('pcs');      // Satuan (pcs, porsi, kg)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_categories');
    }
};
