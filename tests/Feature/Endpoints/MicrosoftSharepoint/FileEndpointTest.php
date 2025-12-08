<?php

use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use RouteConnex\RouteConnexSdkPhp\Endpoints\MicrosoftSharepoint\FilesEndpoint;
use RouteConnex\RouteConnexSdkPhp\Exceptions\InvalidHttpMethodException;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

describe('General: microsoft-sharepoint/file', function () {
    it('has correct path', function () {
        $path = FilesEndpoint::getPath();

        expect($path)->toBe('microsoft-sharepoint/files');
    });
});

describe('GET microsoft-sharepoint/file', function () {
    test('HTTP method is allowed', function () {
        expect(FilesEndpoint::isMethodAllowed(HttpMethod::GET))->toBeTrue();
    });

    it('requires authentication', function () {
        expect(FilesEndpoint::requiresAuth(HttpMethod::GET))->toBeTrue();
    });

    it('can run with valid parameters', function () {
        $client = $this->makeErClientForMicrosoftSharepoint();
        $endpoint = new FilesEndpoint($client);

        $response = $endpoint->get(query: [
            'site_name' => $this->getMicrosoftSharepointTestConfig()['site_name'],
            'channel' => $this->getMicrosoftSharepointTestConfig()['channel_name'],
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
            ->and($responseContent)->toContain('"type":{"name":"List files based on parameters given","system":"Microsoft Sharepoint"}')
            ->and($responseContent)->toContain('"status":"success"');
    });

    it('can run fluently with valid parameters', function () {
        $response = $this->makeErClientForMicrosoftSharepoint()
            ->microsoftSharepoint()
            ->files()
            ->get(query: [
                'site_name' => $this->getMicrosoftSharepointTestConfig()['site_name'],
                'channel' => $this->getMicrosoftSharepointTestConfig()['channel_name'],
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
            ->and($responseContent)->toContain('"type":{"name":"List files based on parameters given","system":"Microsoft Sharepoint"}')
            ->and($responseContent)->toContain('"status":"success"');
    });

    it('errors if missing required parameters', function () {
        $this->makeErClientForMicrosoftSharepoint()
            ->microsoftSharepoint()
            ->files()
            ->get(query: [
                //  Site name is required!
                // 'site_name' => $this->getMicrosoftSharepointTestConfig()['site_name'],

                'channel' => $this->getMicrosoftSharepointTestConfig()['channel_name'],
            ]);
    })->expectException(ServerException::class);
});

describe('POST microsoft-sharepoint/file', function () {
    test('HTTP method is allowed', function () {
        expect(FilesEndpoint::isMethodAllowed(HttpMethod::POST))->toBeTrue();
    });

    it('requires authentication', function () {
        expect(FilesEndpoint::requiresAuth(HttpMethod::POST))->toBeTrue();
    });

    it('can run with small file', function () {
        $file_name = 'Test upload from PHP SDK '.time();

        $response = $this->makeErClientForMicrosoftSharepoint()->microsoftSharepoint()->files()->post(body: [
            'site_name' => $this->getMicrosoftSharepointTestConfig()['site_name'],
            'channel' => $this->getMicrosoftSharepointTestConfig()['channel_name'],
            'file_path' => $this->getMicrosoftSharepointTestConfig()['file_path'],
            'file_name' => $file_name,
            '_meta_' => [
                'internal_id' => 112233,
            ],
        ]);

        $responseContent = $response->getBody()->getContents();

        expect($response)->toBeInstanceOf(ResponseInterface::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getHeader('Content-Type'))->toContain('application/json')
            ->and($responseContent)->toBeString()
            ->and($responseContent)->toBeJson()
            ->and($responseContent)->toContain('"data":{"id":')
            ->and($responseContent)->toContain('"_meta_":{"internal_id":112233}')
            ->and($responseContent)->toContain('"type":{"name":"Upload new file to sharepoint","system":"Microsoft Sharepoint"}')
            ->and($responseContent)->toContain('"status":"success"');

        $runId = json_decode($responseContent, true)['data']['id'];

        $count = 0;

        do {
            $run = $this->makeErClientForMicrosoftSharepoint()->run()->_id_($runId)->get()
                ->getBody()->getContents();

            $status = json_decode($run, true)['data']['status']['id'];

            if (in_array($status, ['created', 'queued', 'running'])) {
                if ($count > 20) {
                    throw new Exception('No response from API');
                }

                sleep(1);
                $count++;

                continue;
            }

            expect($status)->toBe('succeeded')
                ->and($run)->toContain('"response_data":{"id":"')
                ->and($run)->toContain('","name":"'.$file_name.'","type":"file"');

            break;
        } while (true);
    })->skip('Skipped by default because this actually pushes a file to remote server');

    it('can run with large file', function () {
        $file_name = 'Test large upload from PHP SDK '.time();

        $response = $this->makeErClientForMicrosoftSharepoint()->microsoftSharepoint()->files()->post(body: [
            'site_name' => $this->getMicrosoftSharepointTestConfig()['site_name'],
            'channel' => $this->getMicrosoftSharepointTestConfig()['channel_name'],
            'file_path' => $this->getMicrosoftSharepointTestConfig()['large_file_path'],
            'file_name' => $file_name,
            '_meta_' => [
                'internal_id' => 223344,
            ],
        ]);

        $responseContent = $response->getBody()->getContents();

        expect($response)->toBeInstanceOf(ResponseInterface::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getHeader('Content-Type'))->toContain('application/json')
            ->and($responseContent)->toBeString()
            ->and($responseContent)->toBeJson()
            ->and($responseContent)->toContain('"data":{"id":')
            ->and($responseContent)->toContain('"_meta_":{"internal_id":223344}')
            ->and($responseContent)->toContain('"type":{"name":"Upload new file to sharepoint","system":"Microsoft Sharepoint"}')
            ->and($responseContent)->toContain('"status":"success"');

        $runId = json_decode($responseContent, true)['data']['id'];

        $count = 0;

        do {
            $run = $this->makeErClientForMicrosoftSharepoint()->run()->_id_($runId)->get()
                ->getBody()->getContents();

            $status = json_decode($run, true)['data']['status']['id'];

            if (in_array($status, ['created', 'queued', 'running'])) {
                if ($count > 20) {
                    throw new Exception('No response from API');
                }

                sleep(2);
                $count++;

                continue;
            }

            expect($status)->toBe('succeeded')
                ->and($run)->toContain('"response_data":{"id":"')
                ->and($run)->toContain('","name":"'.$file_name.'","type":"file"');

            break;
        } while (true);
    })->skip('Skipped by default because this actually pushes a file to remote server');

    it('errors if missing required parameters', function () {
        // TODO
    })->todo();
});

describe('PUT microsoft-sharepoint/file', function () {
    test('HTTP method is not not allowed', function () {
        expect(FilesEndpoint::isMethodAllowed(HttpMethod::PUT))->toBeFalse();
    });

    it('errors if try to run from client fluent', function () {
        $client = $this->makeErClientForMicrosoftSharepoint();
        $client->microsoftSharepoint()->files()->put();
    })->expectException(InvalidHttpMethodException::class);
});

describe('PATCH microsoft-sharepoint/file', function () {
    test('HTTP method is not not allowed', function () {
        expect(FilesEndpoint::isMethodAllowed(HttpMethod::PATCH))->toBeFalse();
    });

    it('errors if try to run from client fluent', function () {
        $client = $this->makeErClientForMicrosoftSharepoint();
        $client->microsoftSharepoint()->files()->patch();
    })->expectException(InvalidHttpMethodException::class);
});

describe('DELETE microsoft-sharepoint/file', function () {
    test('HTTP method is not not allowed', function () {
        expect(FilesEndpoint::isMethodAllowed(HttpMethod::DELETE))->toBeFalse();
    });

    it('errors if try to run from client fluent', function () {
        $client = $this->makeErClientForMicrosoftSharepoint();
        $client->microsoftSharepoint()->files()->delete();
    })->expectException(InvalidHttpMethodException::class);
});
