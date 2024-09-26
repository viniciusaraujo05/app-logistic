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
        Schema::create('chart_types', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
        });

        DB::table('chart_types')->insert([
            ['identifier' => 'envios_por_dia_mes'],
            ['identifier' => 'envios_por_transportadora'],
            ['identifier' => 'tempo_medio_envio'],
            ['identifier' => 'mapa_envios_pais'],
            ['identifier' => 'encomendas_por_hora_dia'],
            ['identifier' => 'encomendas_por_dia_mes'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_types');
    }
};
