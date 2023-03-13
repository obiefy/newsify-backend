<?php

namespace App\Http\Controllers;

use App\News\NewsCollector;
use App\News\NewsRepositoryInterface;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    private NewsRepositoryInterface $repository;
    public function __construct(NewsRepositoryInterface $repository) {
        $this->repository = $repository;
    }
    
    public function index(Request $request)
    {
        try {
            $filters = array_filter($request->only(['keyword', 'date', 'category', 'source']));

            $news = $this->repository->getNews($filters);

            return $this->ok($news);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
