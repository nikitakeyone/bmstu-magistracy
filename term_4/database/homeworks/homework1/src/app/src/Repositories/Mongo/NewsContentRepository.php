<?php

namespace App\Repositories\Mongo;

use App\Config\Database as AppConfigDatabase;
use MongoDB\Client;
use MongoDB\Database as MongoDatabase;
use MongoDB\BSON\UTCDateTime;

class NewsContentRepository
{
    private MongoDatabase $db;
    private \MongoDB\Collection $collection;

    public function __construct()
    {
        $this->db = AppConfigDatabase::getMongoDatabase();
        $this->collection = $this->db->selectCollection('news_content');
    }

    public function findByNewsId(int $newsId): ?array
    {
        $result = $this->collection->findOne(
            ['news_meta_id' => $newsId],
            ['sort' => ['version' => -1]]
        );

        return $result !== null ? (array) $result : null;
    }

    public function create(int $newsId, string $contentHtml, string $contentJson, int $authorId): void
    {
        $this->collection->insertOne([
            'news_meta_id' => $newsId,
            'version' => 1,
            'content_html' => $contentHtml,
            'content_json' => $contentJson,
            'author_id' => $authorId,
            'created_at' => new UTCDateTime()
        ]);
    }

    public function update(int $newsId, string $contentHtml, string $contentJson, int $authorId): void
    {
        $latest = $this->findByNewsId($newsId);
        $version = $latest ? ($latest['version'] + 1) : 1;

        $this->collection->insertOne([
            'news_meta_id' => $newsId,
            'version' => $version,
            'content_html' => $contentHtml,
            'content_json' => $contentJson,
            'author_id' => $authorId,
            'created_at' => new UTCDateTime()
        ]);
    }

    public function deleteByNewsId(int $newsId): void
    {
        $this->collection->deleteMany(['news_meta_id' => $newsId]);
    }

    public function getVersions(int $newsId): array
    {
        $cursor = $this->collection->find(
            ['news_meta_id' => $newsId],
            ['sort' => ['version' => -1]]
        );

        return array_map(fn($doc) => (array) $doc, iterator_to_array($cursor));
    }
}