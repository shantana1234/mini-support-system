<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class UserController
{
    public function register()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $user = new User();
        $result = $user->create($input);
        echo json_encode($result);
    }

    public function login()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $user = new User();
        $result = $user->login($input);
        echo json_encode($result);
    }
   public function logout()
    {
        $userId = AuthMiddleware::check(); 
        $headers = getallheaders();
        preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches);
        $token = $matches[1];

        $tokenPath = __DIR__ . '/../../storage/tokens.json';
        $tokens = json_decode(file_get_contents($tokenPath), true);

        unset($tokens[$token]);
        file_put_contents($tokenPath, json_encode($tokens));

        http_response_code(200);
        echo json_encode(['message' => 'User ' . $userId . ' logged out']);
    }

}
