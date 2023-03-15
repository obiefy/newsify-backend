<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\NewsServiceInterface;

interface NewsRepositoryInterface
{
    public function getFeed(User $user): array;

    public function getFilters(): array;

    public function getNews(array $filters = []): array;

    public function getNewsFrom(NewsServiceInterface $service, array $filters = []): array;
}
