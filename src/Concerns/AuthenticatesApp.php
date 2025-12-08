<?php

namespace RouteConnex\RouteConnexSdkPhp\Concerns;

use GuzzleHttp\Exception\GuzzleException;
use RouteConnex\RouteConnexSdkPhp\ErAccessToken;

trait AuthenticatesApp
{
    protected ?ErAccessToken $accessToken = null;

    /**
     * @throws GuzzleException
     */
    public function authenticate(bool $forceNewToken = false): self
    {
        if ($forceNewToken) {
            $this->accessToken = null;
        }

        if ($this->isAuthenticated()) {
            return $this;
        }

        $uri = $this->baseUrl.(! str_ends_with($this->baseUrl, '/') ? '/' : '').'auth/token';

        $response = $this->client->post($uri, [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->accessToken = new ErAccessToken($data['access_token'], $data['token_type'], $data['expires_in']);

        return $this;
    }

    public function isAuthenticated(): bool
    {
        return ! is_null($this->accessToken) && $this->accessToken->isValid();
    }

    public function getToken(): ?string
    {
        return $this->accessToken?->getAccessToken();
    }

    public function getTokenType(): ?string
    {
        return $this->accessToken?->getTokenType();
    }

    public function getTokenExpiry(): ?\DateTimeInterface
    {
        return $this->accessToken?->getTokenExpiry();
    }

    public function getTokenExpiresIn(): ?int
    {
        return $this->accessToken?->getExpiresIn();
    }
}
