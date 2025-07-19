<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/AlertController.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $queryParams = getQueryParams(['trip_id']);
        handleGetAlert($queryParams);
        break;
    case 'POST':
        $queryParams = getQueryParams(['trip_id']);
        handleCreateAlert($queryParams);
        break;
    case 'PUT':
        $queryParams = getQueryParams(['id']);
        updateAlertHandler($queryParams['id']);
    default:
        respond('02', 'Method Not Allowed');
}
