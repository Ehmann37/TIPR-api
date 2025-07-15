<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/SessionController.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        handleTripPost();
        break;
    default:
        respond('02', 'Method Not Allowed');

}
exit;
