<?php

namespace App\news\Services;

use App\News\NewsService;

class NewYorkTimes extends NewsService
{
    public static function make(): static
    {
        return (new static)->withParams('api-key', config('services.new-york-times.key'));
    }

    public function url(): string
    {
        return config('services.new-york-times.url').'/articlesearch.json';
    }

    public function format(array $data): array
    {
        if (! isset($data['response']['docs'])) {
            return [];
        }

        return collect($data['response']['docs'])->map(function ($article) {
            return [
                'platform' => static::class,
                'title' => $article['headline']['main'],
                'publishedAt' => $this->publicationDate($article['pub_date']),
                'author' => str($article['byline']['original'])->replace('By ', '')->toString(),
                'source' => $article['source'],
                'description' => $article['snippet'],
                'cover' => null,
            ];
        })->toArray();
    }
}
