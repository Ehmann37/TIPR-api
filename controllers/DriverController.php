<?php

require_once __DIR__ . '/../models/DriverModel.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/QueryUtils.php';


function handleGetDriver($queryParams) {
  $id = $queryParams['id'];

  if ($id !== null) {
    if (!checkDriverExists($id)) {
        respond(404, 'Driver not found');
        return;
    }
    
    $driver = getDriverById($id);
    respond(200, 'Driver fetched', $driver);
  } else {
    $allowed = [];
    $filters = buildFilters($queryParams, $allowed);
    
    $drivers = getDrivers($filters);
    respond(200, 'Driver fetched', $drivers);
    
  }
}
