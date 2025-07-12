<?php

require_once __DIR__ . '/../models/ConductorModel.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/QueryUtils.php';


function handleGetConductor($queryParams) {
  $id = $queryParams['id'];

  if ($id !== null) {
    if (!checkConductorExists($id)) {
        respond(404, 'Conductor not found');
        return;
    }
    
    $conductor = getConductorById($id);
    respond(200, 'Conductor fetched', $conductor);
  } else {
    $allowed = [];
    $filters = buildFilters($queryParams, $allowed);
    
    $conductors = getConductors($filters);
    respond(200, 'Conductors fetched', $conductors);
    
  }
}
