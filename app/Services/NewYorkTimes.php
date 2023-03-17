<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewYorkTimes implements NewsServiceInterface
{
    public static function news(array $filters = []): array
    {
        $error = null;
        $data = [];
        $query = static::getQuery($filters);

        $response = Http::acceptJson()
            ->get(config('services.new-york-times.url').'/articlesearch.json', $query)
            ->json();

        if($response['status'] !== 'OK') {
            $error = $response['fault']['faultstring'] ?? $response['status'];
        } else {
            $data = (new static)->format($response['response']['docs']);
        }

        return [$error, $data];
    }

    public static function getQuery(array $filters = []): array {
        $query = [
            'api-key' => config('services.new-york-times.key'),
        ];
        $fq = [];
        if (isset($filters['keyword'])) {
            $query['q'] = $filters['keyword'];
        }
        if (isset($filters['date'])) {
            $start = formatDate($filters['date']);
            $end = formatDate($filters['date']);

            $fq[] = "pub_date:[{$start} TO {$end}]";
        }
        if (isset($filters['category'])) {
            $categories = str_replace(',', '" "', $filters['category']);
            $fq[] = 'news_desk:("'. $categories.'")';
        }
        if (isset($filters['source'])) {
            $sources = str_replace(',', '" "', $filters['source']);

            $fq[] = 'source:("'. $sources .'")';
        }
        $query['fq'] = implode(' OR ', $fq);

        return $query;
    }

    public function format(array $articles): array
    {
        return collect($articles)->map(function ($article) {
            return [
                'platform' => static::class,
                'title' => $article['headline']['main'],
                'publishedAt' => diffForHumans($article['pub_date']),
                'author' => str($article['byline']['original'])->replace('By ', '')->toString(),
                'source' => $article['source'] ?? null,
                'category' => $article['news_desk'],
                'description' => $article['snippet'],
                'cover' => $this->getCover($article),
            ];
        })->toArray();
    }

    private function getCover($article) {
        if(!isset($article['multimedia'][0]['urlss'])) {
            return "https://images.unsplash.com/photo-1624996379697-f01d168b1a52?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80";
        }

        return config('services.new-york-times.url').'/articlesearch.json/get/'.$article['multimedia'][0]['url'].'?api-key='.config('services.new-york-times.key');
    }

    public static function categories(): array
    {
        return [ 
            "Adventure Sports", "Arts & Leisure", "Arts", "Automobiles", "Blogs", "Books", "Booming", 
            "Business Day", "Business", "Cars", "Circuits", "Classifieds", "Connecticut", 
            "Crosswords & Games", "Culture", "DealBook", "Dining", "Editorial", "Education", 
            "Energy", "Entrepreneurs", "Environment", "Escapes", "Fashion & Style", "Fashion", 
            "Favorites", "Financial", "Flight", "Food", "Foreign", "Generations", "Giving", 
            "Global Home", "Health & Fitness", "Health", "Home & Garden", "Home", "Jobs", "Key", 
            "Letters", "Long Island", "Magazine", "Market Place", "Media", "Men's Health", "Metro",
            "Metropolitan", "Movies", "Museums", "National", "Nesting", "Obits", "Obituaries", "Obituary",
            "OpEd", "Opinion", "Outlook", "Personal Investing", "Personal Tech", "Play", "Politics", 
            "Regionals", "Retail", "Retirement", "Science", "Small Business", "Society", "Sports", "Style",
            "Sunday Business", "Sunday Review", "Sunday Styles", "T Magazine", "T Style", "Technology", 
            "Teens", "Television", "The Arts", "The Business of Green", "The City Desk", "The City", 
            "The Marathon", "The Millennium", "The Natural World", "The Upshot", "The Weekend", 
            "The Year in Pictures", "Theater", "Then & Now", "Thursday Styles", "Times Topics", 
            "Travel", "U.S.", "Universal", "Upshot", "UrbanEye", "Vacation", "Washington", "Wealth", 
            "Weather", "Week in Review", "Week", "Weekend", "Westchester", "Wireless Living", 
            "Women's Health", "Working", "Workplace", "World", "Your Money",
        ];
    }

    public static function sources(): array
    {
        return [];
    }
}
