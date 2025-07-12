<?php

require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/QueryUtils.php';
require_once __DIR__ . '/../utils/ValidationUtils.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';

function handleGetBus($queryParams) {
  $id = $queryParams['id'];

  if ($id !== null) {
    if (!checkBusExists($id)) {
        respond(404, 'Bus not found');
        return;
    }
    
    $bus = getBusById($id);
    respond(200, 'Bus fetched', $bus);
  } else {
    $allowed = ['route_id', 'conductor_id', 'driver_id', 'status'];
    $filters = buildFilters($queryParams, $allowed);
    
    $buses = getBuses($filters);
    respond(200, 'Buses fetched', $buses);
    
  }
}

function updateBusHandler($id) {
    if ($id === null) {
        respond(400, 'Missing bus ID');
        return;
    }

    $data = sanitizeInput(getRequestBody());
    
    $allowed = ['route_id', 'driver_id', 'conductor_id', 'status'];
    if (!validateAtLeastOneField($data, $allowed)) {
      respond(400, 'No valid fields provided for update');
      return;
    }

    try {
        $updated = updateBus($id, $data, $allowed);
        if ($updated) {
            respond(200, 'Bus updated');
        } else {
            respond(404, 'Bus not found or no changes made');
        }
    } catch (Exception $e) {
        respond(500, $e->getMessage());
    }
}
