<?php

require_once __DIR__ . '/../models/AlertModel.php';
require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/QueryUtils.php';
require_once __DIR__ . '/../utils/ValidationUtils.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';

function handleGetAlert($queryParams) {
  $bus_id = $queryParams['bus_id'];
  $trip_id = $queryParams['trip_id'];

  if ($bus_id == null &&  $trip_id == null) {
    respond('01', 'Missing bus_id or trip_id');
    return;
  }

  if (!checkBusExists($bus_id)) {
    respond('01', 'Bus not found');
    return;
  }

  if (!checkTripExist($trip_id)) {
    respond('01', 'Trip not found');
    return;
  }

  $filters = buildFilters($queryParams, ['bus_id', 'trip_id']);
  $alerts = getAlerts($filters);
  if (empty($alerts)) {
    respond('01', 'No alerts found for the given bus or trip');
    return;
  }
  respond('1', 'Alerts fetched successfully', $alerts);
}

function handleCreateAlert($queryParams) {
  $data = sanitizeInput(getRequestBody());
  
  $bus_id = $queryParams['bus_id'];
  $trip_id = $queryParams['trip_id'];

  if ($bus_id == null &&  $trip_id == null) {
    respond('01', 'Missing bus_id or trip_id');
    return;
  }

  if (!checkBusExists($bus_id)) {
    respond('01', 'Bus not found');
    return;
  }

  if (!checkTripExist($trip_id)) {
    respond('01', 'Trip not found');
    return;
  }

  if (!isset($data['message'])) {
    respond('01', 'Missing required fields: bus_id, trip_id, message');
    return;
  }

  $data['bus_id'] = $bus_id;
  $data['trip_id'] = $trip_id;

  try {
    $alert = createAlert($data);
    if (!$alert) {
      respond('01', 'Failed to create alert');
      return;
    }
    respond('1', 'Alert created successfully', $alert);
  } catch (Exception $e) {
    respond(500, $e->getMessage());
  }
}

function updateAlertHandler($id) {
    if ($id === null) {
        respond('01', 'Missing alert ID');
        return;
    }

    if (!checkAlertExists($id)) {
        respond('01', 'Alert not found');
        return;
    }

    updateAlert($id);
    respond('1', 'Alert updated successfully');
}
