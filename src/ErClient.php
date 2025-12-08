<?php

namespace RouteConnex\RouteConnexSdkPhp;

use GuzzleHttp\Client;
use RouteConnex\RouteConnexSdkPhp\Concerns as Concerns;

class ErClient
{
    use Concerns\AuthenticatesApp,
        Concerns\HasEndpoints,
        Concerns\RunsHttpRequests;

    protected string $baseUrl = 'https://app.routeconnex.com/api';

    protected Client $client;

    public function __construct(
        protected string $clientId,
        protected string $clientSecret,
        protected ApiVersion $version = ApiVersion::V1,
    ) {
        $this->client = new Client;
    }

    public static function make(
        string $clientId,
        string $clientSecret,
        ApiVersion $version = ApiVersion::V1,
    ): ErClient {
        return new ErClient(
            clientId: $clientId,
            clientSecret: $clientSecret,
            version: $version,
        );
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): ErClient
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    protected function getFullUri(string $path): string
    {
        $url = $this->baseUrl;

        if (! str_ends_with($url, '/')) {
            $url .= '/';
        }

        $url .= $this->version->value;

        if (! str_starts_with($path, '/')) {
            $url .= '/';
        }

        return $url.$path;
    }
}
