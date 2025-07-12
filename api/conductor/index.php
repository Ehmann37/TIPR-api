<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/ConductorController.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        $queryParams = getQueryParams(['id']);
        handleGetConductor($queryParams);
        break;
    default:
        respond(405, 'Method Not Allowed');
}
