<?php

require_once __DIR__ . '/../Models/Department.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Models/User.php';

class DepartmentController
{
    public function index()
    {
        $department = new Department();
        echo json_encode($department->all());
    }

    public function store()
    {
        $userId = AuthMiddleware::check();
        if (!$this->isAdmin($userId)) return;

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name is required']);
            return;
        }

        $department = new Department();
        echo json_encode($department->create($input['name']));
    }

    public function update($id)
    {
        $userId = AuthMiddleware::check();
        if (!$this->isAdmin($userId)) return;

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name is required']);
            return;
        }

        $department = new Department();
        
        echo json_encode($department->update($id, $input['name']));
    }

    public function delete($id)
    {
        $userId = AuthMiddleware::check();
        if (!$this->isAdmin($userId)) return;

        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        $departmentExists = $stmt->fetchColumn();

        if (!$departmentExists) {
            http_response_code(404);
            echo json_encode(['error' => 'Department not found']);
            return;
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE department_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();
        // echo $count;
        if ($count > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Cannot delete: department is assigned to tickets']);
            return ;
        }

        $department = new Department();
        echo json_encode($department->delete($id));
    }


    private function isAdmin($userId) 
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $role = $stmt->fetchColumn();

        if ($role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Only admin users can perform this action']);
            return false;
        }

        return true;
    }
    public function show($id)
    {
        $department = new Department();
        echo json_encode($department->find($id));
    }

}
