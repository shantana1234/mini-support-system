<?php

require_once __DIR__ . '/../../config/database.php';

class TicketNote
{
    public function create($ticket_id, $user_id, $note)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO ticket_notes (ticket_id, user_id, note) VALUES (?, ?, ?)");
        $stmt->execute([$ticket_id, $user_id, $note]);

        return ['message' => 'Note added successfully'];
    }

    public function all($ticket_id)
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT tn.id, tn.note, tn.created_at, u.name as author
            FROM ticket_notes tn
            JOIN users u ON tn.user_id = u.id
            WHERE tn.ticket_id = ?
            ORDER BY tn.created_at ASC
        ");
        $stmt->execute([$ticket_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function find($noteId)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM ticket_notes WHERE id = ?");
        $stmt->execute([$noteId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($noteId, $newNote)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE ticket_notes SET note = ? WHERE id = ?");
        $stmt->execute([$newNote, $noteId]);
        return ['message' => 'Note updated'];
    }

    public function delete($noteId)
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM ticket_notes WHERE id = ?");
        $stmt->execute([$noteId]);
        return ['message' => 'Note deleted'];
    }

}
