<?php

namespace RouteConnex\RouteConnexSdkPhp\Endpoints\RouteConnex;

use RouteConnex\RouteConnexSdkPhp\Endpoints\RouteConnexEndpoint;

class TestAuthEndpoint extends RouteConnexEndpoint
{
    protected static string $path = 'test-auth';

    protected static ?string $parentEndpoint = parent::class;

    protected static bool $isValidEndpoint = true;
}
