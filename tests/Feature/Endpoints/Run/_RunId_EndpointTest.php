<?php

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use RouteConnex\RouteConnexSdkPhp\Endpoints\Run\_RunId_Endpoint;
use RouteConnex\RouteConnexSdkPhp\Exceptions\InvalidHttpMethodException;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

describe('General: run/{runId}', function () {
    it('has correct path', function () {
        $path = _RunId_Endpoint::getPath();

        expect($path)->toBe('run/{runId}');
    });
});

describe('GET run/{runId}', function () {
    test('HTTP method is allowed', function () {
        expect(_RunId_Endpoint::isMethodAllowed(HttpMethod::GET))->toBeTrue();
    });

    it('requires authentication', function () {
        expect(_RunId_Endpoint::requiresAuth(HttpMethod::GET))->toBeTrue();
    });

    test('path with replaces works correctly', function () {
        $client = $this->makeErClient();
        $endpoint = new _RunId_Endpoint($client, 'test-id');

        expect($endpoint)->toBeInstanceOf(_RunId_Endpoint::class)
            ->and($endpoint->getPathWithReplaces())->toBe('run/test-id');
    });

    it('returns "Not found" error if an invalid ID is used', function () {
        $client = $this->makeErClient();

        try {
            $client->run()->_id_('not-a-valid-id')->get();

            // It shouldn't get here - or it fails the test
            expect(true)->toBeFalse();
        } catch (ClientException $e) {
            expect($e->getCode())->toBe(404)
                ->and($e->getResponse()->getStatusCode())->toBe(404)
                ->and($e->getResponse()->getBody()->getContents())->toContain('error')->toContain('Run ID not found');
        }
    });

    it('can run', function () {
        $client = $this->makeErClientForMicrosoftSharepoint();
        $endpoint = new _RunId_Endpoint($client, $this->getMicrosoftSharepointTestConfig()['run_id']);

        $response = $endpoint->get();
        $responseContent = $response->getBody()->getContents();

        expect($response)->toBeInstanceOf(ResponseInterface::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getHeader('Content-Type'))->toContain('application/json')
            ->and($responseContent)->toBeString()
            ->and($responseContent)->toBeJson()
            ->and($responseContent)->toContain('"data":{"id":"'.$this->getMicrosoftSharepointTestConfig()['run_id'].'"')
            ->and($responseContent)->toContain('"status":"success"');
    });

    it('can run from client fluent endpoint', function () {
        $response = $this->makeErClientForMicrosoftSharepoint()->run()->_id_($this->getMicrosoftSharepointTestConfig()['run_id'])->get();
        $responseContent = $response->getBody()->getContents();

        expect($response)->toBeInstanceOf(ResponseInterface::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getHeader('Content-Type'))->toContain('application/json')
            ->and($responseContent)->toBeString()
            ->and($responseContent)->toBeJson()
            ->and($responseContent)->toContain('"data":{"id":"'.$this->getMicrosoftSharepointTestConfig()['run_id'].'"')
            ->and($responseContent)->toContain('"status":"success"');
    });
});

describe('POST run/{runId}', function () {
    test('HTTP method is not allowed', function () {
        expect(_RunId_Endpoint::isMethodAllowed(HttpMethod::POST))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (POST)', function () {
        $client = $this->makeErClient();
        $client->run()->_id_('any-id')->post();
    })->expectException(InvalidHttpMethodException::class);
});

describe('PUT run/{runId}', function () {
    test('HTTP method is not allowed', function () {
        expect(_RunId_Endpoint::isMethodAllowed(HttpMethod::PUT))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (PUT)', function () {
        $client = $this->makeErClient();
        $client->run()->_id_('any-id')->put();
    })->expectException(InvalidHttpMethodException::class);
});

describe('PATCH run/{runId}', function () {
    test('HTTP method is not allowed', function () {
        expect(_RunId_Endpoint::isMethodAllowed(HttpMethod::PATCH))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (PATCH)', function () {
        $client = $this->makeErClient();
        $client->run()->_id_('any-id')->patch();
    })->expectException(InvalidHttpMethodException::class);
});

describe('DELETE run/{runId}', function () {
    test('HTTP method is not allowed', function () {
        expect(_RunId_Endpoint::isMethodAllowed(HttpMethod::DELETE))->toBeFalse();
    });

    it('errors if try to run from client fluent endpoint with invalid HTTP method (DELETE)', function () {
        $client = $this->makeErClient();
        $client->run()->_id_('any-id')->delete();
    })->expectException(InvalidHttpMethodException::class);
});
