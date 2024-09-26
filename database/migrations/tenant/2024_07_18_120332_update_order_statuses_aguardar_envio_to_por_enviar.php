<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('order_statuses')
            ->where('name', 'Aguardar envio')
            ->update(['name' => 'Por Enviar']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('order_statuses')
            ->where('name', 'Por Enviar')
            ->update(['name' => 'Aguardar envio']);
    }
};
