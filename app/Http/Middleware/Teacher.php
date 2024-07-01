<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Utils\Messages;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Teacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json(['message' => Messages::UNAUTHORIZED_MESSAGE], Response::HTTP_UNAUTHORIZED);
        }
        if (auth()->user()->role_id !== User::ROLE_TEACHER && auth()->user()->role_id !== User::ROLE_ADMIN) {
            return response()->json(['message' => 'You do not have access'], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
