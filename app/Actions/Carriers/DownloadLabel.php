<?php

namespace App\Actions\Carriers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadLabel
{
    /**
     * Executes the download of a label based on the provided data.
     *
     * @param array $data The data needed to generate the label.
     */
    public function execute(array $data): BinaryFileResponse|JsonResponse
    {
        try {
            $disco = Storage::disk('public');
            $path = $disco->path($data['order_code'] . $data['type']);

            return response()->file($path)->deleteFileAfterSend();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Arquivo não encontrado'], 404);
        }
    }
}
