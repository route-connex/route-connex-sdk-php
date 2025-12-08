<?php

namespace RouteConnex\RouteConnexSdkPhp\Endpoints;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RouteConnex\RouteConnexSdkPhp\ErClient;
use RouteConnex\RouteConnexSdkPhp\Exceptions\InvalidEndpointException;
use RouteConnex\RouteConnexSdkPhp\Exceptions\InvalidHttpMethodException;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

abstract class Endpoint
{
    protected static string $path;

    /** @var class-string<Endpoint>|null */
    protected static ?string $parentEndpoint = null;

    protected static array|bool $requiresAuth = true;

    protected static bool $isValidEndpoint;

    protected static array $allowedMethods = [
        HttpMethod::GET,
    ];

    protected array $options = [];

    public function __construct(
        protected ErClient $client,
    ) {
        //
    }

    public static function make(ErClient $client): Endpoint
    {
        return new static($client);
    }

    // ----------------------------------------

    public static function getPath(): string
    {
        $path = '';

        if (! is_null(static::$parentEndpoint)) {
            $path = static::$parentEndpoint::getPath();

            if (! str_ends_with($path, '/')) {
                $path .= '/';
            }
        }

        return $path.static::$path;
    }

    public static function requiresAuth(HttpMethod $httpMethod): bool
    {
        if (is_bool(static::$requiresAuth)) {
            return static::$requiresAuth;
        }

        if (! isset(static::$requiresAuth[$httpMethod->value])) {
            throw new InvalidHttpMethodException('Method auth not set');
        }

        return static::$requiresAuth[$httpMethod->value];
    }

    public static function isMethodAllowed(HttpMethod $httpMethod): bool
    {
        return in_array($httpMethod, static::$allowedMethods);
    }

    public function getClient(): ErClient
    {
        return $this->client;
    }

    public function getPathWithReplaces(): string
    {
        return preg_replace_callback(
            pattern: '/(\{.*?})/',
            callback: fn ($matches) => $this->{trim($matches[1], '{}')},
            subject: static::getPath(),
        );
    }

    /**
     * @throws GuzzleException
     */
    public function request(HttpMethod $httpMethod, array $query = [], array $body = [], array $headers = []): ResponseInterface
    {
        if (! static::$isValidEndpoint) {
            throw new InvalidEndpointException('This endpoint is not valid. Maybe its path is not complete.');
        }

        if (! static::isMethodAllowed($httpMethod)) {
            throw new InvalidHttpMethodException('HTTP method not allowed for endpoint');
        }

        $options = $this->options;

        if (count($query) > 0) {
            $options['query'] = $query;
        }

        if (count($body) > 0) {
            $options['json'] = $body;
        }

        if (count($headers) > 0) {
            $options['headers'] = $headers;
        }

        return $this->client->runHttpRequest($httpMethod, static::getPathWithReplaces(), static::requiresAuth($httpMethod), $options);
    }

    /**
     * @throws GuzzleException
     */
    public function get(array $query = [], array $body = [], array $headers = []): ResponseInterface
    {
        return $this->request(HttpMethod::GET, $query, $body, $headers);
    }

    /**
     * @throws GuzzleException
     */
    public function post(array $body = [], array $query = [], array $headers = []): ResponseInterface
    {
        return $this->request(HttpMethod::POST, $query, $body, $headers);
    }

    /**
     * @throws GuzzleException
     */
    public function put(array $body = [], array $query = [], array $headers = []): ResponseInterface
    {
        return $this->request(HttpMethod::PUT, $query, $body, $headers);
    }

    /**
     * @throws GuzzleException
     */
    public function patch(array $body = [], array $query = [], array $headers = []): ResponseInterface
    {
        return $this->request(HttpMethod::PATCH, $query, $body, $headers);
    }

    /**
     * @throws GuzzleException
     */
    public function delete(array $query = [], array $body = [], array $headers = []): ResponseInterface
    {
        return $this->request(HttpMethod::DELETE, $query, $body, $headers);
    }

    // ----------------------------------------

    /**
     * @throws InvalidHttpMethodException
     */
    protected function throwInvalidHttpMethodException(): void
    {
        throw new InvalidHttpMethodException('The endpoint does not support this method.');
    }
}
