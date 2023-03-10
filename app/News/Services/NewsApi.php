<?php

namespace App\News\Services;

use App\News\NewsService;
use Carbon\Carbon;

class NewsApi extends NewsService {

    public static function make(): static {
        return (new static)
            ->withParams('apiKey', config('services.news-api.key'))
            ->withParams('language', 'en')
            ->withParams('pageSize', 10);
    }

    public function url(): string {
        return config('services.news-api.url').'/everything';
    }

    public function format(array $data): array
    {
        if(!isset($data['articles'])) {
            return [];
        }

        return collect($data['articles'])->map(function ($article) {
            return [
                'platform' => static::class,
                'title' => $article['title'],
                'publishedAt' => $this->publicationDate($article['publishedAt']),
                'author' => $article['author'],
                'source' => $article['source']['name'],
                'description' => $article['description'],
                'cover' => "FROOM NEWS API",
            ];
        })->toArray();
    }
}