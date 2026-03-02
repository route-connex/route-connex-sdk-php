<?php

namespace RouteConnex\RouteConnexSdkPhp\Endpoints\MicrosoftSharepoint;

use RouteConnex\RouteConnexSdkPhp\Endpoints\MicrosoftSharepointEndpoint;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

class DriveColumnsEndpoint extends MicrosoftSharepointEndpoint
{
    protected static string $path = 'drive-columns';

    protected static ?string $parentEndpoint = parent::class;

    protected static array|bool $requiresAuth = [
        HttpMethod::GET_VALUE => true,
    ];

    protected static bool $isValidEndpoint = true;

    protected static array $allowedMethods = [
        HttpMethod::GET,
    ];
}
