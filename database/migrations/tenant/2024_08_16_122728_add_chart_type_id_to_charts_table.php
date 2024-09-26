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
        Schema::table('charts', function (Blueprint $table) {
            $table->unsignedBigInteger('chart_type_id');

            $table->foreign('chart_type_id')
                ->references('id')
                ->on('chart_types')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('charts', function (Blueprint $table) {
            $table->dropForeign(['chart_type_id']);
            $table->dropColumn('chart_type_id');
        });
    }
};
