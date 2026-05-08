# Proteus SDK

Cliente Laravel/PHP para consumir la API de Proteus usando la autenticacion de Caronte.

## Instalacion

```bash
composer require ometra/proteus-client
```

Publica la configuracion si necesitas sobrescribirla:

```bash
php artisan vendor:publish --tag=proteus-config
```

## Configuracion

```env
PROTEUS_BASE_URL=https://proteus.example.com/api
CARONTE_APP_CN=mi-aplicacion
CARONTE_APP_SECRET=...
```

El SDK genera `X-Application-Token` con `caronte-sdk`. No usa `PROTEUS_APP_TOKEN`,
Bearer tokens ni `uri_user`.

Proteus requiere tenant en API. Antes de llamar al SDK, debe existir un
`TenantContext` activo:

```php
use Equidna\BeeHive\Tenancy\TenantContext;

$tenantContext = app(TenantContext::class);
$tenantContext->set('tenant-id');
```

Las llamadas de usuario usan `Caronte::getToken()` y envian:

- `X-Application-Token`
- `X-User-Token`
- `X-Tenant-Id`

Las llamadas de aplicacion envian:

- `X-Application-Token`
- `X-Tenant-Id`

## Uso

```php
use Ometra\Apollo\Proteus\Facades\Proteus;

$directories = Proteus::directoriesIndex();

$media = Proteus::mediaUpload([
    'type' => 'image',
    'directory_id' => $directoryId,
    'media' => [$request->file('image')],
    'metadata' => [
        'source' => 'apollo',
    ],
]);

Proteus::mediaSetMetadata($mediaId, [
    'metadata' => [
        'title' => 'Hero image',
    ],
]);
```

Tambien puedes inyectar el cliente principal:

```php
use Ometra\Apollo\Proteus\Proteus;

public function __invoke(Proteus $proteus): array
{
    return $proteus->media()->mediaIndex(['type' => 'image']);
}
```

## API

El contrato completo de wrappers esta en [docs/api-contract.md](docs/api-contract.md).

Dominios cubiertos:

- Categories
- Directories
- Presets
- Media
- Metadata

## Pruebas

```bash
composer test
```

La suite valida que los wrappers apunten a las rutas correctas y que el cliente
envie los headers de Caronte, tenant y multipart para uploads.
