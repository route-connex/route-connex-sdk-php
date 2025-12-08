<?php

namespace RouteConnex\RouteConnexSdkPhp\Endpoints\MicrosoftSharepoint;

use RouteConnex\RouteConnexSdkPhp\Endpoints\MicrosoftSharepointEndpoint;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

class FilesEndpoint extends MicrosoftSharepointEndpoint
{
    protected static string $path = 'files';

    protected static ?string $parentEndpoint = parent::class;

    protected static array|bool $requiresAuth = [
        HttpMethod::GET_VALUE => true,
        HttpMethod::POST_VALUE => true,
    ];

    protected static bool $isValidEndpoint = true;

    protected static array $allowedMethods = [
        HttpMethod::GET,
        HttpMethod::POST,
    ];
}
