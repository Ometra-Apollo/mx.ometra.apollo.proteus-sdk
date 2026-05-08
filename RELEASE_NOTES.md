# Release v2.0.0 "Helios"

**Date:** 2026-05-08
**Branch:** `remake` → `main`
**Package:** `ometra/proteus-client`

---

## Summary

Helios is a complete ground-up rewrite of the Proteus SDK. The old architecture — built around
a monolithic `BaseApiService` with DB migrations, partials, and manual token management — has
been replaced with a clean, focused HTTP-client layer that delegates authentication entirely
to `caronte-sdk`.

Every Proteus API domain (Media, Metadata, Categories, Directories, Presets) is now covered by
a dedicated typed class. A `Proteus` facade provides ergonomic static access. All 36 wrappers
are fully tested.

---

## Highlights

- **Full API coverage** – 36 wrappers across 5 domains with zero missing endpoints.
- **Caronte-native auth** – `X-Application-Token`, `X-User-Token`, and `X-Tenant-Id` are
  assembled automatically from `caronte-sdk`; no more manual token configuration.
- **Multipart auto-detection** – payloads containing `UploadedFile` instances are
  transparently converted to multipart requests.
- **BeeHive tenant resolution** – `TenantContext` is resolved at request time; no static
  config required.
- **PHPUnit test suite** – `RecordingProteusApiClient` test double captures requests for
  assertion without hitting the network.
- **Zero DB footprint** – migrations removed; the SDK is now a pure HTTP client.
- **PHP 8.2+ and Laravel 12** – modern baseline with strict types throughout.

---

## Added

- `ProteusApiClient` — low-level HTTP client (extends `CaronteHttpClient`).
- `MediaApi`, `MetadataApi`, `CategoriesApi`, `DirectoriesApi`, `PresetsApi` — typed API wrappers.
- `Proteus` main class with magic delegation and typed accessors.
- `Facades\Proteus` static facade.
- `ProteusServiceProvider` (singleton registration, config publishing).
- `docs/api-contract.md` — endpoint contract reference.
- Full PHPUnit test suite under `tests/`.
- PHPDoc on all public and protected API methods.

## Changed

- `composer.json` — updated dependencies (PHP 8.2, Laravel 12, Guzzle 7.9, Caronte SDK 3.2).
- `config/proteus.php` — simplified to `base_url` only.
- `README.md` — rewritten with installation, configuration, auth, and usage sections.

## Removed

- `BaseApiService`, `DownloadMedia`, `PayloadFormatting` partials.
- DB migrations.
- Old guides (`IMPLEMENTATION_GUIDE.md`, `PROTEUS_APPS_GUIDE.md`).

---

## Breaking Changes

This is a **major version** bump. Consumers of v1.x must migrate. See
[BREAKING_CHANGES.md](BREAKING_CHANGES.md) for step-by-step migration guidance.

---

## Full History

See [CHANGELOG.md](CHANGELOG.md) for the complete project history.
