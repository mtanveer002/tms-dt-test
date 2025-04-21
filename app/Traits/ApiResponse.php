<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * success response method.
     */
    public function sendSuccess($result, $message): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (! empty($result)) {
            $response['data'] = $result;
        }

        return response()->json($response, 200);
    }

    /**
     * return error response.
     */
    public function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
