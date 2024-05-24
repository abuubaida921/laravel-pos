<?php

namespace App\Helper;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function OutResponse($message, $data, $status): JsonResponse
    {
        return response()->json(['msg' => $message, 'data' => $data], $status);
    }
}