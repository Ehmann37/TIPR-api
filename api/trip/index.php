<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/TripModel.php';

checkAuthorization();
header("Content-Type: application/json");

$KEY = 'mysecretkey1234567890abcdef'; 

$method = $_SERVER['REQUEST_METHOD'];

switch($method){
  case 'PUT':
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Empty request body']);
        exit;
    }


    if (isset($data['status']) && isset($data['bus_id'])) { 
      if (!in_array($data['status'], ['active', 'complete'])) {
          http_response_code(400);
          echo json_encode(['status' => 'error', 'message' => 'Invalid status. Must be "active" or "complete".']);
          exit;
      }

      if ($data['status'] === 'complete') {
        $update = completeInstatnce($data['bus_id'], $data['status']);
        if (!$update) {
            echo json_encode(['status' => 'error', 'message' => 'No active trip found for the bus']);
            exit;
        }
      } 

      else if ($data['status'] === 'active') {
        if (checkBusifActive($data['bus_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Bus is already active']);
            exit;
        }
        $update = createInstance($data['bus_id'], $data['status']);
      }
      
      else {
          $update = null;
      }
    } 


    if ($update) {
        echo json_encode([
          'status' => 'success',
          'message' => 'Trip status updated successfully',
          'data' => $update]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update trip status']);
    }
}
?>