<?php

namespace Ometra\Apollo\Proteus\Api;

class MediaApi
{
    public function __construct(
        protected ProteusApiClient $client,
    ) {
    }

    public function mediaIndex(array $data = []): array
    {
        return $this->client->userRequest('GET', 'media', query: $data);
    }

    public function mediaCreate(): array
    {
        return $this->client->userRequest('GET', 'media/create');
    }

    public function mediaTags(): array
    {
        return $this->client->userRequest('GET', 'media/tags');
    }

    public function mediaShow(string $id): array
    {
        return $this->client->userRequest('GET', 'media/' . $id);
    }

    public function mediaUpload(array $data): array
    {
        return $this->uploadFile($data);
    }

    public function uploadFile(array $data): array
    {
        return $this->client->userRequest('POST', 'media', payload: $data);
    }

    public function mediaDelete(string $id): ?array
    {
        return $this->client->userRequest('DELETE', 'media/' . $id);
    }

    public function mediaAvailableFormats(string $id): array
    {
        return $this->client->userRequest('GET', 'media/' . $id . '/available-formats');
    }

    public function mediaSetDefaultFormat(string $id, array $data): array
    {
        return $this->client->userRequest(
            'POST',
            'media/' . $id . '/available-formats',
            payload: $data
        );
    }

    public function mediaTransformationOptions(string $id): array
    {
        return $this->client->userRequest('GET', 'media/' . $id . '/request-transformations');
    }

    public function mediaRequestTransformations(string $id, array $data): array
    {
        return $this->requestTransformations($id, $data);
    }

    public function requestTransformations(string $id_media, array $data): array
    {
        return $this->client->userRequest(
            'POST',
            'media/' . $id_media . '/request-transformations',
            payload: $data
        );
    }

    public function mediaSetMetadata(string $id, array $data): array
    {
        return $this->client->userRequest(
            'POST',
            'media/' . $id . '/set-metadata',
            payload: $data
        );
    }

    public function mediaTagStore(string $id, array $data): array
    {
        return $this->client->userRequest(
            'POST',
            'media/' . $id . '/tags/store',
            payload: $data
        );
    }

    public function mediaDownload(string $id, ?string $ext = null): array
    {
        return $this->client->userRequest(
            'GET',
            'media/' . $id . '/download',
            query: array_filter(['ext' => $ext])
        );
    }

    public function saveMediaLocal(string $id, string $ext): array
    {
        return $this->mediaDownload($id, $ext);
    }
}
