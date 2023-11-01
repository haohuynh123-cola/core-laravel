<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ResponseAPI
{
    /**
     * @param null $message
     * @param null $data
     * @param $statusCode
     * @param bool $isSuccess
     * @return \Illuminate\Http\JsonResponse
     */
    public function coreResponse($message = null, $data = null, $statusCode, $isSuccess = true): JsonResponse
    {
        $res = [];
        if ($message) {
            $res['message'] = $message;
        }
        $res['success'] = $isSuccess;
        $res['code'] = $statusCode;
        $res['data'] = (data_get($data, 'data')) ? $data['data'] : (($data && !array_key_exists('data', $data)) ? $data : []);
        if (data_get($data, 'meta')) {
            $res['paging'] = $data['meta']['pagination'];
            unset($data['meta']);
        }
        if (!data_get($res, 'success')) {
            unset($res['data']);
        }
        $array_expect = ['success', 'code', 'message', 'data', 'paging'];
        uksort($res, function ($a, $b) use ($array_expect) {
            $pos_a = array_search($a, $array_expect);
            $pos_b = array_search($b, $array_expect);
            return $pos_a - $pos_b;
        });
        return response()->json($res, $statusCode);
    }

    /**
     * @param $data
     * @param null $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data, $message = null, $statusCode = 200): JsonResponse
    {
        if (data_get($data, 'error')) {
            return $this->coreResponse(data_get($data, 'message'), null, data_get($data, 'statusCode', 400), false);
        }
        if (data_get($data, 'message')) {
            $message = data_get($data, 'message');
            unset($data['message']);
        }
        return $this->coreResponse($message, $data, $statusCode);
    }

    /**
     * @param $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message, $statusCode = 500): JsonResponse
    {
        return $this->coreResponse($message, null, $statusCode, false);
    }
}
