<?php

namespace App\Http\Controllers\Api\Carriers;

use App\Actions\Carriers\Correos\CreateDataService;
use App\Actions\Carriers\DownloadLabel;
use App\Enums\HttpStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Carriers\ServiceRequest;
use App\Services\Carriers\Correos\CorreosService;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class CorreosController extends Controller
{
    /**
     * Executes the label function to save a service based on the provided request data.
     *
     * @param ServiceRequest $request The request containing the service data.
     * @param CorreosService $correosService The CorreosService instance to handle service operations.
     * @param CreateDataService $createData The CreateDataService instance to create service data.
     * @return JsonResponse The response from saving the service.
     * @throws Throwable
     */
    public function label(
        ServiceRequest    $request,
        CorreosService    $correosService,
        CreateDataService $createData
    ): JsonResponse {
        $dataService = $createData->execute($request->validated());
        $serviceResponse = $correosService->createService($dataService);

        Log::info('Codigo de retorno: ', [$serviceResponse['codigoRetorno']]);
        if ($serviceResponse['codigoRetorno'] !== 0) {
            Log::warning('Erro ao criar serviço: ', [$serviceResponse['mensajeRetorno']]);
            return response()->json([
                'success' => false,
                'message' => $serviceResponse['mensajeRetorno'],
            ], HttpStatusEnum::BAD_REQUEST);
        }

        try {
            return response()->json([
                'success' => true,
                'data' => $correosService->saveService(
                    $serviceResponse,
                    $request->all()
                ),
            ]);
        } catch (Exception $exception) {
            Bugsnag::notifyException($exception);
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao salvar o serviço.',
            ], HttpStatusEnum::INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Executes the download label function with the provided request data.
     *
     * @param DownloadLabel $downloadLabel The DownloadLabel instance to execute.
     * @param Request $request The request containing the data.
     */
    public function download(DownloadLabel $downloadLabel, Request $request): BinaryFileResponse|JsonResponse
    {
        return $downloadLabel->execute($request->all());
    }

    /**
     * Retrieves the available service types from the CorreosService instance.
     *
     * @param CorreosService $correosService The CorreosService instance used to retrieve service types.
     * @return JsonResponse The JSON response containing the available service types.
     */
    public function serviceTypes(CorreosService $correosService): JsonResponse
    {
        return $correosService->getServicesTypes();
    }
}
