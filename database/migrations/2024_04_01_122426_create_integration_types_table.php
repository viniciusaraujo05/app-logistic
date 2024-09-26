<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'integration_types',
            function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            }
        );

        DB::table('integration_types')->insert(
            [
                ['name' => 'marketplace'],
                ['name' => 'ecommerce'],
                ['name' => 'carrier'],
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_types');
    }
};
