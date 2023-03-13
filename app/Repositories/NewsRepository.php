<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\NewsApi;
use App\Services\NewsServiceInterface;
use App\Services\NewYorkTimes;
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

    public function getNewsFrom(NewsServiceInterface $service, array $filters = []): array
    {
        return $service->news($filters);
    }
}
