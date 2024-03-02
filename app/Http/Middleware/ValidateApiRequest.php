<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request content type is JSON
        if (!$request->isJson()) {
            return response()->json(['error' => 'Invalid request format. Must be JSON.'], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        // Attempt to decode the JSON payload
        json_decode($request->getContent());

        // Check if JSON decoding was successful
        if (json_last_error() != JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON format.'], Response::HTTP_BAD_REQUEST);
        }
        
        return $next($request);
    }
}
