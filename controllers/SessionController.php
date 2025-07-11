<?php

require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../models/StopModel.php';
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/TokenUtils.php';
require_once __DIR__ . '/../utils/TimeUtils.php'; 

function handleTripPost() {
    $KEY = 'mysecretkey1234567890abcdef';
    $data = sanitizeInput(getRequestBody());

    if (isset($data['latitude'], $data['longitude'], $data['bus_id'])) {
        if (!is_numeric($data['bus_id']) || intval($data['bus_id']) != $data['bus_id']) {
            respond(400, 'Invalid bus_id. It must be an integer.');
            return;
        }

        $nearestStop = findNearestStop($data['latitude'], $data['longitude']);
        $busId = intval($data['bus_id']);

        if (!checkBusExists($busId)) {
            respond(404, 'Bus not found');
            return;
        }

        if (!$nearestStop) {
            respond(404, 'No nearby stop found');
            return;
        }

        $tripId = getActiveTrip($busId);
        if (!$tripId) {
            respond(400, 'No active trip for the bus');
            return;
        }

        $payload = [
            'timestamp' => getCurrentTime(),
            'bus_id' => $busId,
            'stop_id' => $nearestStop['stop_id'],
            'trip_id' => $tripId
        ];

        $token = encryptData(json_encode($payload), $KEY);

        respond(200, 'Stop and trip data encrypted', [
            'stop_name' => $nearestStop['stop_name'],
            'token' => $token,
            'trip_id' => $tripId
        ]);

    } elseif (isset($data['id'], $data['payment_id'])) {
        $tripDetails = decryptData($data['id'], $KEY);
        if (!$tripDetails) {
            respond(400, 'Invalid or expired token');
            return;
        }

        $tripDetails['current_stop'] = getStopById($tripDetails['stop_id'])['stop_name'] ?? null;
        $tripDetails['stops'] = getStopsByBusId($tripDetails['bus_id'], $tripDetails['stop_id']);
        unset($tripDetails['stop_id']);

        $passenger = checkPayment($data['payment_id']);
        if (!$passenger) {
            respond(404, 'Payment not found');
            return;
        }

        respond(200, 'Trip and passenger data', [
            'trip_details' => $tripDetails,
            'passenger_details' => $passenger
        ]);
    } else {
        respond(400, 'Invalid request structure');
    }
}

