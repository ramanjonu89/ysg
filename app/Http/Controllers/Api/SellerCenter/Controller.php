<?php
namespace App\Http\Controllers\Api\SellerCenter;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller
{
    public function successResponse($data, $code = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $code);
    }

    public function errorResponse($message, $data = [], $code = 400)
    {
        return response()->json([
            'error' => true,
            'message' => $message,
            'errors' => $data,
        ], $code);
    }

    public function buildResponseFromPagination(LengthAwarePaginator $data): array
    {
        $data = $data->toArray();
        return [
            'items' => $data['data'],
            'pagination' => [
                'total' => $data['total'],
                'per_page' => $data['per_page'],
                'current_page' => $data['current_page'],
                'last_page' => $data['last_page'],
                'next_page_url' => $data['next_page_url'],
                'prev_page_url' => $data['last_page_url'],
                'from' => $data['from'],
                'to' => $data['to']
            ],
        ];
    }
}
