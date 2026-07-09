<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $exception): JsonResponse|Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.validation_failed'),
                    'errors' => $exception->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.unauthenticated'),
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($exception instanceof AuthorizationException) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.unauthorized_access'),
                ], Response::HTTP_FORBIDDEN);
            }

            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.route_not_found'),
                ], Response::HTTP_NOT_FOUND);
            }

            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.resource_not_found'),
                ], Response::HTTP_NOT_FOUND);
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.method_not_allowed'),
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }

            if ($exception instanceof UniqueConstraintViolationException) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.duplicate_entry'),
                ], Response::HTTP_CONFLICT);
            }

            if ($exception instanceof QueryException) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.database_error_occurred'),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($exception instanceof CustomException) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'data' => $exception->getData(),
                ], $exception->getStatusCode());
            }
        }

        return parent::render($request, $exception);
    }
}
