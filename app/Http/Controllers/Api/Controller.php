<?php
namespace App\Http\Controllers\Api;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function successResponse($message = '', $data = [], $code = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function errorResponse($message, $data = [], $code = 400): \Illuminate\Http\JsonResponse
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

