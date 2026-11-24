---
name: route-connex-sdk-development
description: Integrate with the RouteConnex API using the PHP SDK, including authentication, Microsoft SharePoint file operations, HPE Content Manager records, and async run polling.
---

# RouteConnex SDK Development

## When to use this skill

Use this skill when integrating a Laravel application with the RouteConnex API — authenticating, uploading files to Microsoft SharePoint, managing HPE Content Manager records, or polling asynchronous operation results.

## Installation

```bash
composer require route-connex/route-connex-sdk-php
```

## Client Initialization

```php
use RouteConnex\RouteConnexSdkPhp\ErClient;
use RouteConnex\RouteConnexSdkPhp\ApiVersion;

$client = ErClient::make(
    clientId: config('route-connex.client_id'),
    clientSecret: config('route-connex.client_secret'),
    version: ApiVersion::V1, // optional, V1 is default
);

// Optional: override the base URL (e.g. for local development)
$client->setBaseUrl('https://routeconnex.local:8080/api');
```

Store `client_id` and `client_secret` in `.env` and expose them via a config file — never hardcode credentials.

## Authentication

Authentication is OAuth2 client credentials and is handled automatically for any endpoint that requires it. You may also call it explicitly:

```php
$client->authenticate();             // authenticates if not already authenticated
$client->authenticate(forceNewToken: true); // force a new token even if current is valid

$client->isAuthenticated();          // bool
$client->getToken();                 // string|null
$client->getTokenExpiry();           // DateTimeInterface|null
```

## Endpoints

All endpoints return a PSR-7 `ResponseInterface`. Decode the body with `json_decode($response->getBody()->getContents(), true)`.

The standard response envelope is:

```json
{
  "status": "success",
  "data": { ... },
  "type": { "name": "...", "system": "..." }
}
```

### RouteConnex

```php
// About — no authentication required
$response = $client->routeConnex()->about()->get();

// Test authentication
$response = $client->routeConnex()->testAuth()->get();
```

### Microsoft SharePoint — Files

```php
// List files (async — returns a run ID)
$response = $client->microsoftSharepoint()->files()->get(query: [
    'site_name' => 'your-site',
    'channel'   => 'your-channel',
    '_meta_'    => json_encode(['internal_id' => 123]), // optional
]);

// Upload a file (async — returns a run ID)
$response = $client->microsoftSharepoint()->files()->post(body: [
    'site_name' => 'your-site',
    'channel'   => 'your-channel',
    'file_path' => '/absolute/path/to/file.pdf',
    'file_name' => 'uploaded-file.pdf',
    '_meta_'    => ['internal_id' => 123], // optional
]);
```

### Microsoft SharePoint — Drive Columns

```php
// List drive columns (async — returns a run ID)
$response = $client->microsoftSharepoint()->driveColumns()->get(query: [
    'site_name' => 'your-site',
    '_meta_'    => json_encode(['internal_id' => 123]), // optional
]);
```

### HPE Content Manager — Records

```php
// Search records (async — returns a run ID)
$response = $client->hpeContentManager()->records()->get(query: [
    'query'  => 'search term',
    '_meta_' => json_encode(['internal_id' => 123]), // optional
]);

// Create a record (async — returns a run ID)
$response = $client->hpeContentManager()->records()->post(body: [
    'file_path'           => '/absolute/path/to/file.pdf',
    'record_title'        => 'Document Title',
    'record_type'         => 'Document',
    'container_id'        => 'container-id',
    'author_email'        => 'author@example.com',
    'author_default_id'   => 'author-id',
    'finalize_on_save'    => true,
    'make_new_revision'   => false,
    '_meta_'              => ['internal_id' => 123], // optional
]);
```

## Polling Asynchronous Runs

Many operations are asynchronous. They return a `run_id` which must be polled until the status is `succeeded` or `failed`.

```php
// Extract run ID from an async response
$data = json_decode($response->getBody()->getContents(), true);
$runId = $data['data']['id'];

// Poll for completion
do {
    sleep(2);
    $runResponse = $client->run()->_id_($runId)->get();
    $run = json_decode($runResponse->getBody()->getContents(), true);
    $status = $run['data']['status']['id']; // created|queued|running|succeeded|failed
} while (in_array($status, ['created', 'queued', 'running']));

if ($status === 'succeeded') {
    $result = $run['data']['response_data'];
}
```

Run statuses: `created`, `queued`, `running`, `succeeded`, `failed`.

## Exception Handling

```php
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use RouteConnex\RouteConnexSdkPhp\Exceptions\NotAuthenticatedException;
use RouteConnex\RouteConnexSdkPhp\Exceptions\InvalidHttpMethodException;
use RouteConnex\RouteConnexSdkPhp\Exceptions\InvalidEndpointException;

try {
    $response = $client->microsoftSharepoint()->files()->post(body: [...]);
} catch (NotAuthenticatedException $e) {
    // OAuth token could not be obtained
} catch (ClientException $e) {
    // 4xx HTTP error — $e->getResponse()->getStatusCode()
} catch (ServerException $e) {
    // 5xx HTTP error
}
```

## Service Provider Registration (recommended pattern)

Bind the client as a singleton so it reuses the cached OAuth token across the request lifecycle:

```php
// AppServiceProvider::register()
$this->app->singleton(ErClient::class, fn () => ErClient::make(
    clientId: config('route-connex.client_id'),
    clientSecret: config('route-connex.client_secret'),
));
```

Then inject `ErClient` via the constructor or resolve it with `app(ErClient::class)`.