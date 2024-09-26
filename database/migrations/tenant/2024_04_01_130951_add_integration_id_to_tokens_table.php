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
            'tokens',
            function (Blueprint $table) {
                $table->foreignId('integration_id')->nullable()->after('platform')->constrained('public.integrations');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'tokens',
            function (Blueprint $table) {
                $table->dropForeign(['integration_id']);
                $table->dropColumn('integration_id');
            }
        );
    }
};
