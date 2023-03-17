<?php

namespace App\Http\Controllers;

use App\Repositories\NewsRepositoryInterface;
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

    public function feed(Request $request)
    {
        try {
            $filters = [];
            $user = $request->user();
            if($user->categories) {
                $filters['category'] = implode(',', $user->categories);
            }
            if($user->sources) {
                $filters['source'] = implode(',', $user->sources);
            }
            $news = $this->repository->getNews($filters);

            return $this->ok($news);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function filters()
    {
        try {

            $filters = $this->repository->getFilters();

            return $this->ok($filters);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
