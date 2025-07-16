<?php

require_once __DIR__ . '/../models/LocationModel.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/QueryUtils.php';
require_once __DIR__ . '/../utils/ValidationUtils.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';

function handleGetAmount($queryParams) {
    $origin_id = $queryParams['origin_id'] ?? null;
    $destination_id = $queryParams['destination_id'] ?? null;

    if ($origin_id === null || $destination_id === null) {
        respond('01', 'Origin and destination stop IDs are required');
        return;
    }

    $origin_coordinates = getStopCoordinates($origin_id);
    $destination_coordinates = getStopCoordinates($destination_id);
    if ($origin_coordinates === null || $destination_coordinates === null) {
        respond('01', 'Invalid origin or destination stop ID');
        return;
    }

    $distance = findDistance(
      $origin_coordinates['latitude'],
      $origin_coordinates['longitude'],
      $destination_coordinates['latitude'],
      $destination_coordinates['longitude']
    );

    $baseFare = 40.0;
    $fare_amount = $baseFare + ($distance - 4) * 5;

    respond('1', 'Fare calculated successfully', [
        'fare_amount' => $fare_amount
    ]);
}
