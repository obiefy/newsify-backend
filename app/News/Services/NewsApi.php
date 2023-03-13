<?php

namespace App\News\Services;

use Illuminate\Support\Facades\Http;

class NewsApi implements NewsServiceInterface
{
    public static function news(array $filters): array
    {
        $error = null;
        $data = [];

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
        
        $urlSuffix = '/top-headlines';
        if(count($filters) > 0) {
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

    public function format(array $articles): array
    {
        return collect($articles)->map(function ($article) {
            return [
                'platform' => static::class,
                'title' => $article['title'],
                'publishedAt' => diffForHumans($article['publishedAt']),
                'author' => $article['author'],
                'source' => $article['source']['name'],
                'description' => $article['description'],
                'cover' => 'FROOM NEWS API',
            ];
        })->toArray();
    }
}
