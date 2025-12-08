<?php

use Psr\Http\Message\ResponseInterface;
use RouteConnex\RouteConnexSdkPhp\Endpoints\RouteConnex\AboutEndpoint;
use RouteConnex\RouteConnexSdkPhp\Exceptions\InvalidHttpMethodException;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

describe('General: route-connex/about', function () {
    it('has correct path', function () {
        $path = AboutEndpoint::getPath();

        expect($path)->toBe('route-connex/about');
    });
});

describe('GET route-connex/about', function () {
    test('HTTP method is allowed', function () {
        expect(AboutEndpoint::isMethodAllowed(HttpMethod::GET))->toBeTrue();
    });

    it('does not require authentication', function () {
        expect(AboutEndpoint::requiresAuth(HttpMethod::GET))->toBeFalse();
    });

    it('can run', function () {
        $client = $this->makeErClient();
        $about = new AboutEndpoint($client);

        expect($about)->toBeInstanceOf(AboutEndpoint::class);

        $response = $about->get();
        $responseContent = $response->getBody()->getContents();

        expect($response)->toBeInstanceOf(ResponseInterface::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getHeader('Content-Type'))->toContain('application/json')
            ->and($responseContent)->toBeString()
            ->and($responseContent)->toContain('"status":"success"');
    });

    it('can run from client fluent endpoint', function () {
        $client = $this->makeErClient();

        $response = $client->routeConnex()->about()->get();
        $responseContent = $response->getBody()->getContents();

        expect($response)->toBeInstanceOf(ResponseInterface::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getHeader('Content-Type'))->toContain('application/json')
            ->and($responseContent)->toBeString()
            ->and($responseContent)->toContain('"status":"success"');
    });
});

describe('POST route-connex/about', function () {
    test('HTTP method is not allowed', function () {
        expect(AboutEndpoint::isMethodAllowed(HttpMethod::POST))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (POST)', function () {
        $client = $this->makeErClient();
        $client->routeConnex()->about()->post();
    })->expectException(InvalidHttpMethodException::class);
});

describe('PUT route-connex/about', function () {
    test('HTTP method is not allowed', function () {
        expect(AboutEndpoint::isMethodAllowed(HttpMethod::PUT))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (PUT)', function () {
        $client = $this->makeErClient();
        $client->routeConnex()->about()->put();
    })->expectException(InvalidHttpMethodException::class);
});

describe('PATCH route-connex/about', function () {
    test('HTTP method is not allowed', function () {
        expect(AboutEndpoint::isMethodAllowed(HttpMethod::PATCH))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (PATCH)', function () {
        $client = $this->makeErClient();
        $client->routeConnex()->about()->patch();
    })->expectException(InvalidHttpMethodException::class);
});

describe('DELETE route-connex/about', function () {
    test('HTTP method is not allowed', function () {
        expect(AboutEndpoint::isMethodAllowed(HttpMethod::DELETE))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (DELETE)', function () {
        $client = $this->makeErClient();
        $client->routeConnex()->about()->delete();
    })->expectException(InvalidHttpMethodException::class);
});
