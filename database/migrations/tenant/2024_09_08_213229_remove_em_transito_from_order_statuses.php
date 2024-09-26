<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::table('order_statuses')
            ->where('name', 'Em Transito')
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::table('order_statuses')->insert([
            'name' => 'Em Transito',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
