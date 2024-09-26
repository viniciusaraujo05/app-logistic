<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up()
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->string('name_fixed')->nullable()->after('name');
        });

        DB::table('order_statuses')->update([
            'name_fixed' => DB::raw('name')
        ]);
    }

    public function down()
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->dropColumn('name_fixed');
        });
    }
};
