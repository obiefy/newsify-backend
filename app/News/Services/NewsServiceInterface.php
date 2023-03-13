<?php

namespace App\News\Services;

interface NewsServiceInterface
{
    public static function news(array $filters): array;

    public function format(array $articles): array;
}
