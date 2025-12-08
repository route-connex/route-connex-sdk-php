<?php

namespace RouteConnex\RouteConnexSdkPhp\Concerns;

use RouteConnex\RouteConnexSdkPhp\Endpoints\HpeContentManagerEndpoint;
use RouteConnex\RouteConnexSdkPhp\Endpoints\MicrosoftSharepointEndpoint;
use RouteConnex\RouteConnexSdkPhp\Endpoints\RouteConnexEndpoint;
use RouteConnex\RouteConnexSdkPhp\Endpoints\RunEndpoint;

trait HasEndpoints
{
    public function routeConnex(): RouteConnexEndpoint
    {
        return new RouteConnexEndpoint($this);
    }

    public function run(): RunEndpoint
    {
        return new RunEndpoint($this);
    }

    public function microsoftSharepoint(): MicrosoftSharepointEndpoint
    {
        return new MicrosoftSharepointEndpoint($this);
    }

    public function hpeContentManager(): HpeContentManagerEndpoint
    {
        return new HpeContentManagerEndpoint($this);
    }
}
