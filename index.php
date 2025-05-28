<?php

require_once 'config/database.php';
require_once 'app/Routes/routes.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$uri = trim($uri, '/');

$basePath = 'job-interview/skiff/mini-support-system';
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
    $uri = trim($uri, '/');
}

route($method, $uri);
