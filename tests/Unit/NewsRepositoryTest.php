<?php

namespace Tests\Feature;

use App\Services\NewsApi;
use App\Services\NewYorkTimes;
use App\Repositories\NewsRepository;
use Tests\TestCase;

class NewsRepositoryTest extends TestCase
{
    public function test_it_retrieves_news_from_the_repository(): void
    {
        $repository = new NewsRepository;
        $news = $repository->getNews();
        
        $this->assertNotEmpty($news);
        $this->assertCount(20, $news);
        $this->assertArrayHasKey('title', $news[0]);
        $this->assertArrayHasKey('source', $news[0]);
    }

    public function test_it_retrieves_news_from_news_api_service(): void
    {
        $repository = new NewsRepository;
        [$error, $news] = $repository->getNewsFrom(new NewsApi);
        
        $this->assertNull($error);
        $this->assertNotEmpty($news);
        $this->assertCount(10, $news);
        $this->assertArrayHasKey('title', $news[0]);
        $this->assertArrayHasKey('source', $news[0]);
    }

    public function test_it_retrieves_news_from_new_york_times_service(): void
    {
        $repository = new NewsRepository;
        [$error, $news] = $repository->getNewsFrom(new NewYorkTimes);
        
        $this->assertNull($error);
        $this->assertNotEmpty($news);
        $this->assertCount(10, $news);
        $this->assertArrayHasKey('title', $news[0]);
        $this->assertArrayHasKey('source', $news[0]);
    }

}
