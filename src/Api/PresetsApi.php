<?php

namespace Ometra\Apollo\Proteus\Api;

/**
 * Wrapper para los endpoints de presets de la API de Proteus.
 *
 * Los presets son configuraciones de transformación asociadas a un directorio.
 * Todas las llamadas requieren autenticación de usuario (X-User-Token)
 * y un tenant activo en el TenantContext.
 */
class PresetsApi
{
    public function __construct(
        protected ProteusApiClient $client,
    ) {}

    /**
     * Lista los presets de un directorio. Requiere permiso READ.
     *
     * @param  string  $directory_id  UUID del directorio.
     * @return array<string, mixed>
     */
    public function presetIndex(string $directory_id): array
    {
        return $this->client->userRequest('GET', 'directories/' . $directory_id . '/presets');
    }

    /**
     * Crea un nuevo preset en un directorio. Requiere permiso WRITE.
     *
     * @param  string  $directory_id  UUID del directorio.
     * @param  array<string, mixed>  $data  Payload del preset.
     * @return array<string, mixed>
     */
    public function presetStore(string $directory_id, array $data): array
    {
        return $this->client->userRequest(
            'POST',
            'directories/' . $directory_id . '/presets',
            payload: $data
        );
    }

    /**
     * Elimina un preset de un directorio. Requiere permiso WRITE.
     *
     * @param  string  $directory_id  UUID del directorio.
     * @param  string  $preset_id     UUID del preset.
     * @return array<string, mixed>|null  Null si la respuesta está vacía.
     */
    public function presetDelete(string $directory_id, string $preset_id): ?array
    {
        return $this->client->userRequest(
            'DELETE',
            'directories/' . $directory_id . '/presets/' . $preset_id
        );
    }

    /**
     * Devuelve el detalle de un preset. Requiere permiso READ.
     *
     * @param  string  $directory_id  UUID del directorio.
     * @param  string  $preset_id     UUID del preset.
     * @return array<string, mixed>
     */
    public function presetShow(string $directory_id, string $preset_id): array
    {
        return $this->client->userRequest(
            'GET',
            'directories/' . $directory_id . '/presets/' . $preset_id
        );
    }

    /**
     * Actualiza un preset existente. Requiere permiso WRITE.
     *
     * @param  string  $directory_id  UUID del directorio.
     * @param  string  $preset_id     UUID del preset.
     * @param  array<string, mixed>  $data  Payload con los campos a actualizar.
     * @return array<string, mixed>
     */
    public function presetUpdate(string $directory_id, string $preset_id, array $data): array
    {
        return $this->client->userRequest(
            'PUT',
            'directories/' . $directory_id . '/presets/' . $preset_id,
            payload: $data
        );
    }
}
