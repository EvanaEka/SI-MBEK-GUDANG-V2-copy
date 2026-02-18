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
        Schema::create('disposals', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation
            $table->morphs('disposable');
            // menghasilkan:
            // disposable_id
            // disposable_type

            $table->integer('quantity');
            $table->string('reason');
            // contoh: qc_failed, expired, damaged, contamination

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->constrained('admins')
                ->cascadeOnDelete();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposals');
    }
};
