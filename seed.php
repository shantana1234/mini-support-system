<?php

require_once __DIR__ . '/config/database.php';

function hashPassword($plain) {
    return password_hash($plain, PASSWORD_BCRYPT);
}

// $pdo->exec("DELETE FROM ticket_notes");
// $pdo->exec("DELETE FROM tickets");
// $pdo->exec("DELETE FROM departments");
// $pdo->exec("DELETE FROM users");

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("TRUNCATE TABLE ticket_notes");
$pdo->exec("TRUNCATE TABLE tickets");
$pdo->exec("TRUNCATE TABLE departments");
$pdo->exec("TRUNCATE TABLE users");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
$users = [
    ['Admin User', 'admin@example.com', hashPassword('admin123'), 'admin'],
    ['Agent Shantana', 'shantana@example.com', hashPassword('agent123'), 'agent'],
    ['Agent Shopnil', 'shopnil@example.com', hashPassword('agent123'), 'agent'],
    ['Agent Saba', 'saba@example.com', hashPassword('agent123'), 'agent'],
    ['Agent Shama', 'shama@example.com', hashPassword('agent123'), 'agent'],
];

foreach ($users as $u) {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute($u);
}
echo "-------------Users seeded-------------";

$departments = ['Support', 'IT', 'HR', 'Legal'];

foreach ($departments as $name) {
    $stmt = $pdo->prepare("INSERT INTO departments (name) VALUES (?)");
    $stmt->execute([$name]);
}
echo "-------------Departments seeded-------------";
$tickets = [
    // User 1
    ['Login issue', 'User cannot log in to the dashboard.', 1, 1, 'open', null],
    ['Password reset not working', 'Reset email is not sent.', 1, 2, 'open', null],
    ['Bug in report module', 'Export to PDF is not working.', 2, 2, 'in_progress', null],
    ['UI alignment broken', 'Text overlaps on the dashboard.', 2, 1, 'open', 'storage/uploads/tickets/sample-pdf-file.pdf'],
    ['App crashes on iOS', 'Crash after tapping “Start”.', 3, 1, 'open', 'storage/uploads/tickets/sample-pdf-file-test.pdf'],
    ['Push notifications delayed', 'User receives alerts after 1 hour.', 3, 3, 'in_progress', null],
    ['Cannot assign tasks', 'Task list does not save assignments.', 4, 2, 'open', null],
    ['Email templates broken', 'Email confirmation text is corrupted.', 5, 4, 'open', 'storage/uploads/tickets/Sample-pdf-Files-for-Testing-1.webp'],
];


foreach ($tickets as $t) {
    $stmt = $pdo->prepare("INSERT INTO tickets (title, description, user_id, department_id, status, attachment) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute($t);
}
echo "-------------Tickets seeded-------------";
$notes = [
    [1, 2, 'We are investigating the login issue.'],
    [1, 1, 'Confirmed: user was locked out. Fixed.'],
    [1, 2, 'Asked the user to try again.'],
    [2, 1, 'Issue confirmed on staging.'],
    [2, 3, 'SMTP logs checked — email sending failed.'],
    [2, 2, 'Fixed by updating mail config.'],
    [3, 3, 'Assigned to frontend team.'],
    [3, 1, 'Cannot reproduce on Chrome.'],
    [3, 2, 'Occurs only when using Firefox.'],
    [4, 2, 'CSS issue found on responsive layout.'],
    [4, 4, 'Fixed via layout patch.'],
    [4, 1, 'Deployed and verified by QA.'],
    [5, 1, 'Crash logs received from TestFlight.'],
    [5, 3, 'App crashes on iOS 14 but not iOS 16.'],
    [5, 4, 'Patched and released new version.'],
    [6, 3, 'Delays confirmed during peak hours.'],
    [6, 1, 'Notification queue looks fine.'],
    [6, 2, 'Improved push scheduling.'],
    [7, 5, 'Assignment fails on mobile view.'],
    [7, 2, 'Bug reproduced in Safari.'],
    [7, 1, 'Fix deployed, needs retesting.'],
    [8, 4, 'Template renders incorrectly in Outlook.'],
    [8, 5, 'HTML structure was broken.'],
    [8, 3, 'Updated layout and CSS.'],
    [8, 1, 'Client confirmed fix is working.'],
];


foreach ($notes as $n) {
    $stmt = $pdo->prepare("INSERT INTO ticket_notes (ticket_id, user_id, note) VALUES (?, ?, ?)");
    $stmt->execute($n);
}
echo "-------------Notes seeded-------------";
