<?php

require_once __DIR__ . '/../../config/database.php';

class Ticket
{
    public function all()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM tickets");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($title, $description, $user_id, $department_id, $attachment = null)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO tickets (title, description, user_id, department_id, attachment) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $user_id, $department_id, $attachment]);

        return ['message' => 'Ticket created successfully', 'attachment' => $attachment];
    }
    public function find($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            http_response_code(404);
            return ['error' => 'Ticket not found'];
        }

        return $ticket;
    }
    public function update($id, $data)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT attachment FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        if (is_null($data['attachment']) && !empty($ticket['attachment'])) {
            $existingFilePath = __DIR__ . '/../../' . $ticket['attachment'];
            if (file_exists($existingFilePath)) {
                unlink($existingFilePath);
            }
        }

        $stmt = $pdo->prepare("
            UPDATE tickets 
            SET title = ?, description = ?, department_id = ?, status = ?, attachment = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['department_id'],
            $data['status'],
            $data['attachment'],
            $id
        ]);

        return ['message' => 'Ticket updated successfully'];
    }

    public function delete($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT attachment FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            http_response_code(404);
            return ['error' => 'Ticket not found'];
        }

        if (!empty($ticket['attachment'])) {
            $filePath = __DIR__ . '/../../' . $ticket['attachment'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
        $stmt->execute([$id]);

        return ['message' => 'Ticket deleted successfully'];
    }


}
