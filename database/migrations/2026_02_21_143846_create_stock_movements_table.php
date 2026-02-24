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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->morphs('stockable');
            // stockable_id
            // stockable_type

            $table->enum('type', ['in', 'out']);

            $table->integer('quantity');

            $table->string('source')->nullable();
            // PO, Production, Sale, Adjustment

            $table->unsignedBigInteger('reference_id')->nullable();
            // id PO, id Production, id Order dll

            $table->timestamp('movement_date')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
