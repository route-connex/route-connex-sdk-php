# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

PHP SDK (library, PHP ^8.1, Guzzle 7) for the RouteConnex API. Namespace `RouteConnex\RouteConnexSdkPhp\` maps to `src/`; tests use `RouteConnex\RouteConnexSdkPhp\Tests\` → `tests/`.

## Commands

Run everything through Docker with `make` (see `make help`): the image is pinned to PHP 8.1, the minimum supported version, and must stay on 8.1. The host PHP may be newer, so don't treat a host `composer test` run as proof of 8.1 compatibility.

```bash
make build                                     # build the PHP 8.1 image
make install                                   # composer install
make test                                      # run all Pest tests
make test ARGS="tests/Feature/Endpoints/MicrosoftSharepoint/DriveColumnsEndpointTest.php"   # one file
make test ARGS='--filter="requires authentication"'                                         # by test name
make lint                                      # Laravel Pint (PSR-12 / Laravel style)
make composer ARGS="..."                       # any composer command
```

`docker-compose.yml` uses host networking so `BASE_URL` resolves like on the host. Test files referenced by `TEST_FILE_PATH`/`TEST_LARGE_FILE_PATH` must be mounted into the container via an untracked `docker-compose.override.yml` (see the comment in `docker-compose.yml`).

### Tests hit a live API

There are no HTTP mocks. `tests/TestCase.php` loads `.env` (see `.env.example`) in `setUp()` and **requires every listed variable to be non-empty** — even pure unit tests fail without a complete `.env`. Feature tests call a real RouteConnex instance at `BASE_URL` using separate client credentials per integration (`MS_SP_*` for SharePoint, `HPE_CM_*` for HPE Content Manager); use `makeErClientForMicrosoftSharepoint()` / `makeErClientForHpeContentManager()` and the matching `get*TestConfig()` helpers. Validation failures from the API surface as Guzzle `ServerException`.

## Architecture

`ErClient` composes three traits in `src/Concerns/`:
- `AuthenticatesApp` — OAuth2 client-credentials flow against `{baseUrl}/auth/token` (note: **not** version-prefixed), caching an `ErAccessToken` until it expires.
- `RunsHttpRequests` — `runHttpRequest()` adds the Bearer header when auth is required and builds the URI as `{baseUrl}/{version}/{path}` (`ApiVersion` enum, currently only `v1`).
- `HasEndpoints` — fluent entry points (`routeConnex()`, `microsoftSharepoint()`, `hpeContentManager()`, `run()`).

### Endpoint model (`src/Endpoints/`)

All endpoints extend the abstract `Endpoint`, configured almost entirely through **static properties**:
- `$path` — this segment only; `getPath()` walks `$parentEndpoint` to build the full path.
- `$parentEndpoint = parent::class` — child endpoints *extend* their group class (e.g. `FilesEndpoint extends MicrosoftSharepointEndpoint`) and point to it for path composition.
- `$isValidEndpoint` — `false` on group/namespace classes (e.g. `MicrosoftSharepointEndpoint`) so they can't be requested directly; `true` on leaf endpoints. Must be redeclared on every leaf since it's inherited from the group.
- `$allowedMethods` — array of `HttpMethod` cases (default GET only).
- `$requiresAuth` — `bool`, or an array keyed by `HttpMethod::*_VALUE` constants (`get`, `post`, …). If using the array form, every allowed method needs an entry or `requiresAuth()` throws.

Group classes expose children through methods (`files()`, `driveColumns()`). Path parameters use `{name}` placeholders resolved from same-named instance properties via `getPathWithReplaces()` — see `Run/_RunId_Endpoint.php` (`_Name_` naming convention for parameterized endpoints, constructed via `RunEndpoint::_id_($id)`).

`get()`/`post()`/etc. on `Endpoint` send `query` as query string and `body` as JSON (`json` Guzzle option). Most integration endpoints are async on the server side: they return a run ID that is polled via `run()->_id_($id)->get()` (statuses: `created`, `queued`, `running`, `succeeded`, `failed`).

### Adding an endpoint

Follow the pattern from commit `3fe366d` (drive-columns): leaf class under the group's subfolder, accessor method on the group class, a Feature test mirroring the folder structure (path, allowed method, auth, live call, fluent call, missing-param error), and an entry in `CHANGELOG.md`. Also update `README.md` and `resources/boost/skills/route-connex-sdk-development/SKILL.md` (a Laravel Boost skill shipped for consumers of the SDK).

## Releases

The version lives in three places that must be bumped together: `composer.json` (`version`), `version`, and the top heading of `CHANGELOG.md` (newest first). Releases are tagged `vX.Y.Z`.
