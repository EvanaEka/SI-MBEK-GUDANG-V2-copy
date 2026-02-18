<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulas', function (Blueprint $table) {
            $table->id();

            $table->string('kode_formula')->unique();
            $table->string('nama_formula');
            $table->text('deskripsi')->nullable();

            // admin yang membuat formula
            $table->foreignId('created_by')
                ->constrained('admins')
                ->restrictOnDelete();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulas');
    }
};
