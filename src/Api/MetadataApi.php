<?php

namespace Ometra\Apollo\Proteus\Api;

class MetadataApi
{
    public function __construct(
        protected ProteusApiClient $client,
    ) {
    }

    public function metadataKeys(string $key): array
    {
        return $this->client->applicationRequest('GET', 'media/metadata/' . $key);
    }

    public function metadataValuesFromKey(string $key): array
    {
        return $this->client->applicationRequest('GET', 'media/metadata/' . $key . '/values');
    }

    public function metadataIndex(string $id, array $data = []): array
    {
        return $this->client->userRequest('GET', 'media/' . $id . '/metadata', query: $data);
    }

    public function metadataShow(string $id, string $key): array
    {
        return $this->client->userRequest('GET', 'media/' . $id . '/metadata/' . $key);
    }

    public function metadataStore(string $id, array $data): array
    {
        return $this->client->userRequest('POST', 'media/' . $id . '/metadata', payload: $data);
    }

    public function metadataUpdate(string $id, array $data): array
    {
        return $this->client->userRequest('PUT', 'media/' . $id . '/metadata', payload: $data);
    }

    public function metadataDelete(string $id, string $key): ?array
    {
        return $this->client->userRequest('DELETE', 'media/' . $id . '/metadata/' . $key);
    }
}
