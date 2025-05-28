<?php

require_once __DIR__ . '/../../config/database.php';

class Department
{
    public function all()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM departments");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO departments (name) VALUES (?)");
        $stmt->execute([$name]);
        return ['message' => 'Department created'];
    }

    public function update($id, $name)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE departments SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
        return ['message' => 'Department updated'];
    }

    public function delete($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        return ['message' => 'Department deleted'];
    }
    public function find($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            http_response_code(404);
            return ['error' => 'Department not found'];
        }

        return $result;
    }

}
