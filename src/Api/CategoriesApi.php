<?php

namespace Ometra\Apollo\Proteus\Api;

class CategoriesApi
{
    public function __construct(
        protected ProteusApiClient $client,
    ) {
    }

    public function categoriesIndex(): array
    {
        return $this->client->applicationRequest('GET', 'categories');
    }

    public function categoryStore(array $data): array
    {
        return $this->client->applicationRequest('POST', 'categories', payload: $data);
    }

    public function categoryUpdate(string $id, array $data): array
    {
        return $this->client->applicationRequest('PUT', 'categories/' . $id, payload: $data);
    }

    public function categoryDelete(string $id): ?array
    {
        return $this->client->applicationRequest('DELETE', 'categories/' . $id);
    }

    public function categoryShow(string $id): array
    {
        return $this->client->applicationRequest('GET', 'categories/' . $id);
    }
}
