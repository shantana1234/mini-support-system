<?php

class AuthMiddleware
{
     private static $mockCheck;

    public static function check()
    {
        if (self::$mockCheck) {
            return call_user_func(self::$mockCheck);
        }
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization header missing']);
            exit;
        }

        if (!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid Authorization format']);
            exit;
        }

        $token = $matches[1];
        $tokenPath = __DIR__ . '/../../storage/tokens.json';

        if (!file_exists($tokenPath)) {
            http_response_code(500);
            echo json_encode(['error' => 'Token storage not found']);
            exit;
        }

        $tokens = json_decode(file_get_contents($tokenPath), true);

        if (!isset($tokens[$token])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit;
        }

        return $tokens[$token]; 
    }

     public static function mock($callback)
    {
        self::$mockCheck = $callback;
    }
}
