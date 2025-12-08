<?php

namespace RouteConnex\RouteConnexSdkPhp\Concerns;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RouteConnex\RouteConnexSdkPhp\Exceptions\NotAuthenticatedException;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

trait RunsHttpRequests
{
    /**
     * @throws GuzzleException
     */
    public function runHttpRequest(HttpMethod $httpMethod, string $path, bool $withAuthentication = false, array $options = []): ResponseInterface
    {
        if ($withAuthentication) {
            $this->authenticate();

            if (! $this->isAuthenticated()) {
                throw new NotAuthenticatedException('Not authenticated.');
            }

            $options['headers']['Authorization'] = 'Bearer '.$this->getToken();
        }

        return $this->client->request($httpMethod->value, $this->getFullUri($path), $options);
    }
}
