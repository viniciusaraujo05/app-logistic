<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'integrations',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('integration_type_id')->constrained('integration_types');
                $table->timestamps();
            }
        );

        $integrations = [
            ['name' => 'woocommerce', 'type' => 'ecommerce'],
            ['name' => 'magento', 'type' => 'ecommerce'],
            ['name' => 'worten', 'type' => 'marketplace'],
            ['name' => 'kuantokusta', 'type' => 'marketplace'],
            ['name' => 'amazon', 'type' => 'marketplace'],
        ];

        foreach ($integrations as $integration) {
            $integrationTypeId = DB::table('integration_types')
                ->where('name', $integration['type'])
                ->value('id');

            DB::table('integrations')->insert(
                [
                    'name' => $integration['name'],
                    'integration_type_id' => $integrationTypeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
