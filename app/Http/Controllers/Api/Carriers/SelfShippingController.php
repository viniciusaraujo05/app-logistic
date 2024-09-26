<?php

namespace App\Http\Controllers\Api\Carriers;

use App\Actions\Carriers\DownloadLabel;
use App\Actions\Carriers\SelfShipping\CreateDataService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Carriers\SelfShippingRequest;
use App\Services\Carriers\SelfShipping\SelfShippingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SelfShippingController extends Controller
{
    /**
     * Executes the label function to save a service based on the provided request data.
     *
     * @param  SelfShippingRequest  $request  The request containing the service data.
     * @param  CreateDataService  $dataService  The CreateDataService instance to create service data.
     * @param  SelfShippingService  $selfShippingService  The SelfShippingService instance to handle service operations.
     * @return mixed The result of saving the service.
     */
    public function label(
        SelfShippingRequest $request,
        CreateDataService $dataService,
        SelfShippingService $selfShippingService
    ) {
        $data = $dataService->execute($request->all());

        return $selfShippingService->saveService($data);
    }

    /**
     * Executes the download label function with the provided request data.
     *
     * @param  DownloadLabel  $downloadLabel  The DownloadLabel instance to execute.
     * @param  Request  $request  The request containing the data.
     * @return BinaryFileResponse The result of executing the download label function.
     */
    public function download(Request $request, DownloadLabel $downloadLabel): BinaryFileResponse
    {
        return $downloadLabel->execute($request->all());
    }
}
