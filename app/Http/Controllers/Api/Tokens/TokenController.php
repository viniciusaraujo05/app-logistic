<?php

namespace App\Http\Controllers\Api\Tokens;

use App\Actions\Tokens\CreateToken;
use App\Actions\Tokens\DestroyToken;
use App\Actions\Tokens\IndexTokens;
use App\Actions\Tokens\ShowToken;
use App\Actions\Tokens\ToggleToken;
use App\Actions\Tokens\UpdateToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tokens\IndexTokensRequest;
use App\Http\Requests\Tokens\TokenRequest;
use App\Http\Requests\Tokens\UpdateTokenRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class TokenController extends Controller
{
    /**
     * Show all tokens.
     *
     * @param IndexTokensRequest $request
     * @param IndexTokens $indexTokens description
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index(IndexTokensRequest $request, IndexTokens $indexTokens): JsonResponse
    {
        return $indexTokens->execute($request->get('integration_type'));
    }

    /**
     * create a new token.
     *
     * @param TokenRequest $request description
     * @param CreateToken $createToken description
     *
     * @throws Throwable
     */
    public function create(TokenRequest $request, CreateToken $createToken): JsonResponse
    {
        return $createToken->execute($request->all());
    }

    /**
     * Show a specific token.
     *
     * @param Request $request The ID of the token to show.
     * @param ShowToken $showToken The object responsible for showing the token.
     * @return JsonResponse The JSON response containing the result of the show operation.
     *
     * @throws Throwable
     */
    public function show(Request $request, ShowToken $showToken): JsonResponse
    {
        return $showToken->execute($request->get('token_id'));
    }

    /**
     * Update a token using the given request and ID.
     *
     * @param UpdateTokenRequest $request The request containing the token data
     * @param UpdateToken $updateToken The service to update the token
     * @return JsonResponse The JSON response after updating the token
     *
     * @throws Throwable
     */
    public function update(UpdateTokenRequest $request, UpdateToken $updateToken): JsonResponse
    {
        return $updateToken->execute($request->all());
    }

    /**
     * Update a token using the given request and ID.
     *
     * @param Request $request The request containing the token data
     * @param ToggleToken $updateToken The service to update the token
     * @return JsonResponse The JSON response after updating the token
     */
    public function toggle(Request $request, ToggleToken $updateToken): JsonResponse
    {
        return $updateToken->execute($request->get('tokenId'));
    }

    /**
     * Delete a token using the given request and ID.
     *
     * @param string $tokenId description
     * @param DestroyToken $destroyToken description
     *
     * @throws Throwable
     */
    public function destroy(string $tokenId, DestroyToken $destroyToken): JsonResponse
    {
        return $destroyToken->execute($tokenId);
    }
}
