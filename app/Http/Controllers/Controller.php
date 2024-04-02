<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function response($message = '', $data = [], $status = 200)
    {
        return response()->json(
            ['message' => $message, 'data' => $data],
            $status
        );
    }

    public function responseOk($message = '', $data = [])
    {
        return response()->json(
            ['message' => $message, 'data' => $data],
            Response::HTTP_OK
        );
    }

    public function responseError($message = '', $data = [], $errorCode = 500)
    {
        return response()->json(
            ['message' => $message, 'data' => $data],
            $errorCode
        );
    }

    public function createdSuccess($message = 'Created successfully', $data = [])
    {
        return response()->json(
            ['message' => $message, 'data' => $data],
            Response::HTTP_CREATED
        );
    }

    public function deletedSuccess($message = 'Deleted successfully')
    {
        return response()->json(
            ['message' => $message],
            Response::HTTP_NO_CONTENT
        );
    }

    public function responseUnauthorized()
    {
        return response()->json(
            ['message' => 'Unauthorized'],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
