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
        Schema::table(
            'orders',
            function (Blueprint $table) {
                $table->foreignId('source_integration_id')->nullable()->after('id')->constrained('public.integrations');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'orders',
            function (Blueprint $table) {
                $table->dropForeign(['source_integration_id']);
                $table->dropColumn('source_integration_id');
            }
        );
    }
};
