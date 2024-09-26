<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdAndVolumesTotalToTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'tracking',
            function (Blueprint $table) {
                $table->integer('order_id')->nullable();
                $table->integer('volumes_total')->nullable();
                $table->string('receiver_name')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'tracking',
            function (Blueprint $table) {
                $table->dropColumn('order_id');
                $table->dropColumn('volumes_total');
                $table->dropColumn('receiver_name');
            }
        );
    }
}
