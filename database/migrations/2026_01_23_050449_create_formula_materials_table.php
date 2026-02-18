<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formula_materials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('formula_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('material_id')
                ->constrained()
                ->restrictOnDelete();

            // Persentase komposisi bahan dalam formula (total = 100%)
            $table->decimal('persentase', 5, 2); // contoh: 25.50 %

            $table->timestamps();

            // Mencegah bahan yang sama dobel di satu formula
            $table->unique(['formula_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formula_materials');
    }
};
