<?php

use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use RouteConnex\RouteConnexSdkPhp\Endpoints\HpeContentManager\RecordsEndpoint;
use RouteConnex\RouteConnexSdkPhp\Exceptions\InvalidHttpMethodException;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

describe('General: hpe-content-manager/records', function () {
    it('has correct path', function () {
        $path = RecordsEndpoint::getPath();

        expect($path)->toBe('hpe-content-manager/records');
    });
});

describe('GET hpe-content-manager/records', function () {
    test('HTTP method is allowed', function () {
        expect(RecordsEndpoint::isMethodAllowed(HttpMethod::GET))->toBeTrue();
    });

    it('requires authentication', function () {
        expect(RecordsEndpoint::requiresAuth(HttpMethod::GET))->toBeTrue();
    });

    it('can run with valid parameters', function () {
        $client = $this->makeErClientForHpeContentManager();
        $endpoint = new RecordsEndpoint($client);

        $response = $endpoint->get(query: [
            'query' => 'TEST',
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
            ->and($responseContent)->toContain('"type":{"name":"List records based on parameters","system":"HPE Content Manager"}')
            ->and($responseContent)->toContain('"status":"success"');
    });

    it('can run fluently with valid parameters', function () {
        $response = $this->makeErClientForHpeContentManager()
            ->hpeContentManager()
            ->records()
            ->get(query: [
                'query' => 'TEST',
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
            ->and($responseContent)->toContain('"type":{"name":"List records based on parameters","system":"HPE Content Manager"}')
            ->and($responseContent)->toContain('"status":"success"');
    });

    it('errors if missing required parameters', function () {
        $this->makeErClientForHpeContentManager()
            ->hpeContentManager()
            ->records()
            ->get(/*query: [
                // Parameter `query` is required
                // 'query' => 'TEST',
            ]*/);
    })->expectException(ServerException::class);
});

describe('POST hpe-content-manager/records', function () {
    test('HTTP method is allowed', function () {
        expect(RecordsEndpoint::isMethodAllowed(HttpMethod::POST))->toBeTrue();
    });

    it('requires authentication', function () {
        expect(RecordsEndpoint::requiresAuth(HttpMethod::POST))->toBeTrue();
    });

    it('can run', function () {
        $record_title = 'Test file from elementTIME '.time();

        $response = $this->makeErClientForHpeContentManager()
            ->hpeContentManager()
            ->records()
            ->post(body: [
                'file_path' => $this->getHpeContentManagerTestConfig()['file_path'],
                'record_title' => $record_title,
                'record_type' => $this->getHpeContentManagerTestConfig()['record_type'],
                'container_id' => $this->getHpeContentManagerTestConfig()['container_id'],
                'author_email' => $this->getHpeContentManagerTestConfig()['author_email'],
                'author_default_id' => $this->getHpeContentManagerTestConfig()['author_id'],
                'finalize_on_save' => true,
                'make_new_revision' => true,
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
            ->and($responseContent)->toContain('"type":{"name":"Create new record","system":"HPE Content Manager"}')
            ->and($responseContent)->toContain('"status":"success"');

        $runId = json_decode($responseContent, true)['data']['id'];

        $count = 0;

        do {
            $run = $this->makeErClientForHpeContentManager()->run()->_id_($runId)->get()
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
                ->and($run)->toContain('"response_data":{"id":')
                ->and($run)->toContain('"title":"'.$record_title.'"');

            break;
        } while (true);
    })->skip('Skipped by default because this actually pushes a file to remote server');

    it('errors if missing required parameters', function () {
        // TODO
    })->todo();
});

describe('PUT hpe-content-manager/records', function () {
    test('HTTP method is not not allowed', function () {
        expect(RecordsEndpoint::isMethodAllowed(HttpMethod::PUT))->toBeFalse();
    });

    it('errors if try to run from client fluent', function () {
        $client = $this->makeErClientForHpeContentManager();
        $client->microsoftSharepoint()->files()->put();
    })->expectException(InvalidHttpMethodException::class);
});

describe('PATCH hpe-content-manager/records', function () {
    test('HTTP method is not not allowed', function () {
        expect(RecordsEndpoint::isMethodAllowed(HttpMethod::PATCH))->toBeFalse();
    });

    it('errors if try to run from client fluent', function () {
        $client = $this->makeErClientForHpeContentManager();
        $client->microsoftSharepoint()->files()->patch();
    })->expectException(InvalidHttpMethodException::class);
});

describe('DELETE hpe-content-manager/records', function () {
    test('HTTP method is not not allowed', function () {
        expect(RecordsEndpoint::isMethodAllowed(HttpMethod::DELETE))->toBeFalse();
    });

    it('errors if try to run from client fluent', function () {
        $client = $this->makeErClientForHpeContentManager();
        $client->microsoftSharepoint()->files()->delete();
    })->expectException(InvalidHttpMethodException::class);
});
