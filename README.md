# route-connex-sdk-php

PHP SDK for the [RouteConnex API](https://www.routeconnex.com)

## Requirements

- PHP 8.1 or higher
- Composer

## Installation

Install the SDK using Composer:

```bash
composer require route-connex/route-connex-sdk-php
```

## Configuration

To use the SDK, you'll need your RouteConnex API credentials:
- **Client ID**
- **Client Secret**

You can obtain these credentials from your RouteConnex account.

## Basic Usage

### Initialize the Client

```php
use RouteConnex\RouteConnexSdkPhp\ErClient;
use RouteConnex\RouteConnexSdkPhp\ApiVersion;

// Create a new client instance
$client = ErClient::make(
    clientId: 'your-client-id',
    clientSecret: 'your-client-secret',
    version: ApiVersion::V1  // Optional, defaults to V1
);

// Optional: Set a custom base URL (for testing or different environments)
$client->setBaseUrl('https://routeconnex.local:8080/api');
```

### API Versioning

The SDK currently supports API version V1:

```php
use RouteConnex\RouteConnexSdkPhp\ApiVersion;

$client = ErClient::make(
    clientId: 'your-client-id',
    clientSecret: 'your-client-secret',
    version: ApiVersion::V1
);
```

## Available Endpoints

The SDK provides a fluent interface for accessing RouteConnex API endpoints:

### RouteConnex Endpoints

#### About Endpoint

Get information about the RouteConnex API:

```php
$response = $client->routeConnex()->about()->get();
```

#### Test Authentication

Test your API authentication:

```php
$response = $client->routeConnex()->testAuth()->get();
```

### Microsoft SharePoint Integration

#### List Files

List files from Microsoft SharePoint:

```php
$response = $client->microsoftSharepoint()->files()->get(query: [
    'site_name' => 'your-site-name',
    'channel' => 'your-channel-name',
    '_meta_' => json_encode([
        'internal_id' => 12345,
    ]),
]);

$data = json_decode($response->getBody()->getContents(), true);
```

#### Upload File

Upload a file to Microsoft SharePoint:

```php
$response = $client->microsoftSharepoint()->files()->post(body: [
    'site_name' => 'your-site-name',
    'channel' => 'your-channel-name',
    'file_path' => '/path/to/your/file.pdf',
    'file_name' => 'uploaded-file.pdf',
    '_meta_' => [
        'internal_id' => 112233,
    ],
]);

$data = json_decode($response->getBody()->getContents(), true);
$runId = $data['data']['id'];
```

### HPE Content Manager Integration

#### List Records

Query records from HPE Content Manager:

```php
$response = $client->hpeContentManager()->records()->get(query: [
    'query' => 'your-search-query',
    '_meta_' => json_encode([
        'internal_id' => 12345,
    ]),
]);

$data = json_decode($response->getBody()->getContents(), true);
```

#### Create Record

Create a new record in HPE Content Manager:

```php
$response = $client->hpeContentManager()->records()->post(body: [
    'file_path' => '/path/to/your/file.pdf',
    'record_title' => 'My Document Title',
    'record_type' => 'Document',
    'container_id' => 'container-123',
    'author_email' => 'author@example.com',
    'author_default_id' => 'author-id-123',
    'finalize_on_save' => true,
    'make_new_revision' => true,
    '_meta_' => [
        'internal_id' => 112233,
    ],
]);

$data = json_decode($response->getBody()->getContents(), true);
$runId = $data['data']['id'];
```

### Run Status Checking

Many operations in RouteConnex are asynchronous and return a run ID. You can check the status of these operations:

```php
// Get run status
$response = $client->run()->_id_($runId)->get();
$run = json_decode($response->getBody()->getContents(), true);
$status = $run['data']['status']['id'];

// Status can be: 'created', 'queued', 'running', 'succeeded', 'failed'
if ($status === 'succeeded') {
    $responseData = $run['data']['response_data'];
    // Process successful response
}
```

#### Polling for Completion

Example of polling until a run completes:

```php
$runId = $data['data']['id'];
$maxAttempts = 20;
$attempt = 0;

do {
    $run = $client->run()->_id_($runId)->get();
    $runData = json_decode($run->getBody()->getContents(), true);
    $status = $runData['data']['status']['id'];
    
    if (in_array($status, ['created', 'queued', 'running'])) {
        if ($attempt >= $maxAttempts) {
            throw new Exception('Operation timeout');
        }
        sleep(1);
        $attempt++;
        continue;
    }
    
    if ($status === 'succeeded') {
        $responseData = $runData['data']['response_data'];
        // Process successful response
    } else {
        // Handle failed status
    }
    
    break;
} while (true);
```

## Working with Responses

All endpoint methods return a PSR-7 `ResponseInterface` object:

```php
use Psr\Http\Message\ResponseInterface;

$response = $client->routeConnex()->about()->get();

// Get status code
$statusCode = $response->getStatusCode(); // 200

// Get headers
$contentType = $response->getHeader('Content-Type'); // ['application/json']

// Get response body
$body = $response->getBody()->getContents();
$data = json_decode($body, true);

// Check response structure
if ($data['status'] === 'success') {
    $result = $data['data'];
}
```

## Error Handling

The SDK throws exceptions for various error conditions:

```php
use RouteConnex\RouteConnexSdkPhp\Exceptions\InvalidHttpMethodException;
use RouteConnex\RouteConnexSdkPhp\Exceptions\NotAuthenticatedException;
use GuzzleHttp\Exception\ServerException;

try {
    $response = $client->microsoftSharepoint()->files()->get(query: [
        'site_name' => 'your-site',
    ]);
} catch (ServerException $e) {
    // Handle API errors (4xx, 5xx responses)
    $errorResponse = $e->getResponse();
    $statusCode = $errorResponse->getStatusCode();
    $errorBody = $errorResponse->getBody()->getContents();
} catch (NotAuthenticatedException $e) {
    // Handle authentication errors
} catch (InvalidHttpMethodException $e) {
    // Handle invalid HTTP method errors
}
```

## Advanced Usage

### Custom HTTP Requests

You can make custom HTTP requests using the low-level HTTP method:

```php
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

$response = $client->runHttpRequest(
    HttpMethod::GET,
    'route-connex/about'
);
```

### Method Chaining

The SDK supports fluent method chaining for clean, readable code:

```php
$response = ErClient::make('client-id', 'client-secret')
    ->setBaseUrl('https://routeconnex.local:8080/api')
    ->microsoftSharepoint()
    ->files()
    ->get(query: ['site_name' => 'my-site']);
```

## API Reference

### ErClient Methods

- `make(string $clientId, string $clientSecret, ApiVersion $version = ApiVersion::V1): ErClient` - Create a new client instance
- `setBaseUrl(string $baseUrl): ErClient` - Set custom base URL
- `getBaseUrl(): string` - Get current base URL
- `routeConnex(): RouteConnexEndpoint` - Access RouteConnex endpoints
- `microsoftSharepoint(): MicrosoftSharepointEndpoint` - Access Microsoft SharePoint endpoints
- `hpeContentManager(): HpeContentManagerEndpoint` - Access HPE Content Manager endpoints
- `run(): RunEndpoint` - Access run status endpoints

### HTTP Methods

Endpoints support the following HTTP methods where applicable:
- `get(array $query = []): ResponseInterface` - GET request with query parameters
- `post(array $body = []): ResponseInterface` - POST request with body data
- `put(array $body = []): ResponseInterface` - PUT request with body data
- `patch(array $body = []): ResponseInterface` - PATCH request with body data
- `delete(): ResponseInterface` - DELETE request

Note: Not all endpoints support all HTTP methods. Check the API documentation or endpoint implementation for supported methods.

## License

This SDK is open-sourced software licensed under the [MIT license](LICENSE.md).

## Author

**Leo Oberle**
- Email: leocello@gmail.com

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Dev Environment Configuration

For testing, create a `.env` file in the project root with your credentials:

```env
BASE_URL=https://routeconnex.local:8080/api/

# Microsoft SharePoint Configuration
MS_SP_CLIENT_ID=your-client-id
MS_SP_CLIENT_SECRET=your-client-secret
MS_SP_RUN_ID=test-run-id
MS_SP_SITE_NAME=your-site-name
MS_SP_CHANNEL_NAME=your-channel-name

# HPE Content Manager Configuration
HPE_CM_CLIENT_ID=your-client-id
HPE_CM_CLIENT_SECRET=your-client-secret
HPE_CM_RUN_ID=test-run-id
HPE_CM_RECORD_TYPE=Document
HPE_CM_CONTAINER_ID=container-123
HPE_CM_AUTHOR_ID=author-id
HPE_CM_AUTHOR_EMAIL=author@example.com

# Test Files
TEST_FILE_PATH=/path/to/test/file.pdf
TEST_LARGE_FILE_PATH=/path/to/large/file.pdf
```

### Running Tests

The SDK uses Pest PHP for testing:

```bash
# Run all tests
composer test

# Run tests with coverage
vendor/bin/pest --coverage
```

### Code Style

The SDK follows PSR-12 coding standards. Lint your code using Laravel Pint:

```bash
composer lint
```
