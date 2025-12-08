<?php

namespace RouteConnex\RouteConnexSdkPhp;

class ErAccessToken
{
    private \DateTimeInterface $tokenExpiry;

    public function __construct(
        private readonly string $accessToken,
        private readonly string $tokenType,
        int $expiresIn = 0,
    ) {
        $tokenExpiry = (new \DateTime('now'))
            ->setTimezone(new \DateTimeZone('UTC'))
            ->add(\DateInterval::createFromDateString($expiresIn.' seconds'));

        $this->tokenExpiry = \DateTimeImmutable::createFromMutable($tokenExpiry);
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getTokenExpiry(): \DateTimeInterface
    {
        return $this->tokenExpiry;
    }

    public function getExpiresIn(): int
    {
        return $this->tokenExpiry->getTimestamp() - (new \DateTime('now'))->setTimezone(new \DateTimeZone('UTC'))->getTimestamp();
    }

    public function isValid(): bool
    {
        return ! empty($this->accessToken) && $this->getExpiresIn() > 0;
    }
}
