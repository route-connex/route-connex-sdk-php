<?php

namespace RouteConnex\RouteConnexSdkPhp\Endpoints;

use RouteConnex\RouteConnexSdkPhp\Endpoints\RouteConnex\AboutEndpoint;
use RouteConnex\RouteConnexSdkPhp\Endpoints\RouteConnex\TestAuthEndpoint;

class RouteConnexEndpoint extends Endpoint
{
    protected static string $path = 'route-connex';

    protected static bool $isValidEndpoint = false;

    public function about(): AboutEndpoint
    {
        return new AboutEndpoint($this->client);
    }

    public function testAuth(): TestAuthEndpoint
    {
        return new TestAuthEndpoint($this->client);
    }
}
