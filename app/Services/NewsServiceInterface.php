<?php

namespace App\Services;

interface NewsServiceInterface
{
    public static function getQuery(array $filters): array;

    public static function news(array $filters = []): array;

    public function format(array $articles): array;

    public static function categories(): array;

    public static function sources(): array;

}
