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
            'order_statuses',
            function (Blueprint $table) {
                $table->dropUnique(['order']);

                $table->index('order');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'order_statuses',
            function (Blueprint $table) {
                $table->dropIndex(['order']);

                $table->unique('order');
            }
        );
    }
};
