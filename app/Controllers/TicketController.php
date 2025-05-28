<?php

require_once __DIR__ . '/../Models/Ticket.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class TicketController
{

    private function rateLimitCheck($userId, $limit = 10000, $window = 3600)
    {
        $filePath = __DIR__ . '/../../storage/rate_limit.json';
        if (!file_exists($filePath)) file_put_contents($filePath, json_encode([]));
        $data = json_decode(file_get_contents($filePath), true);

        $now = time();
        if (!isset($data[$userId])) {
            $data[$userId] = [];
        }
        $data[$userId] = array_filter($data[$userId], function ($timestamp) use ($now, $window) {
            return ($now - $timestamp) < $window;
        });

        if (count($data[$userId]) >= $limit) {
            http_response_code(429);
            echo json_encode(['error' => 'Rate limit exceeded. Try again later.']);
            exit;
        }

        $data[$userId][] = $now;
        file_put_contents($filePath, json_encode($data));
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
    public function index()
    {
        $userId = AuthMiddleware::check();
        if (!$this->isAdmin($userId)) return;   
        $ticket = new Ticket();
        echo json_encode($ticket->all());
    }
    public function store()
    {
        // print_r($_FILES);
        $userId = AuthMiddleware::check();

        $this->rateLimitCheck($userId);

        $uploadPath = __DIR__ . '/../../storage/uploads/tickets/';
        if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);

        $attachmentPath = null;
        if (!empty($_FILES['attachment']['name'])) {
            echo "koko";
            $filename = uniqid() . '_' . basename($_FILES['attachment']['name']);
            $target = $uploadPath . $filename;

            if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload file']);
                return;
            }

            $attachmentPath = 'storage/uploads/tickets/' . $filename;
        }
        $title = $_POST['title'] ?? null;
        $desc = $_POST['description'] ?? null;
        $deptId = $_POST['department_id'] ?? null;
        // echo $title.$desc.$userId.$deptId.$attachmentPath;
        if (!$title || !$deptId) {
            http_response_code(400);
            echo json_encode(['error' => 'title and department_id required']);
            return;
        }

        $ticket = new Ticket();
        echo json_encode($ticket->create($title, $desc, $userId, $deptId, $attachmentPath));
    }

    public function update($id)
    { 
        $userId = AuthMiddleware::check();
        global $pdo;
        $stmt = $pdo->prepare("SELECT user_id FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            return;
        }


        if ($ticket['user_id'] != $userId && !$this->isAdmin($userId)) {
            http_response_code(403);
            echo json_encode(['error' => 'You are not authorized to update this ticket']);
            return;
        }
        // $input = json_decode(file_get_contents('php://input'), true);
        //  echo $input;
        $title = $_POST['title'] ?? null;
        $description = $_POST['description'] ?? null;
        $department_id = $_POST['department_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$title || !$department_id ) {
            http_response_code(400);
            echo json_encode(['error' => 'title and department_id are required']);
            return;
        }
        $allowedStatuses = ['open', 'in_progress', 'closed'];
        if (!in_array($status, $allowedStatuses)) {
            http_response_code(422);
            echo json_encode(['error' => 'Invalid status. Must be open, in_progress, or closed']);
            return;
        }
        $uploadPath = __DIR__ . '/../../storage/uploads/tickets/';
        if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);

        $attachmentPath = $_POST['current_attachment'] ?? null;

        if (!empty($_FILES['attachment']['name'])) {
            $filename = uniqid() . '_' . basename($_FILES['attachment']['name']);
            $target = $uploadPath . $filename;

            if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload file']);
                return;
            }

            $attachmentPath = 'storage/uploads/tickets/' . $filename;
        }
        $ticket = new Ticket();
        echo json_encode($ticket->update($id, [
            'title' => $title,
            'description' => $description,
            'department_id' => $department_id,
            'status' => $status,
            'attachment' => $attachmentPath
        ]));
    }


    public function show($id)
    {
        $userId = AuthMiddleware::check();
        global $pdo;

        $stmt = $pdo->prepare("
            SELECT 
                t.id as ticket_id, t.title, t.description, t.department_id, t.status, 
                t.attachment, t.created_at,
                u.id as user_id, u.name as user_name, u.email as user_email, u.role as user_role
            FROM tickets t
            INNER JOIN users u ON t.user_id = u.id
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            return;
        }
        // echo json_encode(['error' => $ticket['user_id'], 'id' => $userId]);
        if ($ticket['user_id'] != $userId && !$this->isAdmin($userId)) {
            http_response_code(403);
            echo json_encode(['error' => 'You are not authorized to view this ticket']);
            return;
        }

        $stmt = $pdo->prepare("
            SELECT 
                tn.id, tn.note, tn.created_at,
                u.name as author_name, u.role as author_role
            FROM ticket_notes tn
            INNER JOIN users u ON tn.user_id = u.id
            WHERE tn.ticket_id = ?
            ORDER BY tn.created_at ASC
        ");
        $stmt->execute([$id]);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'ticket' => [
                'id' => $ticket['ticket_id'],
                'title' => $ticket['title'],
                'description' => $ticket['description'],
                'department_id' => $ticket['department_id'],
                'status' => $ticket['status'],
                'attachment' => $ticket['attachment'],
                'created_at' => $ticket['created_at']
            ],
            'user' => [
                'id' => $ticket['user_id'],
                'name' => $ticket['user_name'],
                'email' => $ticket['user_email'],
                'role' => $ticket['user_role']
            ],
            'notes' => $notes
        ]);
    }


   public function delete($id)
{
    $userId = AuthMiddleware::check();
    global $pdo;

    // 1. Check if ticket exists
    $stmt = $pdo->prepare("SELECT user_id FROM tickets WHERE id = ?");
    $stmt->execute([$id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        http_response_code(404);
        echo json_encode(['error' => 'Ticket not found']);
        return;
    }

    // 2. Authorization check (owner or admin)
    if ($ticket['user_id'] != $userId && !$this->isAdmin($userId)) {
        http_response_code(403);
        echo json_encode(['error' => 'You are not authorized to delete this ticket']);
        return;
    }

    // 3. Check if any notes exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ticket_notes WHERE ticket_id = ?");
    $stmt->execute([$id]);
    $noteCount = $stmt->fetchColumn();

    if ($noteCount > 0) {
        $stmt = $pdo->prepare("DELETE FROM ticket_notes WHERE ticket_id = ?");
        $stmt->execute([$id]);
    }

    // 4. Delete the ticket itself
    $ticketModel = new Ticket();
    echo json_encode($ticketModel->delete($id));
}





}
