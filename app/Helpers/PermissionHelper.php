<?php

use Illuminate\Support\Facades\Lang;

if (! function_exists('getInversePermissionMapping')) {
    function getInversePermissionMapping(): array
    {
        $translations = Lang::get('permissions');

        $inverseMapping = [];
        foreach ($translations as $key => $value) {
            $inverseMapping[$value] = $key;
        }

        return $inverseMapping;
    }
}
