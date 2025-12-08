<?php

namespace RouteConnex\RouteConnexSdkPhp\Endpoints\RouteConnex;

use RouteConnex\RouteConnexSdkPhp\Endpoints\RouteConnexEndpoint;

class AboutEndpoint extends RouteConnexEndpoint
{
    protected static string $path = 'about';

    protected static ?string $parentEndpoint = parent::class;

    protected static array|bool $requiresAuth = false;

    protected static bool $isValidEndpoint = true;
}
