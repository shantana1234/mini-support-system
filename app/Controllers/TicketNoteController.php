<?php

require_once __DIR__ . '/../Models/TicketNote.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class TicketNoteController
{
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
    public function store($ticketId)
    {
        $userId = AuthMiddleware::check();
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM tickets WHERE id = ?");
        $stmt->execute([$ticketId]);
        $ticketExists = $stmt->fetchColumn();

        if (!$ticketExists) {
            http_response_code(404);
            echo json_encode(['error' => 'Ticket not found']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['note'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Note is required']);
            return;
        }
        $model = new TicketNote();
        echo json_encode($model->create($ticketId, $userId, $input['note']));
    }

    public function index($ticketId)
    {
        $userId = AuthMiddleware::check();
        if (!$this->isAdmin($userId)) return;
        $model = new TicketNote();
        echo json_encode($model->all($ticketId));
    }
    public function show($ticketId, $noteId)
    {
        $userId = AuthMiddleware::check();

        global $pdo;
        $stmt = $pdo->prepare("
            SELECT tn.*, u.role
            FROM ticket_notes tn
            INNER JOIN users u ON tn.user_id = u.id
            WHERE tn.id = ? AND tn.ticket_id = ?
        ");

        $stmt->execute([$noteId, $ticketId]);
        $note = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$note) {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found']);
            return;
        }

        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $currentUserRole = $stmt->fetchColumn();

        if ($note['user_id'] != $userId && $currentUserRole !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Not authorized to view this note']);
            return;
        }

        echo json_encode([
            'id' => $note['id'],
            'note' => $note['note'],
            'ticket_id' => $note['ticket_id'],
            'user_id' => $note['user_id'],
            'created_at' => $note['created_at']
        ]);
    }



    public function update($ticketId, $noteId)
    {
        $userId = AuthMiddleware::check();

        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM ticket_notes WHERE id = ?");
        $stmt->execute([$noteId]);
        $note = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$note || $note['ticket_id'] != $ticketId) {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found']);
            return;
        }
        
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $currentUserRole = $stmt->fetchColumn();

        if ($note['user_id'] != $userId && $currentUserRole !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized to update this note']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['note'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Note text is required']);
            return;
        }

        $model = new TicketNote();
        echo json_encode($model->update($noteId, $input['note']));
    }

    public function delete($ticketId, $noteId)
    {
        $userId = AuthMiddleware::check();

        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM ticket_notes WHERE id = ?");
        $stmt->execute([$noteId]);
        $note = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$note || $note['ticket_id'] != $ticketId) {
            http_response_code(404);
            echo json_encode(['error' => 'Note not found']);
            return;
        }

        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $role = $stmt->fetchColumn();

        if ($note['user_id'] != $userId && $role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Not allowed to delete this note']);
            return;
        }

        $model = new TicketNote();
        echo json_encode($model->delete($noteId));
    }

}
