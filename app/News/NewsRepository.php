<?php

namespace App\News;

use App\Models\User;
use App\News\Services\NewsApi;
use App\News\Services\NewsServiceInterface;
use App\news\Services\NewYorkTimes;
use Exception;
use Illuminate\Support\Collection;

class NewsRepository implements NewsRepositoryInterface
{
    private $services = [];
    
    public function __construct()
    {
        $this->services = [
            new NewsApi,
            new NewYorkTimes,
        ];
    }

    public function getFeed(User $user): array
    {
        return [];
    }

    public function getNews(array $filters = []): array
    {
        $errors = [];
        $news = [];

        foreach ($this->services as $service) {
            [$error, $data] = $this->getNewsFrom($service, $filters);
            if($error) {
                $errors[] = $error;
            }
            
            $news = [...$news, ...$data];
        }
        
        // If ALL services are down
        if(count($errors) === count($this->services)) {
            throw new Exception('Service is not available, please try again later') ;
        }

        return $news;
    }

    public function getNewsFrom(NewsServiceInterface $service, array $filters = [])
    {
        return $service->news($filters);
    }
}
