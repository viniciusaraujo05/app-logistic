<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'status_types',
            function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            }
        );

        DB::table('status_types')->insert(
            [
                ['name' => 'preparation'],
                ['name' => 'tracking'],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_types');
    }
};
