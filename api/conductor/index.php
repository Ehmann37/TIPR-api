<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/ConductorModel.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = !empty($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
  case 'GET':
      if ($id === null) {
          $conductors = getConductors();
          echo json_encode(['status' => 'success', 'data' => $conductors]);
      } else {
          $conductor = getConductorById($id);
          if ($conductor) {
              echo json_encode(['status' => 'success', 'data' => $conductor]);
          } else {
              http_response_code(404);
              echo json_encode(['status' => 'error', 'message' => 'Conductor not found']);
          }
      }
      break;
  default:
  http_response_code(405);
  echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
} 