<?php

namespace App\Actions\Tokens;

use App\Enums\HttpStatusEnum;
use App\Models\Token;
use App\Utils\IntegrationMapper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateToken
{
    /**
     * Create a new token.
     *
     * @throws Throwable
     */
    public function execute(array $data): JsonResponse
    {
        try {
            $integrationMapper = new IntegrationMapper();
            $platformName = $integrationMapper->integrationName($data['platform']);

            $integration = DB::table('public.integrations')
                ->where('name', $platformName)
                ->first();

            if (!$integration) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Integration not found.',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            $data['integration_id'] = $integration->id;

            Token::query()->create($data);

            return response()->json(
                [
                    'status' => true,
                ],
                HttpStatusEnum::CREATED
            );
        } catch (Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $th->getMessage(),
                ],
                HttpStatusEnum::INTERNAL_SERVER_ERROR
            );
        }
    }
}
