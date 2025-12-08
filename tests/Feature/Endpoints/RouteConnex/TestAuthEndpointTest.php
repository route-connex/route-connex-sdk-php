<?php

use Psr\Http\Message\ResponseInterface;
use RouteConnex\RouteConnexSdkPhp\Endpoints\RouteConnex\TestAuthEndpoint;
use RouteConnex\RouteConnexSdkPhp\Exceptions\InvalidHttpMethodException;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

describe('General: route-connex/test-auth', function () {
    it('has correct path', function () {
        $path = TestAuthEndpoint::getPath();

        expect($path)->toBe('route-connex/test-auth');
    });
});

describe('GET route-connex/test-auth', function () {
    test('HTTP method is allowed', function () {
        expect(TestAuthEndpoint::isMethodAllowed(HttpMethod::GET))->toBeTrue();
    });

    it('requires authentication', function () {
        expect(TestAuthEndpoint::requiresAuth(HttpMethod::GET))->toBeTrue();
    });

    it('can run', function () {
        $client = $this->makeErClient();
        $endpoint = new TestAuthEndpoint($client);

        expect($endpoint)->toBeInstanceOf(TestAuthEndpoint::class);

        $response = $endpoint->get();
        $responseContent = $response->getBody()->getContents();

        expect($response)->toBeInstanceOf(ResponseInterface::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getHeader('Content-Type'))->toContain('application/json')
            ->and($responseContent)->toBeString()
            ->and($responseContent)->toContain('"status":"success"');
    });

    it('can run from client fluent endpoint', function () {
        $client = $this->makeErClient();

        $response = $client->routeConnex()->testAuth()->get();
        $responseContent = $response->getBody()->getContents();

        expect($response)->toBeInstanceOf(ResponseInterface::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getHeader('Content-Type'))->toContain('application/json')
            ->and($responseContent)->toBeString()
            ->and($responseContent)->toContain('"status":"success"');
    });
});

describe('POST route-connex/test-auth', function () {
    test('HTTP method is not allowed', function () {
        expect(TestAuthEndpoint::isMethodAllowed(HttpMethod::POST))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (POST)', function () {
        $client = $this->makeErClient();
        $client->routeConnex()->testAuth()->post();
    })->expectException(InvalidHttpMethodException::class);
});

describe('PUT route-connex/test-auth', function () {
    test('HTTP method is not allowed', function () {
        expect(TestAuthEndpoint::isMethodAllowed(HttpMethod::PUT))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (PUT)', function () {
        $client = $this->makeErClient();
        $client->routeConnex()->testAuth()->put();
    })->expectException(InvalidHttpMethodException::class);
});

describe('PATCH route-connex/test-auth', function () {
    test('HTTP method is not allowed', function () {
        expect(TestAuthEndpoint::isMethodAllowed(HttpMethod::PATCH))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (PATCH)', function () {
        $client = $this->makeErClient();
        $client->routeConnex()->testAuth()->patch();
    })->expectException(InvalidHttpMethodException::class);
});

describe('DELETE route-connex/test-auth', function () {
    test('HTTP method is not allowed', function () {
        expect(TestAuthEndpoint::isMethodAllowed(HttpMethod::DELETE))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (DELETE)', function () {
        $client = $this->makeErClient();
        $client->routeConnex()->testAuth()->delete();
    })->expectException(InvalidHttpMethodException::class);
});
