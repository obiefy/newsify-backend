<?php

namespace App\News;

use App\Models\User;
use App\News\Services\NewsServiceInterface;

interface NewsRepositoryInterface
{
    public function getFeed(User $user): array;

    public function getNews(array $filters = []): array;

    public function getNewsFrom(NewsServiceInterface $service, array $filters = []);
}
