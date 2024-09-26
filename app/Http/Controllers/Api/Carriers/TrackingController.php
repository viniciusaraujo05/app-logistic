<?php

namespace App\Http\Controllers\Api\Carriers;

use App\Actions\Carriers\Tracking\ShowTracking;
use App\Actions\Carriers\Tracking\UpdateTracking;
use App\Http\Controllers\Controller;
use App\Http\Requests\Carriers\UpdateTrackingRequest;
use Illuminate\Http\JsonResponse;

class TrackingController extends Controller
{
    /**
     * Show a Tracking.
     *
     * @param  string  $orderId  The order code.
     * @param  ShowTracking  $showTracking  The object responsible for showing the tracking.
     * @return JsonResponse The JSON response containing the result of the show operation.
     */
    public function show(string $orderId, ShowTracking $showTracking): JsonResponse
    {
        return $showTracking->execute($orderId);
    }

    /**
     * Updates an order using the provided request and Tracking ID.
     *
     * @param  UpdateTrackingRequest  $request  The request containing the updated Tracking data.
     * @param  UpdateTracking  $updateTracking  The service responsible for updating the Tracking.
     * @return JsonResponse The JSON response containing the result of the update operation.
     */
    public function update(UpdateTrackingRequest $request, int $orderId, UpdateTracking $updateTracking): JsonResponse
    {
        return $updateTracking->execute($request->all(), $orderId);
    }
}
