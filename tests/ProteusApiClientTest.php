<?php

namespace Ometra\Apollo\Proteus\Tests;

use Equidna\BeeHive\Tenancy\TenantContext;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;
use Ometra\Apollo\Proteus\Api\MediaApi;
use Ometra\Apollo\Proteus\Api\ProteusApiClient;
use Ometra\Caronte\Caronte;
use Ometra\Caronte\Support\CaronteApplicationToken;
use PHPUnit\Framework\TestCase;

class ProteusApiClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = new Container();
        Container::setInstance($app);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        $app->instance('config', new Repository([
            'proteus' => [
                'base_url' => 'https://proteus.test/api',
            ],
            'caronte' => [
                'app_cn' => 'proteus-sdk-test',
                'app_secret' => str_repeat('s', 32),
                'tls_verify' => true,
                'http' => [
                    'timeout' => 10,
                    'retries' => 1,
                    'retry_sleep' => 150,
                ],
            ],
        ]));
        $app->instance(Factory::class, new Factory());
        $app->instance(Caronte::class, new class {
            public function getToken(): object
            {
                return new class {
                    public function toString(): string
                    {
                        return 'user-token';
                    }
                };
            }
        });

        $tenantContext = new TenantContext();
        $tenantContext->set('tenant-test');
        $app->instance(TenantContext::class, $tenantContext);

        Http::preventStrayRequests();
        Http::fake([
            'https://proteus.test/api/*' => Http::response([
                'status' => 200,
                'message' => 'ok',
                'data' => ['ok' => true],
                'errors' => [],
            ]),
        ]);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);

        parent::tearDown();
    }

    public function testUserRequestSendsApplicationUserAndTenantHeaders(): void
    {
        $client = new ProteusApiClient();

        $client->userRequest('GET', 'media', query: ['type' => 'image']);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://proteus.test/api/media?type=image'
                && $request->hasHeader('X-Application-Token', CaronteApplicationToken::make())
                && $request->hasHeader('X-User-Token', 'user-token')
                && $request->hasHeader('X-Tenant-Id', 'tenant-test');
        });
    }

    public function testApplicationRequestSendsApplicationAndTenantHeadersOnly(): void
    {
        $client = new ProteusApiClient();

        $client->applicationRequest('GET', 'categories');

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://proteus.test/api/categories'
                && $request->hasHeader('X-Application-Token', CaronteApplicationToken::make())
                && $request->hasHeader('X-Tenant-Id', 'tenant-test')
                && !$request->hasHeader('X-User-Token');
        });
    }

    public function testMediaUploadUsesMultipartForUploadedFiles(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'proteus-sdk-test-');
        file_put_contents($path, 'fake-image');

        $file = new UploadedFile($path, 'avatar.jpg', 'image/jpeg', null, true);
        $api = new MediaApi(new ProteusApiClient());

        $api->mediaUpload([
            'type' => 'image',
            'media' => [$file],
            'metadata' => [
                'source' => 'test',
            ],
        ]);

        Http::assertSent(function (Request $request) {
            $parts = $request->data();

            return $request->url() === 'https://proteus.test/api/media'
                && $request->isMultipart()
                && $this->hasMultipartPart($parts, 'type', 'image')
                && $this->hasMultipartPart($parts, 'metadata[source]', 'test')
                && $this->hasMultipartFile($parts, 'media[]', 'avatar.jpg');
        });
    }

    /**
     * @param array<int, array<string, mixed>> $parts
     */
    private function hasMultipartPart(array $parts, string $name, mixed $contents): bool
    {
        foreach ($parts as $part) {
            if (($part['name'] ?? null) === $name && ($part['contents'] ?? null) === $contents) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, array<string, mixed>> $parts
     */
    private function hasMultipartFile(array $parts, string $name, string $filename): bool
    {
        foreach ($parts as $part) {
            if (($part['name'] ?? null) === $name && ($part['filename'] ?? null) === $filename) {
                return true;
            }
        }

        return false;
    }
}
