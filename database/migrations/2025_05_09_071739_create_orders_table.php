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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->unsignedBigInteger('orderable_id')->nullable();
            $table->string('orderable_type')->nullable();
            $table->index(['orderable_id', 'orderable_type']);

            $table->string('order_id')->unique();
            $table->string('snap_token')->nullable();
            $table->integer('gross_amount');
            $table->string('name')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->integer('qty')->default(1);
            $table->enum('status', [
                'pending',
                'settlement',
                'capture',
                'success',
                'failed',
                'expire',
                'cancel'
            ])->default('pending');
            $table->enum('payment_method', ['midtrans', 'manual'])->default('midtrans'); // Tambah kolom payment_method
            $table->string('bukti_transfer')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('bank_origin')->nullable();
            $table->date('transfer_date')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};