<?php

namespace App\Http\Middleware;

use App\Enums\Constant;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $apiKey = config('app.tc_api_key');

        $apiKeyIsValid = (
            ! empty($apiKey)
            && trim($request->header('X-Api-Key')) == $apiKey
        );

        if (!$apiKeyIsValid) {
            return response()->json([
                'status' => Constant::BAD_REQUEST_CODE,
                'message' => 'Xác thực thất bại',
            ], Constant::BAD_REQUEST_CODE);
        }

        return $next($request);
    }
}
