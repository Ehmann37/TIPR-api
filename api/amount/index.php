<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/AmountController.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $queryParams = getQueryParams(['origin_id',  'destination_id']);
        handleGetAmount($queryParams);
        break;
    default:
        respond('02', 'Method Not Allowed');
}
