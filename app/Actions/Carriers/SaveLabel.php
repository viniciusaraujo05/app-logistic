<?php

namespace App\Actions\Carriers;

use Illuminate\Support\Facades\Storage;

class SaveLabel
{
    /**
     * Executes the action to save a label to storage.
     *
     * @param  mixed  $label  The label content to be saved.
     * @param  string  $name  The name of the label file.
     * @param  string  $type  The type of the label file.
     * @return bool Returns true on successful save, false otherwise.
     */
    public function execute(mixed $label, string $name, string $type): bool
    {
        try {
            $archiveName = $name.'.'.strtolower($type);
            Storage::disk('public')->put($archiveName, $label);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
