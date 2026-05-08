<?php

namespace Ometra\Apollo\Proteus\Api;

/**
 * Wrapper para los endpoints de media de la API de Proteus.
 *
 * Todas las llamadas requieren autenticación de usuario (X-User-Token)
 * y un tenant activo en el TenantContext.
 */
class MediaApi
{
    public function __construct(
        protected ProteusApiClient $client,
    ) {}

    /**
     * Lista los recursos de media del tenant activo.
     *
     * @param  array<string, mixed>  $data  Filtros y paginación (filter, items_per_page, page).
     * @return array<string, mixed>
     */
    public function mediaIndex(array $data = []): array
    {
        return $this->client->userRequest('GET', 'media', query: $data);
    }

    /**
     * Devuelve los metadatos auxiliares necesarios para el formulario de creación de media.
     *
     * @return array<string, mixed>
     */
    public function mediaCreate(): array
    {
        return $this->client->userRequest('GET', 'media/create');
    }

    /**
     * Devuelve la lista de tags del tenant activo.
     *
     * @return array<string, mixed>
     */
    public function mediaTags(): array
    {
        return $this->client->userRequest('GET', 'media/tags');
    }

    /**
     * Devuelve el detalle de un recurso de media.
     *
     * @param  string  $id  UUID del recurso.
     * @return array<string, mixed>
     */
    public function mediaShow(string $id): array
    {
        return $this->client->userRequest('GET', 'media/' . $id);
    }

    /**
     * Sube un archivo de media. Alias de {@see uploadFile()}.
     *
     * @param  array<string, mixed>  $data  Payload de subida (type, directory_id, media, metadata).
     * @return array<string, mixed>
     */
    public function mediaUpload(array $data): array
    {
        return $this->uploadFile($data);
    }

    /**
     * Sube un archivo de media mediante una petición POST multipart.
     *
     * @param  array<string, mixed>  $data  Payload de subida (type, directory_id, media, metadata).
     * @return array<string, mixed>
     */
    public function uploadFile(array $data): array
    {
        return $this->client->userRequest('POST', 'media', payload: $data);
    }

    /**
     * Elimina un recurso de media.
     *
     * @param  string  $id  UUID del recurso.
     * @return array<string, mixed>|null  Null si la respuesta está vacía.
     */
    public function mediaDelete(string $id): ?array
    {
        return $this->client->userRequest('DELETE', 'media/' . $id);
    }

    /**
     * Devuelve los formatos disponibles para un recurso de media.
     *
     * @param  string  $id  UUID del recurso.
     * @return array<string, mixed>
     */
    public function mediaAvailableFormats(string $id): array
    {
        return $this->client->userRequest('GET', 'media/' . $id . '/available-formats');
    }

    /**
     * Establece el formato por defecto de un recurso de media.
     *
     * @param  string  $id    UUID del recurso.
     * @param  array<string, mixed>  $data  Payload con el formato a establecer.
     * @return array<string, mixed>
     */
    public function mediaSetDefaultFormat(string $id, array $data): array
    {
        return $this->client->userRequest(
            'POST',
            'media/' . $id . '/available-formats',
            payload: $data
        );
    }

    /**
     * Devuelve las opciones de transformación disponibles para un recurso de media.
     *
     * @param  string  $id  UUID del recurso.
     * @return array<string, mixed>
     */
    public function mediaTransformationOptions(string $id): array
    {
        return $this->client->userRequest('GET', 'media/' . $id . '/request-transformations');
    }

    /**
     * Solicita transformaciones sobre un recurso de media. Alias de {@see requestTransformations()}.
     *
     * @param  string  $id    UUID del recurso.
     * @param  array<string, mixed>  $data  Opciones de transformación.
     * @return array<string, mixed>
     */
    public function mediaRequestTransformations(string $id, array $data): array
    {
        return $this->requestTransformations($id, $data);
    }

    /**
     * Solicita transformaciones sobre un recurso de media.
     *
     * @param  string  $id_media  UUID del recurso.
     * @param  array<string, mixed>  $data     Opciones de transformación.
     * @return array<string, mixed>
     */
    public function requestTransformations(string $id_media, array $data): array
    {
        return $this->client->userRequest(
            'POST',
            'media/' . $id_media . '/request-transformations',
            payload: $data
        );
    }

    /**
     * Asigna metadatos a un recurso de media en bloque.
     *
     * @param  string  $id    UUID del recurso.
     * @param  array<string, mixed>  $data  Payload con los metadatos (metadata[]).
     * @return array<string, mixed>
     */
    public function mediaSetMetadata(string $id, array $data): array
    {
        return $this->client->userRequest(
            'POST',
            'media/' . $id . '/set-metadata',
            payload: $data
        );
    }

    /**
     * Asocia tags a un recurso de media.
     *
     * @param  string  $id    UUID del recurso.
     * @param  array<string, mixed>  $data  Payload con los tags (tags[]).
     * @return array<string, mixed>
     */
    public function mediaTagStore(string $id, array $data): array
    {
        return $this->client->userRequest(
            'POST',
            'media/' . $id . '/tags/store',
            payload: $data
        );
    }

    /**
     * Descarga un recurso de media.
     *
     * @param  string       $id   UUID del recurso.
     * @param  string|null  $ext  Extensión del formato a descargar.
     * @return array<string, mixed>
     */
    public function mediaDownload(string $id, ?string $ext = null): array
    {
        return $this->client->userRequest(
            'GET',
            'media/' . $id . '/download',
            query: array_filter(['ext' => $ext])
        );
    }

    /**
     * Descarga y guarda un recurso de media localmente. Alias de {@see mediaDownload()}.
     *
     * @param  string  $id   UUID del recurso.
     * @param  string  $ext  Extensión del formato a descargar.
     * @return array<string, mixed>
     */
    public function saveMediaLocal(string $id, string $ext): array
    {
        return $this->mediaDownload($id, $ext);
    }
}
