<?php

namespace Ometra\Apollo\Proteus\Api;

class DirectoriesApi
{
    public function __construct(
        protected ProteusApiClient $client,
    ) {
    }

    public function directoriesIndex(array $data = []): array
    {
        return $this->client->userRequest('GET', 'directories', query: $data);
    }

    public function directoryCreate(?string $parentId = null): array
    {
        $endpoint = 'directories/create';

        if ($parentId !== null && $parentId !== '') {
            $endpoint .= '/' . $parentId;
        }

        return $this->client->userRequest('GET', $endpoint);
    }

    public function directoryStore(array $data): array
    {
        return $this->client->userRequest('POST', 'directories', payload: $data);
    }

    public function directoryShow(string $id): array
    {
        return $this->client->userRequest('GET', 'directories/' . $id);
    }

    public function directoryDelete(string $id): ?array
    {
        return $this->client->userRequest('DELETE', 'directories/' . $id);
    }

    public function directoryUpdate(string $id, array $data): array
    {
        return $this->client->userRequest('PUT', 'directories/' . $id, payload: $data);
    }
}
