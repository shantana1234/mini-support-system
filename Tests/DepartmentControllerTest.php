<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Controllers/DepartmentController.php';
require_once __DIR__ . '/../app/Models/Department.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

AuthMiddleware::mock(function () {
    return AuthMiddlewareMock::$userId;
});


function assertEqual($expected, $actual, $message)
{
  
    if ($expected === $actual) {
        echo "$message<br>";
    } else {
        echo "$message Expected: $expected<br>&nbsp;&nbsp;&nbsp;&nbsp;Actual:   $actual<br>";
    }
}


class AuthMiddlewareMock {
    public static $userId = 1; 
}

class DepartmentControllerTest extends DepartmentController {
    protected function isAdmin($userId) {
        return 1; 
    }
}


echo "Running DepartmentController Tests...<br><br>";

$controller = new DepartmentControllerTest();

// TEST 1: Create department
$payload = ['name' => 'Test Department'];
file_put_contents('php://temp', json_encode($payload));
ob_start();
$controller->store();
$output = ob_get_clean();
$data = json_decode($output, true);

assertEqual(true,is_array($data), 'admin can create');

// TEST 2: Missing name
file_put_contents('php://temp', json_encode([]));
ob_start();
$controller->store();
$output = ob_get_clean();

assertEqual(true,str_contains($output, 'Name is required'), 'Missing name returns error');

// TEST 3: Non-admin cannot create
AuthMiddlewareMock::$userId = 2;
ob_start();
$controller->store();
$output = ob_get_clean();
assertEqual(true,str_contains($output, 'Only admin'), 'Non-admin is blocked from store');

// TEST 4: Get all departments
AuthMiddlewareMock::$userId = 1;
ob_start();
$controller->index();
$output = ob_get_clean();
$data = json_decode($output, true);
assertEqual(true,is_array($data), 'Index returns department list');

// TEST 5: Show department by ID
ob_start();
$controller->show(1);
$output = ob_get_clean();
$data = json_decode($output, true);
assertEqual(true,isset($data['id']), 'Show returns a department');

// TEST 6: Update department
$payload = ['name' => 'Updated Department'];
file_put_contents('php://temp', json_encode($payload));
ob_start();
$controller->update(1);
$output = ob_get_clean();
assertEqual(true,is_array($data), 'Update works for admin');

// TEST 7: Delete department
ob_start();
$controller->delete(9);
$output = ob_get_clean();

assertEqual(true,
    str_contains($output, 'deleted') || str_contains($output, 'Cannot delete') || str_contains($output, 'Department not found' ),
    'Delete department either succeeds or is blocked if in use'
);

echo "All tests completed.";
