<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($message, $data, $code = 200, $additional = [], $resourceClass = null)
    {
        $response = [
            'success' => true,
            'code' => $code,
            'message'    => $message,

        ];

        // CASE 1: Paginated data
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $resourceClass = $resourceClass ?? $this->defaultResourceClass ?? null;

            if (!$resourceClass) {
                throw new \Exception('Resource class is required for paginated responses in ' . static::class);
            }

            $collection = $resourceClass::collection($data);

            $response['data'] = $collection->resolve();
            $response['pagination'] = [
                'total'        => $data->total(),
                'count'        => $data->count(),
                'per_page'     => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'from'         => $data->firstItem(),
                'to'           => $data->lastItem(),
            ];
            $response['links'] = [
                'first' => $data->url(1),
                'last'  => $data->url($data->lastPage()),
                'prev'  => $data->previousPageUrl(),
                'next'  => $data->nextPageUrl(),
            ];
        }
        // CASE 2: Resource Collection (manual wrap)
        elseif ($data instanceof \Illuminate\Http\Resources\Json\ResourceCollection) {
            $json = $data->response()->getData(true);
            $response = array_merge($response, $json);
        }
        // CASE 3: Single resource or raw data
        else {
            if ($resourceClass && !($data instanceof \Illuminate\Http\Resources\Json\JsonResource)) {
                $data = new $resourceClass($data);
            }
            $response['data'] = $data instanceof \Illuminate\Http\Resources\Json\JsonResource
                ? $data->resolve()
                : $data;
        }

        return response()->json(array_merge($response, $additional), $code);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = null, $code = 404)
    {
        $response = [
            'success' => false,
            'code' => $code,
            'message' => $error,
            'data' => $errorMessages
        ];


        return response()->json($response, $code);
    }
}
