<?php

namespace App\Http\Controllers;

use App\News\Collector;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function feed()
    {
        try {
            $news = Collector::feed();

            return $this->ok($news);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            $filters = array_filter($request->only(['keyword', 'date', 'category', 'source']));

            $news = Collector::filter($filters);

            return $this->ok($news);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
