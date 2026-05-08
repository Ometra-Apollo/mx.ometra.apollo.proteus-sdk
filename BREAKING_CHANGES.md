# Breaking Changes

## v2.0.0 "Helios"

This release is a complete architectural rewrite. All consumers of v1.x must follow
the migration steps below before upgrading.

---

### 1. `BaseApiService` removed

**Before (v1.x)**

```php
use Ometra\Apollo\Proteus\BaseApiService;

class MyService extends BaseApiService
{
    // custom methods using $this->client, $this->tenantId, etc.
}
```

**After (v2.0.0)**

Inject the relevant `*Api` class directly or use the `Proteus` facade:

```php
use Ometra\Apollo\Proteus\Api\MediaApi;

class MyService
{
    public function __construct(private readonly MediaApi $media) {}

    public function listImages(): array
    {
        return $this->media->mediaIndex(['type' => 'image']);
    }
}
```

---

### 2. `ProteusClient` replaced by `ProteusApiClient`

The old `ProteusClient` class is gone. `ProteusApiClient` (namespace
`Ometra\Apollo\Proteus\Api`) is not a drop-in replacement — it extends
`CaronteHttpClient` and has a different public surface.

Direct usage of the underlying HTTP client is rarely needed; prefer the typed
`*Api` wrappers or the `Proteus` facade.

---

### 3. Authentication environment variables changed

| v1.x variable       | v2.0.0 equivalent                                                       |
| ------------------- | ----------------------------------------------------------------------- |
| `PROTEUS_APP_NAME`  | Not needed — handled by Caronte SDK (`CARONTE_APP_CN`).                 |
| `PROTEUS_APP_TOKEN` | Not needed — `CaronteApplicationToken::make()` is called automatically. |
| `uri_user`          | Not used — user identity comes from `Caronte::getToken()`.              |

The only Proteus-specific variable required is:

```env
PROTEUS_BASE_URL=https://proteus.example.com/api
```

Caronte credentials (`CARONTE_APP_CN`, `CARONTE_APP_SECRET`, etc.) must be
configured separately in the Caronte SDK.

---

### 4. Tenant context is now mandatory for user-authenticated calls

The SDK reads the tenant ID from `Equidna\BeeHive\Tenancy\TenantContext` at
request time. **You must set a tenant before calling any user-authenticated endpoint.**

```php
use Equidna\BeeHive\Tenancy\TenantContext;

app(TenantContext::class)->set('your-tenant-id');
```

Application-authenticated endpoints (`CategoriesApi`) also include `X-Tenant-Id`
when a tenant is active; they work without one but will be tenant-scoped when
provided.

---

### 5. DB migrations removed

The service provider no longer publishes or runs any database migrations.
If your application depended on Proteus migrations (e.g., `proteus_apps` table),
remove those migrations from your project manually.

---

### 6. Config structure simplified

**Before (v1.x)** — `config/proteus.php` had multiple keys for application
management, token handling, resolver classes, etc.

**After (v2.0.0)** — a single key:

```php
return [
    'base_url' => env('PROTEUS_BASE_URL'),
];
```

All other keys are ignored. Re-publish the config after upgrading:

```bash
php artisan vendor:publish --tag=proteus-config --force
```

---

### 7. Partials (`DownloadMedia`, `PayloadFormatting`) removed

These were internal helpers. Multipart formatting and download handling are now
implemented inside `ProteusApiClient` and are not part of the public API.

---

### 8. Removed guide files

`IMPLEMENTATION_GUIDE.md` and `PROTEUS_APPS_GUIDE.md` are deleted. Refer to:

- `README.md` for installation, configuration, and usage.
- `docs/api-contract.md` for the full endpoint contract.
