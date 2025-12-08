<?php

namespace RouteConnex\RouteConnexSdkPhp\Endpoints\Run;

use RouteConnex\RouteConnexSdkPhp\Endpoints\RunEndpoint;
use RouteConnex\RouteConnexSdkPhp\ErClient;

class _RunId_Endpoint extends RunEndpoint
{
    protected static string $path = '{runId}';

    protected static ?string $parentEndpoint = parent::class;

    protected static bool $isValidEndpoint = true;

    public function __construct(
        ErClient $client,
        protected string $runId,
    ) {
        parent::__construct($client);
    }
}
