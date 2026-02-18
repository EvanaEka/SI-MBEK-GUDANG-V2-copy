<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bahan');
            $table->enum('kategori', ['pakan', 'obat']);
            $table->string('satuan');
            $table->integer('stok')->default(0);

            $table->decimal('pemakaian_rata_rata', 10, 2)
                ->default(0);

            $table->integer('lead_time')
                ->default(0);

            $table->integer('safety_stock')
                ->default(5);
                
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};

