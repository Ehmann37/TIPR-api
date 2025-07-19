<?php

require_once __DIR__ . '/../models/StopModel.php';
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../models/TicketModel.php';
require_once __DIR__ . '/../models/FareModel.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/TokenUtils.php';
require_once __DIR__ . '/../utils/TimeUtils.php'; 

function handleTripPost() {
    $KEY = 'mysecretkey1234567890abcdef';
    $data = sanitizeInput(getRequestBody());
    $trip_id = getActiveTrip();

    if ($trip_id === null) {
        respond('01', 'No active trip found');
        return;
    }

    if (isset($data['latitude'], $data['longitude'])) {
        
        $nearestStop = findNearestStop($data['latitude'], $data['longitude']);
        if (!$nearestStop) {
            respond('01', 'No nearby stop found');
            return;
        }

        $payload = [
            'timestamp' => getCurrentTime(),
            'stop_id' => $nearestStop['stop_id'],
            'trip_id' => $trip_id
        ];

        $token = encryptData(json_encode($payload), $KEY);

        respond('1', 'Stop and trip data encrypted', [
            'stop_name' => $nearestStop['stop_name'],
            'token' => $token,
            'trip_id' => $trip_id
        ]);

    } elseif (isset($data['id'], $data['payment_id'])) {
        $tripDetails = decryptData($data['id'], $KEY);
        if (!$tripDetails) {
            respond('01', 'Invalid or expired token');
            return;
        }

        $trip_id = getActiveTrip();

        if ($tripDetails['trip_id'] !== $trip_id) {
            respond('01', 'Trip not active or does not match');
            return;
        }

        $tripDetails['current_stop'] = getStopById($tripDetails['stop_id'])['stop_name'] ?? null;
        $tripDetails['current_stop_id'] = getStopById($tripDetails['stop_id'])['stop_id'] ?? null;
        $tripDetails['stops'] = getStopsByTripId($trip_id, $tripDetails['stop_id']);
        unset($tripDetails['stop_id']);

        $passenger = checkPaymentExists($data['payment_id']);

        
        $passengerState = getTicketByPaymentId($data['payment_id'])['payment']['payment_status'] ?? null;

        if (!$passenger) {
            $passengerDetails = ['state' => 'not_exist'];

            respond('1', 'Trip and passenger data', [
            'trip_details' => $tripDetails,
            'passenger_details' => $passengerDetails
            ]);
        } else {
            $passengerDetails = [
                'state' => $passengerState,
                'total_fare' => getTotalFareByPaymentId($data['payment_id']),
                'passengers' => getTicketByPaymentId($data['payment_id'])['tickets']
            ];

            respond('1', 'Trip and passenger data', [
            'trip_details' => $tripDetails,
            'passenger_details' => $passengerDetails
            ]);
        }

        
    } else {
        respond('01', 'Invalid request structure');
    }
}

