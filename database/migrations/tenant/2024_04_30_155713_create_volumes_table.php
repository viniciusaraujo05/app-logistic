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
            'volumes',
            function (Blueprint $table) {
                $table->id();
                $table->string('volume_id');
                $table->decimal('weight');
                $table->string('order_code');
                $table->foreignId('integration_id')->nullable()->after('platform')->constrained('public.integrations');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volumes');
    }
};
