<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/BusController.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $queryParams = getQueryParams(['id', 'status', 'route_id', 'driver_id', 'conductor_id']);
        handleGetBus($queryParams);
        break;
    case 'PUT':
        $queryParams = getQueryParams(['id']);
        updateBusHandler($queryParams['id']);
        break;
    default:
        respond('02', 'Method Not Allowed');
}
