<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Utils\Messages;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $repository;

    public function responsePaginate(LengthAwarePaginator $paginator)
    {
        return response()->json([
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
            'data' => UserResource::collection($paginator->items())
        ]);
    }

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
            ['message' => Messages::PASSWORD_INVALID_MESSAGE],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
