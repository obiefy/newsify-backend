<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsApi implements NewsServiceInterface
{
    public static function news(array $filters = []): array
    {
        $error = null;
        $data = [];

        $query = static::getQuery($filters);
        
        $urlSuffix = '/top-headlines';
        if(isset($filter['keyword'])) {
            $urlSuffix = '/everything';
        }

        $response = Http::acceptJson()
            ->get(config('services.news-api.url').$urlSuffix, $query)
            ->json();

        if($response['status'] !== 'ok') {
            $error = $response['message'];
        } else {
            $data = (new static)->format($response['articles']);
        }

        return [$error, $data];
    }

    public static function getQuery(array $filters): array
    {
        $query = [
            'apiKey' => config('services.news-api.key'),
            'language' =>  'en',
            'pageSize' =>  10,
        ];
        if (isset($filters['keyword'])) {
            $query['q'] = $filters['keyword'];
        }
        if (isset($filters['date'])) {
            $query['from'] = formatDate($filters['date']);
            $query['to'] = formatDate($filters['date']);
        }
        if (isset($filters['category'])) {
            $query['category'] = $filters['category'];
        }
        if (isset($filters['source'])) {
            $query['sources'] = $filters['source'];
        }

        return $query;
    }

    public function format(array $articles): array
    {
        return collect($articles)->map(function ($article) {
            return [
                'platform' => static::class,
                'title' => $article['title'],
                'publishedAt' => diffForHumans($article['publishedAt']),
                'author' => $article['author'],
                'source' => $article['source']['name'],
                'category' => $article['category'] ?? null,
                'description' => $article['description'],
                'cover' => $article['urlToImage'] ?? "https://images.unsplash.com/photo-1624996379697-f01d168b1a52?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80",
            ];
        })->toArray();
    }

    public static function categories(): array
    {
        return [
            "business", "entertainment", "general", "health", "science", "sports", "technology",
        ];
    }

    public static function sources(): array
    {
        $response = Http::acceptJson()
            ->get(config('services.news-api.url').'/top-headlines/sources', [
                'apiKey' => config('services.news-api.key'),
            ])
            ->json();
        
        return collect($response['sources'])->pluck('name')->toArray();
    }
}
