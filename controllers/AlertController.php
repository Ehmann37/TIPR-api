<?php

require_once __DIR__ . '/../models/AlertModel.php';
require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/QueryUtils.php';
require_once __DIR__ . '/../utils/ValidationUtils.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';

function handleGetAlert($queryParams) {
  $trip_id = (int)$queryParams['trip_id'];

  if ($trip_id == null) {
    respond('01', 'Missing trip_id');
    return;
  }

  if (!checkTripExist($trip_id)) {
    respond('01', 'Trip not found');
    return;
  }

  if ($trip_id !== getActiveTrip()) {
    respond('01', 'Trip is not active');
    return;
  }

  $filters = buildFilters($queryParams, ['trip_id']);
  $alerts = getAlerts($filters);
  if (empty($alerts)) {
    respond('01', 'No alerts found for the given trip');
    return;
  }
  respond('1', 'Alerts fetched successfully', $alerts);
}

function handleCreateAlert($queryParams) {
  $data = sanitizeInput(getRequestBody());
  
  $trip_id = $queryParams['trip_id'];

  if ($trip_id == null) {
    respond('01', 'Missing trip_id');
    return;
  }

  if (!checkTripExist($trip_id)) {
    respond('01', 'Trip not found');
    return;
  }

  if ((int)$trip_id !== getActiveTrip()) {
    respond('01', 'Trip is not active');
    return;
  }

  if (!isset($data['message'])) {
    respond('01', 'Missing message');
    return;
  }
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
