<?php

require_once __DIR__ . '/../Controllers/UserController.php';
require_once __DIR__ . '/../Controllers/DepartmentController.php';
require_once __DIR__ . '/../Controllers/TicketController.php';
require_once __DIR__ . '/../Controllers/TicketNoteController.php';


function route($method, $uri)
{
    $departmentController = new DepartmentController();
    $ticketController = new TicketController();
    $noteController = new TicketNoteController();
    $uri = trim($uri, '/');
    // echo json_encode(['method' => $method, 'uri' => $uri]);

    if ($uri === 'register' && $method === 'POST') {
        (new UserController())->register();
    } elseif ($uri === 'login' && $method === 'POST') {
        (new UserController())->login();
    } elseif ($uri === 'logout' && $method === 'DELETE') {
        (new UserController())->logout();
    }else if ($uri === 'departments' && $method === 'GET') {
        $departmentController->index();
    } elseif ($uri === 'departments' && $method === 'POST') {
        $departmentController->store();
    } elseif (preg_match('/^departments\/(\d+)$/', $uri, $matches) && $method === 'PUT') {
        $departmentController->update($matches[1]);
    } elseif (preg_match('/^departments\/(\d+)$/', $uri, $matches) && $method === 'DELETE') {
        $departmentController->delete($matches[1]);
    }elseif (preg_match('/^departments\/(\d+)$/', $uri, $matches) && $method === 'GET') {
        $departmentController->show($matches[1]);
    }else if ($uri === 'tickets' && $method === 'GET') {
        $ticketController->index();
    }else if ($uri === 'tickets' && $method === 'POST') {
        $ticketController->store();
    }elseif (preg_match('/^tickets\/(\d+)$/', $uri, $matches) && $method === 'GET') {
        $ticketController->show($matches[1]);
    }
    elseif (preg_match('/^tickets\/(\d+)\/update$/', $uri, $matches) && $method === 'POST') {
        $ticketController->update($matches[1]);
    } elseif (preg_match('/^tickets\/(\d+)$/', $uri, $matches) && $method === 'DELETE') {
        $ticketController->delete($matches[1]);

    }else if (preg_match('/^tickets\/(\d+)\/notes$/', $uri, $matches)) {
    $ticketId = $matches[1];

    if ($method === 'POST') {
        $noteController->store($ticketId);
    } elseif ($method === 'GET') {
        $noteController->index($ticketId);
    }
    }else if (preg_match('/^tickets\/(\d+)\/notes\/(\d+)$/', $uri, $matches)) {
    $ticketId = $matches[1];
    $noteId = $matches[2];

    if ($method === 'GET') {
        $noteController->show($ticketId, $noteId);
    } elseif ($method === 'PUT') {
        $noteController->update($ticketId, $noteId);
    } elseif ($method === 'DELETE') {
        $noteController->delete($ticketId, $noteId);
    }
    }else {
        http_response_code(404);
        echo json_encode(['uri'=> $uri, 'error' => 'Route not found']);
    }

}
