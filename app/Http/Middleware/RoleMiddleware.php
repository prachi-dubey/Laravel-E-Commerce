<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthenticated'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        $allowedRoles = array_map(
            fn (string $role) => UserRole::from($role)->value,
            $roles
        );

        if (! in_array($user->role->value, $allowedRoles, true)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
