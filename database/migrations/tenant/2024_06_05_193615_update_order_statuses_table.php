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
        Schema::table(
            'order_statuses',
            function (Blueprint $table) {
                $table->boolean('is_mandatory')->default(false)->after('name');
                $table->boolean('is_active')->default(true)->after('is_mandatory');
                $table->softDeletes();
                $table->unsignedBigInteger('status_type_id')->default(1)->after('is_active');

                $table->foreign('status_type_id')->references('id')->on('status_types')->onDelete('cascade');
            }
        );

        DB::table('order_statuses')->where('name', 'Por pagar')->update(
            [
                'status_type_id' => 1,
                'is_mandatory' => true,
                'is_active' => true,
            ]
        );

        DB::table('order_statuses')->where('name', 'Por aprovar')->update(
            [
                'status_type_id' => 1,
                'is_mandatory' => false,
                'is_active' => true,
            ]
        );

        DB::table('order_statuses')->where('name', 'Preparação')->update(
            [
                'status_type_id' => 1,
                'is_mandatory' => true,
                'is_active' => true,
            ]
        );

        DB::table('order_statuses')->where('name', 'Por enviar')->update(
            [
                'name' => 'Aguardar envio',
                'status_type_id' => 1,
                'is_mandatory' => true,
                'is_active' => true,
            ]
        );

        DB::table('order_statuses')->where('name', 'Enviado')->update(
            [
                'status_type_id' => 2,
                'is_mandatory' => true,
                'is_active' => true,
            ]
        );

        DB::table('order_statuses')->where('name', 'Em Transito')->update(
            [
                'status_type_id' => 2,
                'is_mandatory' => true,
                'is_active' => true,
            ]
        );

        DB::table('order_statuses')->where('name', 'Entregue')->update(
            [
                'status_type_id' => 2,
                'is_mandatory' => true,
                'is_active' => true,
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
        Schema::table(
            'order_statuses',
            function (Blueprint $table) {
                $table->dropColumn('is_mandatory');
                $table->dropColumn('is_active');
                $table->dropColumn('deleted_at');
                $table->dropForeign(['status_type_id']);
                $table->dropColumn('status_type_id');
            }
        );
    }
};
