<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\NewsApi;
use App\Services\NewsServiceInterface;
use App\Services\NewYorkTimes;
use Exception;

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

    public function getFilters(): array
    {
        $categories = [];
        $sources = [];

        foreach ($this->services as $service) {
            $categories = [...$categories, ...$service->categories()];
            $sources = [...$sources, ...$service->sources()];
        }

        return [
            'categories' => collect($categories)->unique()->toArray(),
            'sources' => collect($sources)->unique()->toArray(),
        ];
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

        return collect($news)->shuffle()->toArray();
    }

    public function getNewsFrom(NewsServiceInterface $service, array $filters = []): array
    {
        return $service->news($filters);
    }
}
