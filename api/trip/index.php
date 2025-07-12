<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/TripController.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
  case 'PUT':
    handleUpdateTripStatus();
    break;

  default:
    respond(405, 'Method Not Allowed');
}
