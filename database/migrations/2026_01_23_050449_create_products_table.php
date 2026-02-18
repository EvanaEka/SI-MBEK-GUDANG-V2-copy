<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('kode')->unique();
            $table->string('nama');

            // stok produk jadi (kg)
            $table->decimal('stok', 10, 2)->default(0);

            //ROP
            $table->integer('rop')
                ->default(0);

            // relasi ke formula
            $table->foreignId('formula_id')
                ->nullable()
                ->constrained('formulas')
                ->nullOnDelete();

            //Tipe sumber produk
            $table->enum('type', ['produksi', 'pembelian']);

            // siapa admin yg buat data produk
            $table->foreignId('created_by')
                ->constrained('admins')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
