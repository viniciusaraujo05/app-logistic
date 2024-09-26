<?php

namespace App\Http\Controllers\Api\Carriers;

use App\Actions\Carriers\Ctt\CreateDataService;
use App\Actions\Carriers\DownloadLabel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Carriers\ServiceRequest;
use App\Services\Carriers\Ctt\CttService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class CttController extends Controller
{
    /**
     * Executes the label function to save a service based on the provided request data.
     *
     * @param  ServiceRequest  $request  The request containing the service data.
     * @param  CttService  $cttService  The CttService instance to handle service operations.
     *
     * @throws GuzzleException
     * @throws Throwable
     */
    public function label(
        ServiceRequest $request,
        CttService $cttService,
        CreateDataService $createData
    ): JsonResponse {
        $dataService = $createData->execute($request->validated());

        return $cttService->saveService(
            $dataService,
            $request->all()
        );
    }

    /**
     * Executes the download label function with the provided request data.
     *
     * @param  DownloadLabel  $downloadLabel  The DownloadLabel instance to execute.
     * @param  Request  $request  The request containing the data.
     * @return JsonResponse|BinaryFileResponse The result of executing the download label function.
     */
    public function download(DownloadLabel $downloadLabel, Request $request): BinaryFileResponse|JsonResponse
    {
        return $downloadLabel->execute($request->all());
    }

    /**
     * Retrieves the available service types from the CttService instance.
     *
     * @param  CttService  $cttService  The CttService instance used to retrieve service types.
     * @return JsonResponse The JSON response containing the available service types.
     */
    public function serviceTypes(CttService $cttService): JsonResponse
    {
        return $cttService->getServicesTypes();
    }
}
