<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_qcs', function (Blueprint $table) {
            $table->id();

            // Relasi ke produksi (1 produksi = 1 QC)
            $table->foreignId('production_id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique();

            // Status akhir QC
            $table->enum('status', ['layak', 'tidak_layak']);

            // Persentase kelayakan non-kritis
            $table->decimal('score_non_kritis', 5, 2)
                ->comment('Persentase indikator non-kritis yang terpenuhi');

            // Ambang kelayakan (default 80%, bisa diubah admin)
            $table->decimal('threshold', 5, 2)
                ->default(80);

            // Catatan tambahan QC
            $table->text('catatan')->nullable();

            // Admin yang melakukan QC
            $table->foreignId('created_by')
                ->constrained('admins');

            $table->timestamps(); // created_at = waktu QC
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_qcs');
    }
};
