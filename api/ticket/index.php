<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/TicketController.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';


checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $queryParams = getQueryParams(['ticket_id', 'payment_id',  'passenger_status', 'payment_status', 'passenger_category', 'latitude', 'longitude', 'trip_id']);
        handleGetTicket($queryParams);
        break;

    case 'POST':
        handleCreateTicket();
        break;

    case 'PUT':
        $queryParams = getQueryParams(['ticket_id']);
        updateTicketHandler($queryParams['ticket_id']);
        break;
    default:
        respond('02', 'Method Not Allowed');

}