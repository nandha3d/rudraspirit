<?php

namespace App\Exceptions\Api\V3;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ThrottleRequestsException;

/**
 * V3 API Exception Handler
 *
 * Converts all exceptions into the V3 JSON envelope format.
 * Register this in App\Exceptions\Handler for V3 routes.
 *
 * NEVER returns HTML. NEVER exposes stack traces in production.
 */
class ApiExceptionHandler
{
    /**
     * Determine if this handler should handle the given request.
     */
    public static function shouldHandle(Request $request): bool
    {
        return str_starts_with($request->path(), 'api/v3');
    }

    /**
     * Render an exception as a V3 JSON response.
     */
    public static function render(\Throwable $e, Request $request): JsonResponse
    {
        return match (true) {
            $e instanceof ValidationException          => static::validationError($e),
            $e instanceof AuthenticationException      => static::error('UNAUTHORIZED', 'Authentication required.', 401),
            $e instanceof AuthorizationException       => static::error('FORBIDDEN', 'You do not have permission to perform this action.', 403),
            $e instanceof ModelNotFoundException       => static::error('NOT_FOUND', static::modelNotFoundMessage($e), 404),
            $e instanceof NotFoundHttpException        => static::error('NOT_FOUND', 'The requested endpoint does not exist.', 404),
            $e instanceof MethodNotAllowedHttpException => static::error('METHOD_NOT_ALLOWED', 'HTTP method not allowed for this endpoint.', 405),
            $e instanceof ThrottleRequestsException    => static::error('RATE_LIMITED', 'Too many requests. Please try again later.', 429),
            $e instanceof HttpException                => static::error('HTTP_ERROR', $e->getMessage() ?: 'HTTP error.', $e->getStatusCode()),
            default                                    => static::serverError($e),
        };
    }

    /**
     * Format validation errors.
     */
    private static function validationError(ValidationException $e): JsonResponse
    {
        $errors = [];
        foreach ($e->errors() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'code'    => 'VALIDATION_FAILED',
                    'status'  => 422,
                    'field'   => $field,
                    'message' => $message,
                ];
            }
        }

        return static::response(false, null, $errors, 422);
    }

    /**
     * Format a single error.
     */
    private static function error(string $code, string $message, int $status): JsonResponse
    {
        return static::response(false, null, [[
            'code'    => $code,
            'status'  => $status,
            'message' => $message,
        ]], $status);
    }

    /**
     * Format a 500 server error. Hides details in production.
     */
    private static function serverError(\Throwable $e): JsonResponse
    {
        $message = app()->environment('production')
            ? 'An unexpected error occurred.'
            : $e->getMessage();

        // Log the full error for debugging
        \Log::error('V3 API Error', [
            'exception' => get_class($e),
            'message'   => $e->getMessage(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
        ]);

        return static::response(false, null, [[
            'code'    => 'SERVER_ERROR',
            'status'  => 500,
            'message' => $message,
        ]], 500);
    }

    /**
     * Build the V3 envelope response.
     */
    private static function response(bool $success, $data, array $errors, int $status): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'data'    => $data,
            'meta'    => [
                'timestamp' => now()->toIso8601String(),
                'version'   => config('headless.version', '3.0'),
            ],
            'errors'  => $errors,
        ], $status);
    }

    /**
     * Generate a user-friendly message from ModelNotFoundException.
     */
    private static function modelNotFoundMessage(ModelNotFoundException $e): string
    {
        $model = class_basename($e->getModel());

        return "The requested {$model} was not found.";
    }
}
