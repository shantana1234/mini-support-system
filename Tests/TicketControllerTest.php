<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Controllers/TicketController.php';
require_once __DIR__ . '/../app/Models/Ticket.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';


AuthMiddleware::mock(function () {
    return AuthMiddlewareMock::$userId;
});


function assertEqual($expected, $actual, $message) {
    echo "jbjn".$actual;
    if ($expected === $actual) {
        echo "$message<br>";
    } else {
        echo "$message<br>Expected: $expected<br>&nbsp;&nbsp;&nbsp;&nbsp;Actual:   $actual<br>";
    }
}

class AuthMiddlewareMock {
    public static $userId = 1; 
}

class TicketControllerTest extends TicketController {
  protected function isAdmin($userId) {
        return 1; 
    }}


echo "Running TicketController Tests...<br><br>";

$controller = new TicketControllerTest();

// Test 1: Create Ticket
$_POST['title'] = 'Sample Ticket';
$_POST['description'] = 'A test ticket';
$_POST['department_id'] = 1;
$_POST['user_id'] = 1;
$_FILES = []; 
ob_start(); 
$controller->store();
$output = ob_get_clean();
assertEqual(true, str_contains($output, 'Ticket created successfully'), 'Admin can create ticket');

// Test 2: Show Ticket
ob_start();
$controller->show(2);
$output = ob_get_clean();
$data = json_decode($output, true);
assertEqual(true, isset($data['ticket']), 'Show ticket with details');

// Test 3: Index (admin only)
ob_start();
$controller->index();
$output = ob_get_clean();
$data = json_decode($output, true);
assertEqual(true, is_array($data), 'Admin sees all tickets');

// Test 4: Delete Ticket
ob_start();
$controller->delete(1);
$output = ob_get_clean();
assertEqual(true, str_contains($output, 'Ticket deleted successfully')||str_contains($output, 'Ticket not found'), 'Admin can delete ticket');

echo "<br>🎉 All ticket tests completed.";
