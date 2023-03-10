<?php

namespace App\News;

use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

abstract class NewsService implements NewsInterface
{
    protected PendingRequest $client;

    protected $params = [];

    public function __construct()
    {
        $this->client = Http::acceptJson();
    }

    public function withParams(string $key, string $value): static
    {
        $this->params[$key] = $value;

        return $this;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function search(string $keyword): static
    {
        $this->withParams('q', $keyword);

        return $this;
    }

    public function filter(array $filters): static
    {
        if (isset($filters['keyword'])) {
            $this->search($filters['keyword']);
        }

        return $this;
    }

    public function get(): array
    {
        $data = $this->client
            ->get($this->url(), $this->params())
            ->json();

        return $this->format($data);
    }

    public function publicationDate(string $date): string
    {
        $date = Carbon::createFromDate($date);
        $publicationDate = now()->subDay()->lt($date) ? $date->diffForHumans() : $date->isoFormat('MMMM Do, Y');

        return $publicationDate;
    }
}
