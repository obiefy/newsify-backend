<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\NewsApi;
use App\Services\NewsServiceInterface;
use App\Services\NewYorkTimes;
use App\Services\TheGuardian;
use Exception;

class NewsRepository implements NewsRepositoryInterface
{
    private $services = [];
    
    public function __construct()
    {
        $this->services = [
            new NewsApi,
            new NewYorkTimes,
            new TheGuardian,
        ];
    }

    public function getFeed(User $user): array
    {
        return [];
    }

    public function getFilters(): array
    {
        [$categories, $sources] = cache()->remember('categories', now()->addDay(), function () {
            $categories = [];
            $sources = [];
            foreach ($this->services as $service) {
                $categories = [...$categories, ...$service->categories()];
                $sources = [...$sources, ...$service->sources()];
            }

            return [$categories, $sources];
        });

        return [
            'categories' => collect($categories)->unique()->values()->toArray(),
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
