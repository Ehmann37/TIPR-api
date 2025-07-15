<?php
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../models/TicketModel.php';
require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';


function handleGetTripSummary($queryParams){
    $trip_id = $queryParams['trip_id'] ?? null;

    if ($trip_id !== null){
        if (!checkTripExist($trip_id)){
            respond('01', 'Trip not Found');
        }
    }

    $tripDetails = getTripDetails($trip_id);
    $tickets = getTickets([$trip_id]);

    respond('1', 'Trip Summary Fetched', [
        'trip_details' => $tripDetails,
        'tickets' => $tickets
    ]);
}

function handleUpdateTripStatus() {
    $data = sanitizeInput(getRequestBody());

    $missing = validateFields($data, ['bus_id', 'status']);
    if (!empty($missing)) {
        respond('01', 'Missing required fields: ' . implode(', ', $missing));
        return;
    }

    $bus_id = $data['bus_id'];
    $status = $data['status'];

    if (!checkBusExists($bus_id)) {
        respond('01', 'Bus not found');
        return;
    }

    if (!in_array($status, ['active', 'complete'])) {
        respond('01', 'Invalid status. Must be "active" or "complete".');
        return;
    }

    try {
        if ($status === 'complete') {
            $updated = completeInstatnce($bus_id, $status);
            if (!$updated) {
                respond('01', '00');
                return;
            }
            respond('1', '0');
        } elseif ($status === 'active') {
            if (checkBusifActive($bus_id)) {
                respond('01', '10');
                return;
            }
            $updated = createInstance($bus_id, $status);
            respond('1', '1');
        }

    } catch (Exception $e) {
        respond(500, $e->getMessage());
    }
}
