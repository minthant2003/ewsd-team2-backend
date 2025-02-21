<?php

namespace App\Classes;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiResponseClass
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function rollback($e, $message = "Something went wrong! Process not completed.")
    {
        DB::rollBack();
        self::printStackTrace($e);
        self::throw($e, $message);
    }

    public static function printStackTrace($e)
    {
        Log::error('Exception occurred:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'stackTrace' => $e->getTraceAsString()
        ]);
    }

    public static function throw($e, $message = "Something went wrong! Process not completed.")
    {
        $response = [
            'status' => 500,
            'message' => $message,
            'data' => $e->getMessage()
        ];
        // Log::info($e);
        throw new HttpResponseException(response()->json($response, 500));
    }

    public static function sendResponse($result, $message, $code = 200)
    {
        $response = [
            // 'success' => true,
            'status' => $code,
            'data' => $result
        ];
        if (!empty($message)) {
            $response['message'] = $message;
        }
        return response()->json($response, $code);
    }
}
