<?php

namespace App\Core\Traits;

trait ApiResponse
{
    protected function successResponse($data = [], $message = null, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        
        $response = [
            'status' => 'success',
            'data' => $data
        ];
        
        if ($message) {
            $response['message'] = $message;
        }

        echo json_encode($response);
        
        // During testing, we want to capture output, not kill the script
        if (!defined('TESTING')) {
            exit;
        }
    }

    protected function errorResponse($message, $code = 400, $data = [])
    {
        http_response_code($code);
        header('Content-Type: application/json');
        
        $response = [
            'status' => 'error',
            'message' => $message
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        echo json_encode($response);
        exit;
    }
}
