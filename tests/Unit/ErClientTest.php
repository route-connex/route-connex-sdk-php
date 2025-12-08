<?php

use RouteConnex\RouteConnexSdkPhp\ErClient;

describe('RouteConnexClient', function () {
    it('cannot be instantiated with missing parameters', function () {
        ErClient::make();
    })->expectException(ArgumentCountError::class);

    it('can be instantiated with client configuration', function () {
        $client = ErClient::make('app_id', 'app_secret');

        expect($client)->toBeInstanceOf(ErClient::class);
    });

    it('a base url can be changed in an instance', function () {
        $client = ErClient::make('app_id', 'app_secret');
        $previousUrl = $client->getBaseUrl();
        $client->setBaseUrl('http://localhost/test');

        expect($client->getBaseUrl())->toBe('http://localhost/test')
            ->and($client->getBaseUrl())->not()->toBe($previousUrl);
    });
});
