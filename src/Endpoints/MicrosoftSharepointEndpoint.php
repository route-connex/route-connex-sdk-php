<?php

namespace RouteConnex\RouteConnexSdkPhp\Endpoints;

use RouteConnex\RouteConnexSdkPhp\Endpoints\MicrosoftSharepoint\FilesEndpoint;

class MicrosoftSharepointEndpoint extends Endpoint
{
    protected static string $path = 'microsoft-sharepoint';

    protected static bool $isValidEndpoint = false;

    public function files(): FilesEndpoint
    {
        return new FilesEndpoint($this->client);
    }
}
