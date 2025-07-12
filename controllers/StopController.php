<?php

require_once __DIR__ . '/../models/StopModel.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';

function handleGetStop($queryParams) {
  $id = $queryParams['id'];

  if ($id !== null) {
    if (!checkStopExists($id)) {
      respond(404, 'Stop not found');
      return;
    }

    $stop = getStopById($id);
    respond(200, 'Stop fetched successfully', $stop);
  } else {
    $stops = getAllStops();
    if (empty($stops)) {
        respond(404, 'No stops found');
        return;
    }
    respond(200, 'Stops retrieved successfully', $stops);
  }
}