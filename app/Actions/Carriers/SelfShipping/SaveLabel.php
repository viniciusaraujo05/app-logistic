<?php

namespace App\Actions\Carriers\SelfShipping;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class SaveLabel
{
    /**
     * Executes the PDF generation process for the shipping label.
     *
     * @param  mixed  $data  The data used for generating the PDF.
     *
     * @throws Exception If there are any issues during PDF generation.
     */
    public function execute($data)
    {
        $html = implode(
            '',
            array_map(
                function ($volume) use ($data) {
                    return View::make(
                        'pdf.shipping',
                        ['data' => $data, 'volume' => $volume]
                    )->render();
                },
                $data['volumes']
            )
        );

        $pdf = PDF::loadHTML($html);
        $pdf->setOptions(
            [
                'isRemoteEnabled' => true,
                'isJavascriptEnabled' => true,
            ]
        );

        $pdf->setPaper('a4', 'portrait');
        $pdf->render();

        $archiveName = $data['order_code'].'.pdf';

        return $pdf->save(Storage::disk('public')->path($archiveName));
    }
}
