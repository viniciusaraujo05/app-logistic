<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $integrations = [
            ['name' => 'vasp', 'type' => 'carrier'],
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
        //
    }
};
