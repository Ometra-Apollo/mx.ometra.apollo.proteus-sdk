<?php

namespace Ometra\Apollo\Proteus\Api;

use Equidna\BeeHive\Tenancy\TenantContext;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Ometra\Caronte\Exceptions\CaronteApiException;
use Ometra\Caronte\Facades\Caronte;
use Ometra\Caronte\Support\CaronteApplicationToken;
use Ometra\Caronte\Support\CaronteHttpClient;
use RuntimeException;

/**
 * Cliente HTTP de bajo nivel para la API de Proteus.
 *
 * Extiende CaronteHttpClient para añadir la resolución de URL base desde
 * la configuración de Proteus y la inyección de cabeceras de autenticación
 * de usuario (X-User-Token + X-Tenant-Id) además de las de aplicación.
 *
 * Los payloads que contienen UploadedFile se envían automáticamente
 * como peticiones multipart.
 */
class ProteusApiClient extends CaronteHttpClient
{
    /**
     * Realiza una petición autenticada como usuario.
     *
     * Envía X-Application-Token, X-User-Token y, si hay un TenantContext
     * activo, X-Tenant-Id.
     *
     * @param  string  $method    Método HTTP (GET, POST, PUT, PATCH, DELETE).
     * @param  string  $endpoint  Ruta relativa del endpoint (sin barra inicial).
     * @param  array<string, mixed>  $payload  Cuerpo de la petición.
     * @param  array<string, mixed>  $query    Parámetros de query string.
     * @return array<string, mixed>
     */
    public function userRequest(
        string $method,
        string $endpoint,
        array $payload = [],
        array $query = [],
    ): array {
        $headers = [
            'X-Application-Token' => $this->makeApplicationToken(),
            'X-User-Token' => Caronte::getToken()->toString(),
        ];

        $tenantId = $this->tenantId();
        if ($tenantId !== null) {
            $headers['X-Tenant-Id'] = $tenantId;
        }

        return $this->request($method, $endpoint, $payload, $query, $headers);
    }

    /**
     * Devuelve la URL base de la API de Proteus desde la configuración.
     *
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return (string) config('proteus.base_url');
    }

    /**
     * Genera un token de aplicación Caronte.
     *
     * @return string
     */
    protected function makeApplicationToken(): string
    {
        return CaronteApplicationToken::make();
    }

    /**
     * Ejecuta la petición HTTP y devuelve la respuesta parseada.
     *
     * Construye la URL completa, selecciona multipart si el payload contiene
     * archivos, despacha la petición y delega el parseo al método de la clase padre.
     *
     * @param  string  $method    Método HTTP en minúsculas o mayúsculas.
     * @param  string  $endpoint  Ruta relativa del endpoint.
     * @param  array<string, mixed>  $payload  Cuerpo de la petición.
     * @param  array<string, mixed>  $query    Parámetros de query string.
     * @param  array<string, string>  $headers  Cabeceras HTTP adicionales.
     * @return array<string, mixed>
     *
     * @throws CaronteApiException Si el método HTTP no está soportado.
     */
    protected function request(
        string $method,
        string $endpoint,
        array $payload,
        array $query,
        array $headers,
    ): array {
        $url = rtrim($this->getBaseUrl(), '/') . '/' . ltrim($endpoint, '/');
        $urlWithQuery = $query !== [] ? $url . '?' . http_build_query($query) : $url;
        $method = strtolower($method);

        $request = Http::acceptJson()
            ->withOptions([
                'verify' => (bool) config('caronte.tls_verify', true),
            ])
            ->timeout((int) config('caronte.http.timeout', 10))
            ->retry(
                times: (int) config('caronte.http.retries', 1),
                sleepMilliseconds: (int) config('caronte.http.retry_sleep', 150)
            )
            ->withHeaders($headers);

        if ($this->shouldUseMultipart($payload)) {
            $request = $request->asMultipart();
            $payload = $this->toMultipart($payload);
        }

        $response = match ($method) {
            'get' => $request->get($url, $query),
            'delete' => $request->delete($url, $payload !== [] ? $payload : $query),
            'post' => $request->post($urlWithQuery, $payload),
            'put' => $request->put($urlWithQuery, $payload),
            'patch' => $request->patch($urlWithQuery, $payload),
            default => throw new CaronteApiException("Unsupported HTTP method [{$method}].", 500),
        };

        return $this->parseResponse($response);
    }

    /**
     * Obtiene el tenant ID del TenantContext activo, si existe.
     *
     * @return string|null  El ID del tenant o null si no hay contexto activo.
     */
    private function tenantId(): ?string
    {
        if (!app()->bound(TenantContext::class)) {
            return null;
        }

        /** @var TenantContext $tenantContext */
        $tenantContext = app(TenantContext::class);
        $tenantId = $tenantContext->get();

        return is_string($tenantId) && trim($tenantId) !== '' ? trim($tenantId) : null;
    }

    /**
     * Determina si el payload debe enviarse como multipart.
     *
     * Devuelve true si algún valor del payload (o de arrays anidados)
     * es una instancia de UploadedFile o un resource.
     *
     * @param  array<string, mixed>  $payload
     */
    private function shouldUseMultipart(array $payload): bool
    {
        foreach ($payload as $value) {
            if ($value instanceof UploadedFile || is_resource($value)) {
                return true;
            }

            if (is_array($value) && $this->shouldUseMultipart($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, array{name: string, contents: mixed, filename?: string}>
     */
    private function toMultipart(array $payload, ?string $prefix = null): array
    {
        $parts = [];

        foreach ($payload as $key => $value) {
            $name = $prefix === null ? (string) $key : "{$prefix}[{$key}]";

            if ($value instanceof UploadedFile) {
                $parts[] = $this->filePart($name, $value);
                continue;
            }

            if (is_array($value)) {
                if ($this->isListOfFiles($value)) {
                    foreach ($value as $file) {
                        $parts[] = $this->filePart($name . '[]', $file);
                    }
                    continue;
                }

                $parts = array_merge($parts, $this->toMultipart($value, $name));
                continue;
            }

            $parts[] = [
                'name' => $name,
                'contents' => $this->scalarPartContents($value),
            ];
        }

        return $parts;
    }

    /**
     * @param array<int|string, mixed> $value
     */
    private function isListOfFiles(array $value): bool
    {
        if ($value === []) {
            return false;
        }

        foreach ($value as $item) {
            if (!$item instanceof UploadedFile) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{name: string, contents: resource, filename: string}
     */
    private function filePart(string $name, UploadedFile $file): array
    {
        $path = $file->getRealPath();

        if (!is_string($path)) {
            throw new RuntimeException("Cannot read uploaded file [{$file->getClientOriginalName()}].");
        }

        $stream = fopen($path, 'r');

        if (!is_resource($stream)) {
            throw new RuntimeException("Cannot open uploaded file [{$file->getClientOriginalName()}].");
        }

        return [
            'name' => $name,
            'contents' => $stream,
            'filename' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Convierte un valor escalar a string para incluirlo en un payload multipart.
     *
     * Los booleanos se convierten a '1' o '0'; null se convierte a cadena vacía.
     *
     * @param  mixed  $value
     * @return string
     */
    private function scalarPartContents(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return $value === null ? '' : (string) $value;
    }
}
