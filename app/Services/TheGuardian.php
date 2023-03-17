<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TheGuardian implements NewsServiceInterface
{
    public static function news(array $filters = []): array
    {
        $error = null;
        $data = [];
        $query = static::getQuery($filters);

        $response = Http::acceptJson()
            ->get(config('services.the-guardian.url').'/search', $query)
            ->json();

        if($response['response']['status'] !== 'ok') {
            $error = $response['response']['message'];
        } else {
            $data = (new static)->format($response['response']['results']);
        }
        
        return [$error, $data];
    }

    public static function getQuery(array $filters = []): array {
        $query = [
            'api-key' => config('services.the-guardian.key'),
            'show-fields' => 'thumbnail,byline,trailText,publication',
        ];
        if (isset($filters['keyword'])) {
            $query['q'] = $filters['keyword'];
        }
        if (isset($filters['date'])) {
            $query['use-date'] = 'published';
            $query['from-date'] = formatDate($filters['date'], 'Y-m-d');
            $query['end-date'] = formatDate($filters['date'], 'Y-m-d');
        }
        if (isset($filters['category'])) {
            $query['section'] = $filters['category'];
        }
        if (isset($filters['source'])) {
            $query['publication'] = $filters['source'];
        }

        return $query;
    }

    public function format(array $articles): array
    {
        return collect($articles)->map(function ($article) {
            return [
                'platform' => static::class,
                'title' => $article['webTitle'],
                'publishedAt' => diffForHumans($article['webPublicationDate']),
                'author' => $article['fields']['byline'] ?? null,
                'source' => $article['fields']['publication'] ?? null,
                'category' => $article['sectionName'],
                'description' => $article['fields']['trailText'],
                'cover' => $this->getCover($article),
            ];
        })->toArray();
    }

    private function getCover($article) {
        if(isset($article['fields']['thumbnail'])) {
            return $article['fields']['thumbnail'];
        }

        return "https://images.unsplash.com/photo-1624996379697-f01d168b1a52?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80";
    }

    public static function categories(): array
    {
        $response = Http::acceptJson()
            ->get(config('services.the-guardian.url').'/sections', [
                'api-key' => config('services.the-guardian.key'),
            ])
            ->json();

        return collect($response['response']['results'])->pluck('id')->toArray();
    }

    public static function sources(): array
    {
        return [];
    }
}
