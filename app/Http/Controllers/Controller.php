<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function ok(array $data): Response
    {
        return $this->response(200, 'Success', $data);
    }

    public function error(string $message = null, int $status = 500): Response
    {
        $message = $message ?? 'Server Error, please try again later.';

        return $this->response($status, $message);
    }

    public function response(int $status, string $message, array $data = []): Response
    {
        return response(
            [
                'status' => $status,
                'message' => $message,
                'data' => $data,
            ],
            $status,
        );
        
    }
}
