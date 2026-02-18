<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('qc_indicators', function (Blueprint $table) {
            $table->id();

            // Nama indikator QC
            $table->string('name');

            // Apakah indikator kritis
            $table->boolean('is_critical')
                ->default(false);

            // Status aktif indikator
            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qc_indicators');
    }
};
