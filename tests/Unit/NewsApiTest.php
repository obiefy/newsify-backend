<?php

namespace Tests\Feature;

use App\Repositories\NewsRepository;
use App\Services\NewsApi;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewsApiTest extends TestCase
{
    protected function setUp(): void {
        parent::setUp();
        Http::fake(function () {
            $mockedData = file_get_contents(base_path('/tests/mocks/news-api-articles.json'));
            return Http::response($mockedData, 200);
        });
    }

    public function test_it_retrieves_news_without_filters(): void
    {
        [$error, $news] = NewsApi::news();
        Http::assertSent(function (Request $request) {
            $query = [
                'apiKey' => config('services.news-api.key'),
                'language' => 'en',
                'pageSize' => 10,
            ];
            $url = config('services.news-api.url').'/top-headlines?'.http_build_query($query);
            return $request->url() == $url;
        });
    }

    public function test_it_retrieves_news_with_filters(): void
    {
        $filters = [
            'keyword' => 'Turkey',
            'date' => '2023-03-13',
        ];
        [$error, $news] = NewsApi::news($filters);
        
        Http::assertSent(function (Request $request) use ($filters) {
            $query = [
                'apiKey' => config('services.news-api.key'),
                'language' => 'en',
                'pageSize' => 10,
                'q' => $filters['keyword'],
                'from' => $filters['date'],
                'to' => $filters['date'],
            ];
            $url = config('services.news-api.url').'/everything?'.http_build_query($query);
            return $request->url() == $url;
        });
    }
}
