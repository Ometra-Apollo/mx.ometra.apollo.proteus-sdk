<?php

namespace Ometra\Apollo\Proteus\Api;

class PresetsApi
{
    public function __construct(
        protected ProteusApiClient $client,
    ) {
    }

    public function presetIndex(string $directory_id): array
    {
        return $this->client->userRequest('GET', 'directories/' . $directory_id . '/presets');
    }

    public function presetStore(string $directory_id, array $data): array
    {
        return $this->client->userRequest(
            'POST',
            'directories/' . $directory_id . '/presets',
            payload: $data
        );
    }

    public function presetDelete(string $directory_id, string $preset_id): ?array
    {
        return $this->client->userRequest(
            'DELETE',
            'directories/' . $directory_id . '/presets/' . $preset_id
        );
    }

    public function presetShow(string $directory_id, string $preset_id): array
    {
        return $this->client->userRequest(
            'GET',
            'directories/' . $directory_id . '/presets/' . $preset_id
        );
    }

    public function presetUpdate(string $directory_id, string $preset_id, array $data): array
    {
        return $this->client->userRequest(
            'PUT',
            'directories/' . $directory_id . '/presets/' . $preset_id,
            payload: $data
        );
    }
}
