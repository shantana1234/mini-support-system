<?php

require_once __DIR__ . '/../../config/database.php';

class User
{
    public function create($data)
    {
        global $pdo;

        if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
            http_response_code(400);
            return ['error' => 'Missing required fields'];
        }
        $allowedRoles = ['admin', 'agent'];
        if (!in_array($data['role'], $allowedRoles)) {
            http_response_code(422);
            return ['error' => 'Invalid role. Must be either admin or agent'];
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(409); 
            return ['error' => 'Email already exists'];
        }
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt->execute([$data['name'], $data['email'], $passwordHash, $data['role']]);

        http_response_code(201);
        return ['message' => 'User registered successfully'];
    }

    public function login($data)
    {
        global $pdo;

        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            return ['error' => 'Email and password are required'];
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($data['password'], $user['password_hash'])) {
            $token = bin2hex(random_bytes(16));
            file_put_contents(__DIR__ . '/../../storage/tokens.json', json_encode([$token => $user['id']]));
            return ['token' => $token];
        }

        http_response_code(401);
        return ['error' => 'Invalid credentials'];
    }
}
