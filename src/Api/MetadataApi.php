<?php

namespace Ometra\Apollo\Proteus\Api;

/**
 * Wrapper para los endpoints de metadata de la API de Proteus.
 *
 * Los métodos de catálogo (keys, values) usan autenticación de aplicación.
 * Los métodos de instancia de media usan autenticación de usuario.
 */
class MetadataApi
{
    public function __construct(
        protected ProteusApiClient $client,
    ) {}

    /**
     * Devuelve la definición de una clave de metadato del catálogo.
     *
     * @param  string  $key  Clave de metadato.
     * @return array<string, mixed>
     */
    public function metadataKeys(string $key): array
    {
        return $this->client->applicationRequest('GET', 'media/metadata/' . $key);
    }

    /**
     * Devuelve los valores registrados para una clave de metadato del catálogo.
     *
     * @param  string  $key  Clave de metadato.
     * @return array<string, mixed>
     */
    public function metadataValuesFromKey(string $key): array
    {
        return $this->client->applicationRequest('GET', 'media/metadata/' . $key . '/values');
    }

    /**
     * Lista los metadatos de un recurso de media.
     *
     * @param  string  $id    UUID del recurso de media.
     * @param  array<string, mixed>  $data  Filtros opcionales.
     * @return array<string, mixed>
     */
    public function metadataIndex(string $id, array $data = []): array
    {
        return $this->client->userRequest('GET', 'media/' . $id . '/metadata', query: $data);
    }

    /**
     * Devuelve el valor de un metadato específico de un recurso de media.
     *
     * @param  string  $id   UUID del recurso de media.
     * @param  string  $key  Clave del metadato.
     * @return array<string, mixed>
     */
    public function metadataShow(string $id, string $key): array
    {
        return $this->client->userRequest('GET', 'media/' . $id . '/metadata/' . $key);
    }

    /**
     * Crea un nuevo metadato en un recurso de media.
     *
     * @param  string  $id    UUID del recurso de media.
     * @param  array<string, mixed>  $data  Payload con la clave y valor del metadato.
     * @return array<string, mixed>
     */
    public function metadataStore(string $id, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $id . '/metadata', payload: $data);
    }

    /**
     * Actualiza un metadato existente de un recurso de media.
     *
     * @param  string  $id    UUID del recurso de media.
     * @param  array<string, mixed>  $data  Payload con los valores a actualizar.
     * @return array<string, mixed>
     */
    public function metadataUpdate(string $id, array $data): array
    {
        return $this->client->userRequest('PUT', 'media/' . $id . '/metadata', payload: $data);
    }

    /**
     * Elimina un metadato de un recurso de media.
     *
     * @param  string  $id   UUID del recurso de media.
     * @param  string  $key  Clave del metadato a eliminar.
     * @return array<string, mixed>|null  Null si la respuesta está vacía.
     */
    public function metadataDelete(string $id, string $key): ?array
    {
        return $this->client->userRequest('DELETE', 'media/' . $id . '/metadata/' . $key);
    }
}
