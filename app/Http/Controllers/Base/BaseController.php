<?php

namespace App\Http\Controllers\Base;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $statusCode = 200;

    /**
     * Status code getter
     *
     * @return int
     */

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Status code setter
     *
     * @param $code int
     * @return $this
     */

    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this->statusCode;
    }

    /**
     * Response client
     *
     * @param array $content Response data
     * @param array $headers Response headers
     * @return mixed
     */

    public function respond($content = [], $headers = [])
    {
        $result = [];
        $result['status'] = data_get($content, 'status', true);

        if (isset($content['data'])) {
            $result['data'] = $content['data'];
        }

        if (isset($content['message'])) {
            $result['message'] = $content['message'];
        }

        if (isset($content['paging'])) {
            $result['paging'] = $content['paging'];
        }

        return response($result, !empty($content['responseCode']) ? $content['responseCode'] : $this->getStatusCode())->withHeaders($headers);
    }

    /**
     * Response Success
     *
     * @param array $content response data
     */

    public function respondSuccess($content = [], $code)
    {
        return response($content, $code);
    }
    /**
     * 400 - The request was invalid
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function respondBadRequest($message = 'the request was invalid', $code = 400)
    {
        return $this->setStatusCode($code)->respondWithError($message);
    }



    /**
     * Response 500
     *
     * @param string $message
     * @return mixed
     */
    public function respondInternalError($message = 'Interval server')
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }

    /**
     * Response with error.
     *
     * @param string $message
     * @param int $code
     * @return mixed
     */
    public function respondWithError($message, $code = null)
    {
        $data = [
            'error' => [
                'message' => $message,
            ],
        ];

        if ($code) {
            $data['error']['code'] = $code;
        }

        return $this->respond($data);
    }

    /**
     * Convert format error.
     * @param object $data
     * @return mixed
     */
    public function convertFormatError($data)
    {
        return $data->toArray();
    }

    public function returnFalse($message, $resCode = 400)
    {
        $data = ['status' => false, 'message' => $message];
        return response($data, $resCode);
    }

    public function returnTrue($content = '', $message = null, $resCode = 200)
    {
        $data = ['status' => true, 'data' => $content, 'message' => $message];
        return response($data, $resCode);
    }
}
