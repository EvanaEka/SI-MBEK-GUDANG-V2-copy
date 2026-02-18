<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('material_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('material_id')
                ->constrained()
                ->cascadeOnDelete();

            // jumlah batch masuk
            $table->integer('qty');

            // tanggal barang diterima
            $table->date('received_date');

            // tanggal expired
            $table->date('expired_date')->nullable();

            // harga per unit (optional tapi bagus untuk laporan)
            $table->decimal('price_per_unit', 12, 2)->nullable();

            // admin yang input
            $table->foreignId('created_by')
                ->constrained('admins');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_stocks');
    }
};
