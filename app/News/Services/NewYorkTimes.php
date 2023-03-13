<?php

namespace App\news\Services;

use Illuminate\Support\Facades\Http;

class NewYorkTimes implements NewsServiceInterface
{
    public static function news(array $filters): array
    {
        $error = null;
        $data = [];

        $query = [
            'api-key' => config('services.new-york-times.key'),
        ];
        if (isset($filters['keyword'])) {
            $query['q'] = $filters['keyword'];
        }

        $response = Http::acceptJson()
            ->get(config('services.new-york-times.url').'/articlesearch.json', $query)
            ->json();

        if(isset($response['fault']['faultstring'])) {
            $error = $response['fault']['faultstring'];
        } else {
            $data = (new static)->format($response['response']['docs']);
        }

        return [$error, $data];
    }

    public function format(array $articles): array
    {
        return collect($articles)->map(function ($article) {
            return [
                'platform' => static::class,
                'title' => $article['headline']['main'],
                'publishedAt' => formatDate($article['pub_date']),
                'author' => str($article['byline']['original'])->replace('By ', '')->toString(),
                'source' => $article['source'],
                'description' => $article['snippet'],
                'cover' => null,
            ];
        })->toArray();
    }
}
