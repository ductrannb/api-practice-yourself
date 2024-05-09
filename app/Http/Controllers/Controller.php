<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Utils\Messages;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $repository;

    public function responsePaginate(LengthAwarePaginator $paginator, $resourceClass, bool $onlyData = false)
    {
        if ($onlyData) {
            return [
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'data' => $resourceClass::collection($paginator->items())
            ];
        }
        return response()->json([
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
            'data' => $resourceClass::collection($paginator->items())
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

    public function createdSuccess($message = Messages::CREATE_SUCCESS_MESSAGE, $data = [])
    {
        return response()->json(
            ['message' => $message, 'data' => $data],
            Response::HTTP_CREATED
        );
    }

    public function updatedSuccess($message = Messages::UPDATE_SUCCESS_MESSAGE, $data = [])
    {
        return response()->json(
            ['message' => $message, 'data' => $data],
            Response::HTTP_OK
        );
    }

    public function deletedSuccess($message = Messages::DELETE_SUCCESS_MESSAGE)
    {
        return response()->json(
            ['message' => $message],
            Response::HTTP_OK
        );
    }

    public function responseUnauthorized()
    {
        return response()->json(
            ['message' => Messages::PASSWORD_INVALID_MESSAGE],
            Response::HTTP_UNAUTHORIZED
        );
    }

    protected function collectPaginate(Collection $items, $currentPage = null, $perPage = 10) : LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $items->forPage($currentPage ?: 1, $perPage), $items->count(), $perPage, $currentPage ?: 1
        );
    }
}
