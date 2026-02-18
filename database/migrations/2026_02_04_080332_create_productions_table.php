<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();

            // Formula yang diproduksi
            $table->foreignId('formula_id')
                ->constrained()
                ->cascadeOnDelete();

            // Produk jadi
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Jumlah produksi (kg)
            $table->integer('qty_produksi');

            /**
             * ======================
             * QC RESULT (SUMMARY)
             * ======================
             */

            // Status hasil QC
            $table->enum('qc_status', [
                'pending',
                'layak',
                'tidak_layak',
            ])->default('pending');

            // Persentase indikator non-kritis yang lolos
            $table->decimal('qc_percentage', 5, 2)
                ->nullable()
                ->comment('Persentase kelulusan indikator non-kritis');

            // Ambang kelayakan QC (default 80%)
            $table->decimal('qc_threshold', 5, 2)
                ->default(80)
                ->comment('Minimal persentase kelayakan QC');

            // Tanggal produksi nyata
            $table->date('production_date');

            // Expired date
            $table->date('expired_date')->nullable();

            /**
             * ======================
             * STATUS PRODUKSI
             * ======================
             */
            $table->enum('status', [
                'diproses',
                'selesai',
                'rejected',
            ])->default('diproses');

            // Dicatat oleh admin
            $table->foreignId('created_by')
                ->constrained('admins');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
