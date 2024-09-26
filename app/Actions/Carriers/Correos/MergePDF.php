<?php

namespace App\Actions\Carriers\Correos;

use LynX39\LaraPdfMerger\Facades\PdfMerger;

class MergePDF
{
    public function execute($name, $order)
    {
        $pdfMerger = PdfMerger::init();

        foreach ($name as $pdfName) {
            $pdfPath = storage_path('app/public/'.$pdfName.'.pdf');
            $pdfMerger->addPDF($pdfPath, 'all');
        }

        $pdfMerger->merge();
        $pdfMerger->save(storage_path('app/public/'.$order.'.pdf'), 'file');
    }
}
