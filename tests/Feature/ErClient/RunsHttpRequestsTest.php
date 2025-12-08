<?php

use Psr\Http\Message\ResponseInterface;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

describe('Can run HTTP requests', function () {
    it('can run GET basic requests', function () {
        $client = $this->makeErClient();
        $response = $client->runHttpRequest(HttpMethod::GET, 'route-connex/about');

        expect($response)->toBeInstanceOf(ResponseInterface::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getBody()->getContents())->toContain('"status":"success"');
    });
});
