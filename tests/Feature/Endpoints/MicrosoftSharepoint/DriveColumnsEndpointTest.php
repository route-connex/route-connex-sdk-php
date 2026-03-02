<?php

use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use RouteConnex\RouteConnexSdkPhp\Endpoints\MicrosoftSharepoint\DriveColumnsEndpoint;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

describe('General: microsoft-sharepoint/drive-columns', function () {
    it('has correct path', function () {
        $path = DriveColumnsEndpoint::getPath();

        expect($path)->toBe('microsoft-sharepoint/drive-columns');
    });
});

describe('GET microsoft-sharepoint/drive-columns', function () {
    test('HTTP method is allowed', function () {
        expect(DriveColumnsEndpoint::isMethodAllowed(HttpMethod::GET))->toBeTrue();
    });

    it('requires authentication', function () {
        expect(DriveColumnsEndpoint::requiresAuth(HttpMethod::GET))->toBeTrue();
    });

    it('can run with valid parameters', function () {
        $client = $this->makeErClientForMicrosoftSharepoint();
        $endpoint = new DriveColumnsEndpoint($client);

        $response = $endpoint->get(query: [
            'site_name' => $this->getMicrosoftSharepointTestConfig()['site_name'],
            '_meta_' => json_encode([
                'internal_id' => 12345,
            ]),
        ]);

        $responseContent = $response->getBody()->getContents();

        expect($response)->toBeInstanceOf(ResponseInterface::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getHeader('Content-Type'))->toContain('application/json')
            ->and($responseContent)->toBeString()
            ->and($responseContent)->toBeJson()
            ->and($responseContent)->toContain('"data":{"id":')
            ->and($responseContent)->toContain('"_meta_":{"internal_id":12345}')
            ->and($responseContent)->toContain('"type":{"name":"List columns from drive based on parameters given","system":"Microsoft Sharepoint"}')
            ->and($responseContent)->toContain('"status":"success"');
    });

    it('can run fluently with valid parameters', function () {
        $response = $this->makeErClientForMicrosoftSharepoint()
            ->microsoftSharepoint()
            ->driveColumns()
            ->get(query: [
                'site_name' => $this->getMicrosoftSharepointTestConfig()['site_name'],
                '_meta_' => json_encode([
                    'internal_id' => 987654,
                ]),
            ]);

        $responseContent = $response->getBody()->getContents();

        expect($response)->toBeInstanceOf(ResponseInterface::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getHeader('Content-Type'))->toContain('application/json')
            ->and($responseContent)->toBeString()
            ->and($responseContent)->toBeJson()
            ->and($responseContent)->toContain('"data":{"id":')
            ->and($responseContent)->toContain('"_meta_":{"internal_id":987654}')
            ->and($responseContent)->toContain('"type":{"name":"List columns from drive based on parameters given","system":"Microsoft Sharepoint"}')
            ->and($responseContent)->toContain('"status":"success"');
    });

    it('errors if missing required parameters', function () {
        $this->makeErClientForMicrosoftSharepoint()
            ->microsoftSharepoint()
            ->driveColumns()
            ->get(query: [
                //  Site name is required!
                // 'site_name' => $this->getMicrosoftSharepointTestConfig()['site_name'],
                'drive_name' => 'aaa',
            ]);
    })->expectException(ServerException::class);
});
