<?php

namespace App\Services;

use App\Models\News;
use App\Repositories\Postgres\NewsMetaRepository;
use App\Repositories\Postgres\CategoryRepository;
use App\Repositories\Mongo\NewsContentRepository;

class NewsService
{
    private NewsMetaRepository $metaRepo;
    private CategoryRepository $categoryRepo;
    private NewsContentRepository $contentRepo;

    public function __construct(
        NewsMetaRepository $metaRepo,
        CategoryRepository $categoryRepo,
        NewsContentRepository $contentRepo
    ) {
        $this->metaRepo = $metaRepo;
        $this->categoryRepo = $categoryRepo;
        $this->contentRepo = $contentRepo;
    }

    public function getPublishedNews(int $limit = 10, int $offset = 0): array
    {
        $newsList = $this->metaRepo->findAll($limit, $offset);

        return array_map(function($news) {
            $model = new News($news);
            $content = $this->contentRepo->findByNewsId($model->id);
            if ($content) {
                $model->content_html = $content['content_html'] ?? '';
                $model->content_json = $content['content_json'] ?? '{}';
            }
            return $model;
        }, $newsList);
    }

    public function getNewsBySlug(string $slug): ?News
    {
        $meta = $this->metaRepo->findBySlug($slug);
        if (!$meta) {
            return null;
        }

        $news = new News($meta);
        $content = $this->contentRepo->findByNewsId($news->id);

        if ($content) {
            $news->content_html = $content['content_html'] ?? '';
            $news->content_json = $content['content_json'] ?? '{}';
        }

        return $news;
    }

    public function createNews(array $data, int $authorId): int
    {
        $metaData = [
            'title' => $data['title'],
            'slug' => $this->generateSlug($data['title']),
            'excerpt' => $data['excerpt'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'author_id' => $authorId,
            'status' => $data['status'] ?? 'draft',
            'published_at' => ($data['status'] === 'published') ? date('Y-m-d H:i:s') : null
        ];

        $newsId = $this->metaRepo->create($metaData);

        $this->contentRepo->create(
            $newsId,
            $data['content_html'] ?? '',
            $data['content_json'] ?? '{}',
            $authorId
        );

        return $newsId;
    }

    public function updateNews(int $id, array $data, int $authorId): bool
    {
        $metaData = [
            'title' => $data['title'],
            'slug' => $this->generateSlug($data['title']),
            'excerpt' => $data['excerpt'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'status' => $data['status'] ?? 'draft',
        ];

        if ($data['status'] === 'published') {
            $metaData['published_at'] = date('Y-m-d H:i:s');
        }

        $this->metaRepo->update($id, $metaData);

        $this->contentRepo->update(
            $id,
            $data['content_html'] ?? '',
            $data['content_json'] ?? '{}',
            $authorId
        );

        return true;
    }

    public function deleteNews(int $id): bool
    {
        $this->contentRepo->deleteByNewsId($id);
        return $this->metaRepo->delete($id);
    }

    public function getAllNewsForAdmin(): array
    {
        return $this->metaRepo->findAllForAdmin();
    }

    public function getCategories(): array
    {
        return $this->categoryRepo->findAll();
    }

    private function generateSlug(string $title): string
    {
        $slug = $this->transliterate($title);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9_-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        $baseSlug = $slug;
        $counter = 1;

        while ($this->metaRepo->findBySlug($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function transliterate(string $text): string
    {
        $converter = [
            'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',
            'д' => 'd',    'е' => 'e',    'ё' => 'yo',   'ж' => 'zh',
            'з' => 'z',    'и' => 'i',    'й' => 'y',    'к' => 'k',
            'л' => 'l',    'м' => 'm',    'н' => 'n',    'о' => 'o',
            'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
            'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'ts',
            'ч' => 'ch',   'ш' => 'sh',   'щ' => 'sch',  'ъ' => '',
            'ы' => 'y',    'ь' => '',     'э' => 'e',    'ю' => 'yu',
            'я' => 'ya',

            'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',
            'Д' => 'D',    'Е' => 'E',    'Ё' => 'Yo',   'Ж' => 'Zh',
            'З' => 'Z',    'И' => 'I',    'Й' => 'Y',    'К' => 'K',
            'Л' => 'L',    'М' => 'M',    'Н' => 'N',    'О' => 'O',
            'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
            'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'Ts',
            'Ч' => 'Ch',   'Ш' => 'Sh',   'Щ' => 'Sch',  'Ъ' => '',
            'Ы' => 'Y',    'Ь' => '',     'Э' => 'E',    'Ю' => 'Yu',
            'Я' => 'Ya',

            '№' => '',     '&' => 'and',  '@' => 'at',
        ];

        return strtr($text, $converter);
    }
}