<?php

namespace App\Http\Controllers\Api\Carriers;

use App\Actions\Carriers\DownloadLabel;
use App\Actions\Carriers\Vasp\CreateDataService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Carriers\ServiceRequest;
use App\Services\Carriers\Vasp\VaspService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VaspController extends Controller
{
    /**
     * Executes the label function to save a service based on the provided request data.
     *
     * @param  ServiceRequest  $request  The request containing the service data.
     * @param  VaspService  $vaspService  The VaspService instance to handle service operations.
     * @param  CreateDataService  $createData  The CreateDataService instance to create service data.
     * @return JsonResponse The response from saving the service.
     *
     * @throws Exception If an error occurs while saving the service.
     */
    public function label(
        ServiceRequest $request,
        VaspService $vaspService,
        CreateDataService $createData
    ): JsonResponse {
        $dataService = $createData->execute($request->validated());

        return $vaspService->saveService(
            $vaspService->createService($dataService),
            $request->all()
        );
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

    /**
     * Retrieves the service types based on the postal code provided in the request.
     *
     * @param  VaspService  $vaspService  The VaspService instance used to retrieve the service types.
     * @param  Request  $request  The request object containing the postal code.
     * @return array The service types retrieved from the VaspService.
     */
    public function serviceTypes(Request $request, VaspService $vaspService): array
    {
        return $vaspService->getServiceTypes($request->get('postal_code'));
    }
}
