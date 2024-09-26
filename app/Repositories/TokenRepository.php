<?php

namespace App\Repositories;

use App\Models\Token;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TokenRepository
{
    public function find($tokenId): Model|Collection|Builder|array|null
    {
        return Token::query()->find($tokenId);
    }

    public function get($integration): Collection
    {
        $integrationId = getIntegrationIdByName($integration);

        return Token::query()->where('integration_id', $integrationId)->get();
    }
}
