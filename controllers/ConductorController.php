<?php

require_once __DIR__ . '/../models/ConductorModel.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/QueryUtils.php';


function handleGetConductor($queryParams) {
  $id = $queryParams['id'];

  if ($id !== null) {
    if (!checkConductorExists($id)) {
        respond('01', 'Conductor not found');
        return;
    }
    
    $conductor = getConductorById($id);
    respond('1', 'Conductor fetched', $conductor);
  } else {
    $allowed = [];
    $filters = buildFilters($queryParams, $allowed);
    
    $conductors = getConductors($filters);
    respond('1', 'Conductors fetched', $conductors);
    
  }
}
