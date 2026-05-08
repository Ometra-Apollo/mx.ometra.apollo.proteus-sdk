# Proteus SDK API Contract

This contract is generated from the active Proteus `/api` routes.

## Authentication

Every request must be application-authenticated through Caronte:

| Header | Required | Source |
| --- | --- | --- |
| `X-Application-Token` | Yes | `caronte-sdk` application token. |
| `X-Tenant-Id` | Yes | Current BeeHive `TenantContext`. |
| `X-User-Token` | Optional | Current Caronte user token when the operation must run as a user. |

`uri_user` is not part of the API contract. Proteus ignores it as a request input.

## SDK Coverage

| Domain | Active routes | Current SDK coverage | Missing wrappers |
| --- | ---: | ---: | --- |
| Categories | 5 | 5 | None |
| Directories | 6 | 6 | None |
| Presets | 5 | 5 | None |
| Media | 13 | 13 | None |
| Metadata | 7 | 7 | None |

## Categories

| Wrapper | Method | URI | Auth | Notes |
| --- | --- | --- | --- | --- |
| `categoriesIndex()` | `GET` | `/api/categories` | App | Query: `filter`, `items_per_page`, `page`. |
| `categoryStore(array $data)` | `POST` | `/api/categories` | App | Payload: `key`, `name`. |
| `categoryShow(string $id)` | `GET` | `/api/categories/{id}` | App | Tenant-scoped. |
| `categoryUpdate(string $id, array $data)` | `PUT` | `/api/categories/{id}` | App | Payload: `name`. |
| `categoryDelete(string $id)` | `DELETE` | `/api/categories/{id}` | App | Fails if category has media. |

## Directories

| Wrapper | Method | URI | Auth | Permission |
| --- | --- | --- | --- | --- |
| `directoriesIndex(array $query = [])` | `GET` | `/api/directories` | App + user | Current uploader context. |
| `directoryCreate(?string $parentId = null)` | `GET` | `/api/directories/create/{parent_id?}` | App + user | Metadata helper for create forms/API clients. |
| `directoryStore(array $data)` | `POST` | `/api/directories` | App + user | Creates directory under current uploader or parent. |
| `directoryShow(string $id)` | `GET` | `/api/directories/{id}` | App + user | Requires `READ`. |
| `directoryUpdate(string $id, array $data)` | `PUT` | `/api/directories/{id}` | App + user | Requires `WRITE`. |
| `directoryDelete(string $id)` | `DELETE` | `/api/directories/{id}` | App + user | Requires `DELETE`. |

## Presets

| Wrapper | Method | URI | Auth | Permission |
| --- | --- | --- | --- | --- |
| `presetIndex(string $directoryId)` | `GET` | `/api/directories/{id}/presets` | App + user | Requires `READ`. |
| `presetStore(string $directoryId, array $data)` | `POST` | `/api/directories/{id}/presets` | App + user | Requires `WRITE`. |
| `presetShow(string $directoryId, string $presetId)` | `GET` | `/api/directories/{id}/presets/{preset_id}` | App + user | Requires `READ`. |
| `presetUpdate(string $directoryId, string $presetId, array $data)` | `PUT` | `/api/directories/{id}/presets/{preset_id}` | App + user | Requires `WRITE`. |
| `presetDelete(string $directoryId, string $presetId)` | `DELETE` | `/api/directories/{id}/presets/{preset_id}` | App + user | Requires `WRITE`. |

## Media

| Wrapper | Method | URI | Auth | Permission |
| --- | --- | --- | --- | --- |
| `mediaIndex(array $query = [])` | `GET` | `/api/media` | App + user | Tenant-scoped list. |
| `mediaUpload(array $data)` | `POST` | `/api/media` | App + user | Multipart-capable upload. |
| `mediaCreate()` | `GET` | `/api/media/create` | App + user | Upload metadata helper. |
| `mediaTags()` | `GET` | `/api/media/tags` | App + user | Tenant tag list. |
| `mediaShow(string $id)` | `GET` | `/api/media/{id}` | App + user | Requires `READ`. |
| `mediaDelete(string $id)` | `DELETE` | `/api/media/{id}` | App + user | Requires `DELETE`. |
| `mediaAvailableFormats(string $id)` | `GET` | `/api/media/{id}/available-formats` | App + user | Requires `READ`. |
| `mediaSetDefaultFormat(string $id, array $data)` | `POST` | `/api/media/{id}/available-formats` | App + user | Requires `WRITE`. |
| `mediaDownload(string $id, ?string $ext = null)` | `GET` | `/api/media/{id}/download` | App + user | Requires `READ`. |
| `mediaTransformationOptions(string $id)` | `GET` | `/api/media/{id}/request-transformations` | App + user | Requires `READ`. |
| `mediaRequestTransformations(string $id, array $data)` | `POST` | `/api/media/{id}/request-transformations` | App + user | Requires `WRITE`. |
| `mediaSetMetadata(string $id, array $data)` | `POST` | `/api/media/{id}/set-metadata` | App + user | Requires `WRITE`. |
| `mediaTagStore(string $id, array $data)` | `POST` | `/api/media/{id}/tags/store` | App + user | Requires `WRITE`. |

## Metadata

| Wrapper | Method | URI | Auth | Permission |
| --- | --- | --- | --- | --- |
| `metadataKeys(string $key)` | `GET` | `/api/media/metadata/{key}` | App | Tenant metadata key lookup. |
| `metadataValuesFromKey(string $key)` | `GET` | `/api/media/metadata/{key}/values` | App | Tenant metadata values lookup. |
| `metadataIndex(string $mediaId, array $query = [])` | `GET` | `/api/media/{id}/metadata` | App + user | Requires `READ`. |
| `metadataStore(string $mediaId, array $data)` | `POST` | `/api/media/{id}/metadata` | App + user | Requires `WRITE`. |
| `metadataUpdate(string $mediaId, array $data)` | `PUT` | `/api/media/{id}/metadata` | App + user | Requires `WRITE`. |
| `metadataShow(string $mediaId, string $key)` | `GET` | `/api/media/{id}/metadata/{key}` | App + user | Requires `READ`. |
| `metadataDelete(string $mediaId, string $key)` | `DELETE` | `/api/media/{id}/metadata/{key}` | App + user | Requires `WRITE`. |

## SDK HTTP Client

Proteus SDK uses a local HTTP client adapter built on the installed
`ometra/caronte-sdk` HTTP client support. The adapter:

1. Extends `CaronteHttpClient`.
2. Uses `config('proteus.base_url')`.
3. Builds `X-Application-Token` through `CaronteApplicationToken::make()`.
4. Uses Caronte's inherited `applicationRequest()` and `userRequest()` header behavior.
