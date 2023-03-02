<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = null, int $status = 200): Response
    {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];

        return new Response(json_encode($response), $status, [
            'Content-Type' => 'application/json',
        ]);
    }

    public static function error($data = null, $message = null, int $status = 500): Response
    {
        $response = [
            'success' => false,
            'data' => $data,
            'message' => $message,
        ];

        return new Response(json_encode($response), $status, [
            'Content-Type' => 'application/json',
        ]);
    }
}