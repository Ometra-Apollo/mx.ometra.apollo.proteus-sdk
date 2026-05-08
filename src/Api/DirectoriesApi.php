<?php

namespace Ometra\Apollo\Proteus\Api;

/**
 * Wrapper para los endpoints de directorios de la API de Proteus.
 *
 * Todas las llamadas requieren autenticación de usuario (X-User-Token)
 * y un tenant activo en el TenantContext.
 */
class DirectoriesApi
{
    public function __construct(
        protected ProteusApiClient $client,
    ) {}

    /**
     * Lista los directorios accesibles para el uploader activo.
     *
     * @param  array<string, mixed>  $data  Filtros y paginación opcionales.
     * @return array<string, mixed>
     */
    public function directoriesIndex(array $data = []): array
    {
        return $this->client->userRequest('GET', 'directories', query: $data);
    }

    /**
     * Devuelve los metadatos auxiliares para el formulario de creación de directorio.
     *
     * @param  string|null  $parentId  UUID del directorio padre, o null para el raíz.
     * @return array<string, mixed>
     */
    public function directoryCreate(?string $parentId = null): array
    {
        $endpoint = 'directories/create';

        if ($parentId !== null && $parentId !== '') {
            $endpoint .= '/' . $parentId;
        }

        return $this->client->userRequest('GET', $endpoint);
    }

    /**
     * Crea un nuevo directorio.
     *
     * @param  array<string, mixed>  $data  Payload del directorio (name, parent_id).
     * @return array<string, mixed>
     */
    public function directoryStore(array $data): array
    {
        return $this->client->userRequest('POST', 'directories', payload: $data);
    }

    /**
     * Devuelve el detalle de un directorio. Requiere permiso READ.
     *
     * @param  string  $id  UUID del directorio.
     * @return array<string, mixed>
     */
    public function directoryShow(string $id): array
    {
        return $this->client->userRequest('GET', 'directories/' . $id);
    }

    /**
     * Elimina un directorio. Requiere permiso DELETE.
     *
     * @param  string  $id  UUID del directorio.
     * @return array<string, mixed>|null  Null si la respuesta está vacía.
     */
    public function directoryDelete(string $id): ?array
    {
        return $this->client->userRequest('DELETE', 'directories/' . $id);
    }

    /**
     * Actualiza un directorio existente. Requiere permiso WRITE.
     *
     * @param  string  $id    UUID del directorio.
     * @param  array<string, mixed>  $data  Payload con los campos a actualizar.
     * @return array<string, mixed>
     */
    public function directoryUpdate(string $id, array $data): array
    {
        return $this->client->userRequest('PUT', 'directories/' . $id, payload: $data);
    }
}
