<?php

namespace App\News;

interface NewsInterface
{
    public static function make(): static;

    public function url(): string;

    public function params(): array;

    public function withParams(string $key, string $value): static;

    public function filter(array $filters): static;

    public function search(string $keyword): static;

    public function get(): array;

    public function format(array $articles): array;

    public function publicationDate(string $date): string;
}
