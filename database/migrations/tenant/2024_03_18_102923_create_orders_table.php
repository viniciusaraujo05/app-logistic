<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'orders',
            function (Blueprint $table) {
                $table->id();
                $table->string('order_code');
                $table->string('customer_name');
                $table->json('shipping_address');
                $table->string('email');
                $table->string('phone');
                $table->string('notes');
                $table->string('status');
                $table->string('weight');
                $table->string('price');
                $table->json('products');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
