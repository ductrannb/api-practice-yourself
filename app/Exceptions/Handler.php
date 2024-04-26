<?php

namespace App\Exceptions;

use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }
            if ($e instanceof TokenExpiredException) {
                $token = JWTAuth::refresh(JWTAuth::getToken());
                return response()->json(
                    ['message' => 'Token expired', 'access_token' => $token],
                    Response::HTTP_NOT_ACCEPTABLE
                );
            }
            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => $e->validator->errors()->first(),
                    'errors' => $e->validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json(['message' => 'Method not allowed'], Response::HTTP_METHOD_NOT_ALLOWED);
            }
            if ($e->getPrevious() instanceof RecordsNotFoundException) {
                return response()->json(['message' => 'Record not found'], Response::HTTP_NOT_ACCEPTABLE);
            }
            info($e);
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }
}
