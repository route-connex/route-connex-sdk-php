<?php

describe('RouteConnex Authentication', function () {
    it('can authenticate', function () {
        $client = $this->makeErClient();

        expect($client->isAuthenticated())->toBeFalse()
            ->and($client->getToken())->toBeNull()
            ->and($client->getTokenExpiry())->toBeNull();

        $client->authenticate();

        expect($client->isAuthenticated())->toBeTrue()
            ->and($client->getToken())->toBeString()
            ->and($client->getTokenExpiry())->toBeInstanceOf(DateTimeInterface::class);
    });
});
