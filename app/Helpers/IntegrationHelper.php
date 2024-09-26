<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('getIntegrationIdByName')) {
    function getIntegrationIdByName(string $integrationName)
    {
        return DB::table('public.integrations')
            ->where('name', $integrationName)
            ->value('id');
    }
}
