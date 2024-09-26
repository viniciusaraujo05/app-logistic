<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('account_data', function (Blueprint $table) {
            $table->id();
            $table->string('logo_image_url')->nullable();
            $table->string('website')->nullable();
            $table->json('business_area')->nullable();
            $table->string('contact')->nullable();
            $table->timestamps();
        });

        DB::table('account_data')->insert([
            'website' => 'www.example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('account_data');
    }
};
