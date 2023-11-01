<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiBaseController extends Controller
{
    public function returnSuccess($data = [], $code): JsonResponse
    {
        return response()->json([
            'status' => true,
            'code' => $code,
            'data' => $data
        ]);
    }

    public function returnFail(string $message = '', int $code = 0): JsonResponse
    {
        return response()->json([
            'status' => false,
            'code' => $code,
            'message' => $message ?? 'Message'
        ]);
    }
}
