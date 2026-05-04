<?php

namespace App\Http\Controllers\Api\V3;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

/**
 * V3 API Base Controller
 *
 * All V3 controllers MUST extend this class.
 * Provides standardized response helpers that enforce the V3 envelope format.
 *
 * Response Envelope:
 * {
 *   "success": bool,
 *   "data": mixed,
 *   "meta": { "timestamp": string, "version": string, ... },
 *   "errors": array
 * }
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Return a success response with data.
     *
     * @param mixed $data    The response payload
     * @param array $meta    Additional meta fields (merged with defaults)
     * @param int   $status  HTTP status code (200, 201, etc.)
     * @return JsonResponse
     */
    protected function successResponse($data = null, array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => array_merge($this->baseMeta(), $meta),
            'errors'  => [],
        ], $status);
    }

    /**
     * Return a success response with a single resource.
     *
     * @param mixed  $model         Eloquent model instance
     * @param string $resourceClass Fully qualified Resource class name
     * @param int    $status        HTTP status code
     * @return JsonResponse
     */
    protected function resourceResponse($model, string $resourceClass, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new $resourceClass($model),
            'meta'    => $this->baseMeta(),
            'errors'  => [],
        ], $status);
    }

    /**
     * Return a paginated collection response.
     *
     * @param LengthAwarePaginator $paginator     Paginated query result
     * @param string               $resourceClass Resource class for each item
     * @return JsonResponse
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, string $resourceClass): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $resourceClass::collection($paginator->items()),
            'meta'    => array_merge($this->baseMeta(), [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                ],
            ]),
            'errors'  => [],
        ]);
    }

    /**
     * Return a collection response (non-paginated).
     *
     * @param Collection|array $items         Collection of models
     * @param string           $resourceClass Resource class for each item
     * @return JsonResponse
     */
    protected function collectionResponse($items, string $resourceClass): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $resourceClass::collection($items),
            'meta'    => $this->baseMeta(),
            'errors'  => [],
        ]);
    }

    /**
     * Return an error response.
     *
     * @param array|string $errors  Error(s) — string auto-wrapped to array
     * @param int          $status  HTTP status code (400, 401, 403, 404, 422, 500)
     * @param string       $code    Machine-readable error code
     * @return JsonResponse
     */
    protected function errorResponse($errors, int $status = 400, string $code = 'BAD_REQUEST'): JsonResponse
    {
        if (is_string($errors)) {
            $errors = [[
                'code'    => $code,
                'status'  => $status,
                'message' => $errors,
            ]];
        }

        return response()->json([
            'success' => false,
            'data'    => null,
            'meta'    => $this->baseMeta(),
            'errors'  => $errors,
        ], $status);
    }

    /**
     * Return a 404 not found response.
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404, 'NOT_FOUND');
    }

    /**
     * Return a 401 unauthorized response.
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401, 'UNAUTHORIZED');
    }

    /**
     * Return a 403 forbidden response.
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403, 'FORBIDDEN');
    }

    /**
     * Return a 422 validation error response from a Validator instance.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return JsonResponse
     */
    protected function validationErrorResponse($validator): JsonResponse
    {
        $errors = [];
        foreach ($validator->errors()->toArray() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'code'    => 'VALIDATION_FAILED',
                    'status'  => 422,
                    'field'   => $field,
                    'message' => $message,
                ];
            }
        }

        return response()->json([
            'success' => false,
            'data'    => null,
            'meta'    => $this->baseMeta(),
            'errors'  => $errors,
        ], 422);
    }

    /**
     * Return a 201 created response.
     */
    protected function createdResponse($data = null, string $message = 'Created successfully'): JsonResponse
    {
        return $this->successResponse($data, ['message' => $message], 201);
    }

    /**
     * Return a 204 no content response (for deletes).
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Build base meta object included in every response.
     */
    private function baseMeta(): array
    {
        return [
            'timestamp' => now()->toIso8601String(),
            'version'   => config('headless.version', '3.0'),
        ];
    }

    /**
     * Get validated per_page value from request, clamped to config limits.
     */
    protected function getPerPage(int $requested = null): int
    {
        $default = config('headless.pagination.default_per_page', 20);
        $max     = config('headless.pagination.max_per_page', 100);

        $perPage = $requested ?? request()->input('per_page', $default);

        return min(max((int) $perPage, 1), $max);
    }
}
