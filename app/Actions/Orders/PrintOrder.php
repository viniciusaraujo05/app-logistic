<?php

namespace App\Actions\Orders;

use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Response;

class PrintOrder
{
    /**
     * Executes the print order action.
     *
     * @param  int  $orderId  The ID of the order to be printed.
     * @return Response The PDF response containing the printed order.
     */
    public function execute(int $orderId): Response
    {
        $order = app(ShowOrder::class)->execute($orderId);
        $data = json_decode($order->getContent(), true);

        $pdf = PDF::loadView('pdf.order', ['order' => $data['data']]);
        $pdf->setOptions(
            [
                'isRemoteEnabled' => true,
                'isJavascriptEnabled' => true,
            ]
        );
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($orderId.'.pdf');
    }
}
