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
        Schema::create(
            'addresses',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('contact_name');
                $table->string('phone_number');
                $table->string('email');
                $table->string('street');
                $table->string('place');
                $table->string('postal_code');
                $table->string('city');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
