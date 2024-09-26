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
            'tracking',
            function (Blueprint $table) {
                $table->id();
                $table->string('tracking');
                $table->string('order_code');
                $table->foreignId('integration_id')->nullable()->after('order_id')->constrained('public.integrations');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking');
    }
};
