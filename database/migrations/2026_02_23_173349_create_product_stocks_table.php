<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();

            // Relasi ke produk
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Qty sisa di batch ini
            $table->integer('qty');

            // Sumber stok
            $table->enum('source', ['production', 'purchase', 'sale', 'manual_adjustment']);

            // ID referensi (production_id atau purchase_order_id)
            $table->unsignedBigInteger('reference_id')->nullable();

            // Tanggal masuk gudang
            $table->date('received_date');

            // Expired date (penting untuk obat & pakan)
            $table->date('expired_date')->nullable();

            // harga per unit (optional tapi bagus untuk laporan)
            $table->decimal('price_per_unit', 12, 2)->nullable();

            $table->timestamps();

            // Index untuk optimasi FIFO query
            $table->index(['product_id', 'received_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};