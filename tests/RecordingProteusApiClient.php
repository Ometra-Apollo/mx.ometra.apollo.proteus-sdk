<?php

namespace Ometra\Apollo\Proteus\Tests;

use Ometra\Apollo\Proteus\Api\ProteusApiClient;

class RecordingProteusApiClient extends ProteusApiClient
{
    /**
     * @var array{auth: string, method: string, endpoint: string, payload: array, query: array}|null
     */
    public ?array $lastRequest = null;

    public function applicationRequest(
        string $method,
        string $endpoint,
        array $payload = [],
        array $query = [],
    ): array {
        return $this->record('app', $method, $endpoint, $payload, $query);
    }

    public function userRequest(
        string $method,
        string $endpoint,
        array $payload = [],
        array $query = [],
    ): array {
        return $this->record('user', $method, $endpoint, $payload, $query);
    }

    protected function getBaseUrl(): string
    {
        return 'https://proteus.test/api';
    }

    protected function makeApplicationToken(): string
    {
        return 'application-token';
    }

    private function record(
        string $auth,
        string $method,
        string $endpoint,
        array $payload,
        array $query,
    ): array {
        $this->lastRequest = compact('auth', 'method', 'endpoint', 'payload', 'query');

        return [
            'status' => 200,
            'message' => 'ok',
            'data' => $this->lastRequest,
            'errors' => [],
        ];
    }
}
