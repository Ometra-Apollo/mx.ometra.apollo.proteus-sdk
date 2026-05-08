<?php

namespace Ometra\Apollo\Proteus\Api;

/**
 * Wrapper para los endpoints de categorías de la API de Proteus.
 *
 * Todas las llamadas usan autenticación de aplicación (X-Application-Token).
 * Las categorías están delimitadas por el tenant activo.
 */
class CategoriesApi
{
    public function __construct(
        protected ProteusApiClient $client,
    ) {}

    /**
     * Lista las categorías disponibles en el tenant activo.
     *
     * @param  array<string, mixed>  $data  Filtros y paginación (filter, items_per_page, page).
     * @return array<string, mixed>
     */
    public function categoriesIndex(array $data = []): array
    {
        return $this->client->applicationRequest('GET', 'categories', query: $data);
    }

    /**
     * Crea una nueva categoría.
     *
     * @param  array<string, mixed>  $data  Payload de la categoría (key, name).
     * @return array<string, mixed>
     */
    public function categoryStore(array $data): array
    {
        return $this->client->applicationRequest('POST', 'categories', payload: $data);
    }

    /**
     * Actualiza una categoría existente.
     *
     * @param  string  $id    UUID de la categoría.
     * @param  array<string, mixed>  $data  Payload con los campos a actualizar (name).
     * @return array<string, mixed>
     */
    public function categoryUpdate(string $id, array $data): array
    {
        return $this->client->applicationRequest('PUT', 'categories/' . $id, payload: $data);
    }

    /**
     * Elimina una categoría. Falla si la categoría tiene media asociada.
     *
     * @param  string  $id  UUID de la categoría.
     * @return array<string, mixed>|null  Null si la respuesta está vacía.
     */
    public function categoryDelete(string $id): ?array
    {
        return $this->client->applicationRequest('DELETE', 'categories/' . $id);
    }

    /**
     * Devuelve el detalle de una categoría.
     *
     * @param  string  $id  UUID de la categoría.
     * @return array<string, mixed>
     */
    public function categoryShow(string $id): array
    {
        return $this->client->applicationRequest('GET', 'categories/' . $id);
    }
}
