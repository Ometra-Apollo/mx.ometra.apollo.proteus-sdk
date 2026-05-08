# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

> **This file is the single source of truth for project history.**
> Every future release — whether produced by a human or an AI agent — must update this file
> before merging to `main`.

---

## [v2.0.0] - 2026-05-08 "Helios"

Complete ground-up rewrite of the SDK. The old architecture (`BaseApiService`,
`ProteusClient`, DB migrations, Partials) has been replaced with a focused
HTTP-client layer built on top of `caronte-sdk`'s `CaronteHttpClient`.

### Added

- **`ProteusApiClient`** – low-level HTTP client extending `CaronteHttpClient`.
  Handles URL assembly, multipart detection, query-string building, retry
  configuration, and `X-User-Token` / `X-Application-Token` / `X-Tenant-Id`
  header injection.
- **`MediaApi`** – 13 wrappers covering the full `/api/media` surface:
  `mediaIndex`, `mediaCreate`, `mediaTags`, `mediaShow`, `mediaUpload`,
  `uploadFile`, `mediaDelete`, `mediaAvailableFormats`, `mediaSetDefaultFormat`,
  `mediaTransformationOptions`, `mediaRequestTransformations`,
  `mediaRequestTransformations`, `mediaSetMetadata`, `mediaTagStore`,
  `mediaDownload`, `saveMediaLocal`.
- **`MetadataApi`** – 7 wrappers: `metadataKeys`, `metadataValuesFromKey`,
  `metadataIndex`, `metadataShow`, `metadataStore`, `metadataUpdate`,
  `metadataDelete`.
- **`CategoriesApi`** – 5 wrappers: `categoriesIndex`, `categoryStore`,
  `categoryUpdate`, `categoryDelete`, `categoryShow`.
- **`DirectoriesApi`** – 6 wrappers: `directoriesIndex`, `directoryCreate`,
  `directoryStore`, `directoryShow`, `directoryDelete`, `directoryUpdate`.
- **`PresetsApi`** – 5 wrappers: `presetIndex`, `presetStore`, `presetDelete`,
  `presetShow`, `presetUpdate`.
- **`Proteus`** main class with `__call` magic delegation to the five API scopes
  and explicit typed accessors (`media()`, `metadata()`, `categories()`,
  `directories()`, `presets()`).
- **`ProteusServiceProvider`** – registers all API classes as singletons;
  publishes `config/proteus.php` under the `proteus-config` tag.
- **`Facades\Proteus`** – static facade bound to `Proteus::class`.
- **`docs/api-contract.md`** – machine-readable contract listing every endpoint,
  auth requirements, permissions, and SDK coverage table.
- **PHPUnit test suite** (`tests/`) with `ApiWrappersTest`, `ProteusApiClientTest`,
  and `RecordingProteusApiClient` test double.
- **`phpunit.xml`** configuration file.
- **`.editorconfig`** and **`.gitattributes`** project-level config files.
- **PHPDoc** on all public and protected methods across every API class.

### Changed

- `composer.json` updated to `ometra/proteus-client`; requires `php ^8.2`,
  `illuminate/{database,http,routing,support} ^12.0`,
  `guzzlehttp/guzzle ^7.9`, `ometra/caronte-sdk ^3.2`,
  `league/flysystem-aws-s3-v3 ^3.0`.
- `config/proteus.php` simplified to a single `base_url` key read from
  `PROTEUS_BASE_URL` env variable.
- `README.md` rewritten in Spanish; documents installation, configuration,
  auth headers, tenant context setup, and usage examples.

### Removed

- `src/BaseApiService.php` – monolithic HTTP service class.
- `src/Partials/DownloadMedia.php` – extracted partial.
- `src/Partials/PayloadFormatting.php` – extracted partial.
- `src/config/proteus.php` – old config location (superseded by `config/`).
- `database/migrations/` – DB migrations are no longer part of this SDK.
- `IMPLEMENTATION_GUIDE.md` – replaced by `docs/api-contract.md` and `README.md`.
- `PROTEUS_APPS_GUIDE.md` – application management is no longer SDK's concern.
- `LICENSE` file removed (project is now under org-level licensing).

### Breaking Changes

See [BREAKING_CHANGES.md](BREAKING_CHANGES.md) for full migration guidance.

- `BaseApiService` is gone. All API interaction now goes through the dedicated
  `*Api` classes or the `Proteus` facade.
- DB migrations are no longer published by the service provider.
- `ProteusClient` is replaced by `ProteusApiClient` (different namespace and API).
- Config key `proteus.base_url` is the only required config; all other old keys
  are obsolete.
- `PROTEUS_APP_NAME`, `PROTEUS_APP_TOKEN`, `uri_user` are no longer used.
  Authentication is handled entirely by `caronte-sdk`.

---

<!-- Future releases: prepend a new ## [vX.Y.Z] section above this line. -->
