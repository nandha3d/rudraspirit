<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CORS Middleware for Headless API V3
 *
 * Handles Cross-Origin Resource Sharing headers so any frontend
 * (Next.js, Nuxt, React, Flutter web, etc.) can call the API.
 *
 * Configuration: config/headless.php → cors section
 * Env variable:  API_CORS_ORIGINS (comma-separated domains, or * for all)
 */
class HeadlessCors
{
    public function handle(Request $request, Closure $next): Response
    {
        // Handle preflight OPTIONS request immediately
        if ($request->isMethod('OPTIONS')) {
            return $this->addCorsHeaders(response('', 204), $request);
        }

        $response = $next($request);

        return $this->addCorsHeaders($response, $request);
    }

    /**
     * Add CORS headers to the response.
     */
    private function addCorsHeaders($response, Request $request)
    {
        $allowedOrigins = config('headless.cors.origins', ['*']);
        $origin = $request->header('Origin', '');

        // Determine the Access-Control-Allow-Origin value
        if (in_array('*', $allowedOrigins)) {
            $allowOrigin = '*';
        } elseif (in_array($origin, $allowedOrigins)) {
            $allowOrigin = $origin;
        } else {
            // Origin not allowed — don't add CORS headers
            return $response;
        }

        $response->headers->set('Access-Control-Allow-Origin', $allowOrigin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, X-CSRF-TOKEN, X-Locale');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', (string) config('headless.cors.max_age', 86400));
        $response->headers->set('Access-Control-Expose-Headers', 'X-RateLimit-Limit, X-RateLimit-Remaining');

        return $response;
    }
}
