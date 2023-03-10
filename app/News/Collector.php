<?php 

namespace App\News;

use App\Models\User;
use App\News\Services\NewsApi;
use App\news\Services\NewYorkTimes;

class Collector {
    CONST SERVICES = [
        NewsApi::class,
        NewYorkTimes::class,
    ];

    public static function feed(User $user = null): array {
        return collect(NewsApi::make()->search('Sudan')->get())
            ->merge(NewYorkTimes::make()->search('Sudan')->get())
            ->all();
    }

    public static function filter(array $filters): array {
        $news = [];

        foreach(static::SERVICES as $service) {
            $data = $service::make()->filter($filters)->get();
            $news = [...$news, ...$data];
        }
        return $news;
        
    }

}