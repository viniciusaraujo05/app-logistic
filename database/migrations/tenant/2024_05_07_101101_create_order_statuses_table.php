<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'order_statuses',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedInteger('order')->unique();
                $table->timestamps();
            }
        );

        $statuses = [
            ['name' => 'Por pagar', 'order' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Por aprovar', 'order' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Preparação', 'order' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Por enviar', 'order' => 4, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Enviado', 'order' => 5, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        foreach ($statuses as $status) {
            DB::table('order_statuses')->insert($status);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
