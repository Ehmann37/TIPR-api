<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/TripController.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
  case 'GET':
    $queryParams = getQueryParams(['trip_id']);
    handleGetTripSummary($queryParams);
  case 'PUT':
    handleUpdateTripStatus();
    break;

  default:
    respond('02', 'Method Not Allowed');
}
