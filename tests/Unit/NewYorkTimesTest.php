<?php

namespace Tests\Feature;

use App\Repositories\NewsRepository;
use App\Services\NewYorkTimes;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewYorkTimesTest extends TestCase
{
    protected function setUp(): void {
        parent::setUp();
        Http::fake(function () {
            $mockedData = file_get_contents(base_path('/tests/mocks/new-york-times-articles.json'));
            return Http::response($mockedData, 200);
        });
    }

    public function test_it_retrieves_news_without_filters(): void
    {
        [$error, $news] = NewYorkTimes::news();
        Http::assertSent(function (Request $request) {
            $query = [
                'api-key' => config('services.new-york-times.key'),
            ];
            $url = config('services.new-york-times.url').'/articlesearch.json?'.http_build_query($query);
            return $request->url() == $url;
        });
    }

    public function test_it_retrieves_news_with_filters(): void
    {
        $filters = [
            'keyword' => 'Turkey',
            'date' => '2023-03-13',
        ];
        [$error, $news] = NewYorkTimes::news($filters);
        
        Http::assertSent(function (Request $request) use ($filters) {
            $query = [
                'api-key' => config('services.new-york-times.key'),
                'q' => $filters['keyword'],
                'begin_date' => $filters['date'],
                'end_date' => $filters['date'],
            ];
            $url = config('services.new-york-times.url').'/articlesearch.json?'.http_build_query($query);
            // dd($url, $request->url());
            return $request->url() == $url;
        });
    }

}
