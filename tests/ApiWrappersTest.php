<?php

namespace Ometra\Apollo\Proteus\Tests;

use Ometra\Apollo\Proteus\Api\CategoriesApi;
use Ometra\Apollo\Proteus\Api\DirectoriesApi;
use Ometra\Apollo\Proteus\Api\MediaApi;
use Ometra\Apollo\Proteus\Api\MetadataApi;
use Ometra\Apollo\Proteus\Api\PresetsApi;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ApiWrappersTest extends TestCase
{
    /**
     * @param class-string $apiClass
     * @param array<int, mixed> $arguments
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $query
     */
    #[DataProvider('wrapperRoutes')]
    public function testWrapperRoutes(
        string $apiClass,
        string $wrapper,
        array $arguments,
        string $auth,
        string $method,
        string $endpoint,
        array $payload = [],
        array $query = [],
    ): void {
        $client = new RecordingProteusApiClient();
        $api = new $apiClass($client);

        $api->{$wrapper}(...$arguments);

        self::assertSame([
            'auth' => $auth,
            'method' => $method,
            'endpoint' => $endpoint,
            'payload' => $payload,
            'query' => $query,
        ], $client->lastRequest);
    }

    /**
     * @return array<string, array{0: class-string, 1: string, 2: array<int, mixed>, 3: string, 4: string, 5: string, 6?: array<string, mixed>, 7?: array<string, mixed>}>
     */
    public static function wrapperRoutes(): array
    {
        return [
            'categories index' => [CategoriesApi::class, 'categoriesIndex', [['filter' => 'img']], 'app', 'GET', 'categories', [], ['filter' => 'img']],
            'category store' => [CategoriesApi::class, 'categoryStore', [['name' => 'Images']], 'app', 'POST', 'categories', ['name' => 'Images']],
            'category show' => [CategoriesApi::class, 'categoryShow', ['cat-1'], 'app', 'GET', 'categories/cat-1'],
            'category update' => [CategoriesApi::class, 'categoryUpdate', ['cat-1', ['name' => 'Media']], 'app', 'PUT', 'categories/cat-1', ['name' => 'Media']],
            'category delete' => [CategoriesApi::class, 'categoryDelete', ['cat-1'], 'app', 'DELETE', 'categories/cat-1'],

            'directories index' => [DirectoriesApi::class, 'directoriesIndex', [['page' => 2]], 'user', 'GET', 'directories', [], ['page' => 2]],
            'directory create' => [DirectoriesApi::class, 'directoryCreate', ['dir-1'], 'user', 'GET', 'directories/create/dir-1'],
            'directory store' => [DirectoriesApi::class, 'directoryStore', [['name' => 'Assets']], 'user', 'POST', 'directories', ['name' => 'Assets']],
            'directory show' => [DirectoriesApi::class, 'directoryShow', ['dir-1'], 'user', 'GET', 'directories/dir-1'],
            'directory update' => [DirectoriesApi::class, 'directoryUpdate', ['dir-1', ['name' => 'Updated']], 'user', 'PUT', 'directories/dir-1', ['name' => 'Updated']],
            'directory delete' => [DirectoriesApi::class, 'directoryDelete', ['dir-1'], 'user', 'DELETE', 'directories/dir-1'],

            'preset index' => [PresetsApi::class, 'presetIndex', ['dir-1'], 'user', 'GET', 'directories/dir-1/presets'],
            'preset store' => [PresetsApi::class, 'presetStore', ['dir-1', ['name' => 'Default']], 'user', 'POST', 'directories/dir-1/presets', ['name' => 'Default']],
            'preset show' => [PresetsApi::class, 'presetShow', ['dir-1', '7'], 'user', 'GET', 'directories/dir-1/presets/7'],
            'preset update' => [PresetsApi::class, 'presetUpdate', ['dir-1', '7', ['name' => 'New']], 'user', 'PUT', 'directories/dir-1/presets/7', ['name' => 'New']],
            'preset delete' => [PresetsApi::class, 'presetDelete', ['dir-1', '7'], 'user', 'DELETE', 'directories/dir-1/presets/7'],

            'media index' => [MediaApi::class, 'mediaIndex', [['type' => 'image']], 'user', 'GET', 'media', [], ['type' => 'image']],
            'media upload' => [MediaApi::class, 'mediaUpload', [['type' => 'image']], 'user', 'POST', 'media', ['type' => 'image']],
            'media create' => [MediaApi::class, 'mediaCreate', [], 'user', 'GET', 'media/create'],
            'media tags' => [MediaApi::class, 'mediaTags', [], 'user', 'GET', 'media/tags'],
            'media show' => [MediaApi::class, 'mediaShow', ['media-1'], 'user', 'GET', 'media/media-1'],
            'media delete' => [MediaApi::class, 'mediaDelete', ['media-1'], 'user', 'DELETE', 'media/media-1'],
            'media available formats' => [MediaApi::class, 'mediaAvailableFormats', ['media-1'], 'user', 'GET', 'media/media-1/available-formats'],
            'media set default format' => [MediaApi::class, 'mediaSetDefaultFormat', ['media-1', ['format' => 'jpg']], 'user', 'POST', 'media/media-1/available-formats', ['format' => 'jpg']],
            'media transformation options' => [MediaApi::class, 'mediaTransformationOptions', ['media-1'], 'user', 'GET', 'media/media-1/request-transformations'],
            'media request transformations' => [MediaApi::class, 'mediaRequestTransformations', ['media-1', ['transformations' => []]], 'user', 'POST', 'media/media-1/request-transformations', ['transformations' => []]],
            'media set metadata' => [MediaApi::class, 'mediaSetMetadata', ['media-1', ['metadata' => ['k' => 'v']]], 'user', 'POST', 'media/media-1/set-metadata', ['metadata' => ['k' => 'v']]],
            'media tag store' => [MediaApi::class, 'mediaTagStore', ['media-1', ['tags' => ['a']]], 'user', 'POST', 'media/media-1/tags/store', ['tags' => ['a']]],
            'media download' => [MediaApi::class, 'mediaDownload', ['media-1', 'jpg'], 'user', 'GET', 'media/media-1/download', [], ['ext' => 'jpg']],

            'metadata keys' => [MetadataApi::class, 'metadataKeys', ['author'], 'app', 'GET', 'media/metadata/author'],
            'metadata values' => [MetadataApi::class, 'metadataValuesFromKey', ['author'], 'app', 'GET', 'media/metadata/author/values'],
            'metadata index' => [MetadataApi::class, 'metadataIndex', ['media-1', ['page' => 1]], 'user', 'GET', 'media/media-1/metadata', [], ['page' => 1]],
            'metadata store' => [MetadataApi::class, 'metadataStore', ['media-1', ['key' => 'author']], 'user', 'POST', 'media/media-1/metadata', ['key' => 'author']],
            'metadata update' => [MetadataApi::class, 'metadataUpdate', ['media-1', ['key' => 'author']], 'user', 'PUT', 'media/media-1/metadata', ['key' => 'author']],
            'metadata show' => [MetadataApi::class, 'metadataShow', ['media-1', 'author'], 'user', 'GET', 'media/media-1/metadata/author'],
            'metadata delete' => [MetadataApi::class, 'metadataDelete', ['media-1', 'author'], 'user', 'DELETE', 'media/media-1/metadata/author'],
        ];
    }
}
