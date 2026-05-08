<?php

namespace Ometra\Apollo\Proteus\Api;

use Ometra\Caronte\Support\CaronteApplicationToken;
use Ometra\Caronte\Support\CaronteHttpClient;

class ProteusApiClient extends CaronteHttpClient
{
    protected function getBaseUrl(): string
    {
        return (string) config('proteus.base_url');
    }

    protected function makeApplicationToken(): string
    {
        return CaronteApplicationToken::make();
    }
}
