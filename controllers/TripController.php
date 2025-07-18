<?php
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../models/TicketModel.php';
require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/QueryUtils.php';


function handleGetTripSummary($queryParams){
    $trip_id = $queryParams['trip_id'] ?? null;

    if ($trip_id !== null){
        if (!checkTripExist($trip_id)){
            respond('01', 'Trip not Found');
        }
    }

    $tripDetails = getTripDetails($trip_id);

    $filters = buildFilters($queryParams, ['trip_id']);

    
    $tickets = getTickets($filters);

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
            if (checkPassengerLeftBus(getActiveTrip($bus_id))) {
                respond('01', 'Cannot complete trip, passengers still on bus');
                return;
            }
            $updated = completeInstatnce($bus_id, $status);
            if (!$updated) {
                respond('01', 'Trip not found or already completed');
                return;
            }
            respond('1', 'Trip completed successfully');
        } elseif ($status === 'active') {
            if (checkBusifActive($bus_id)) {
                respond('01', 'Bus already has an active trip');
                return;
            }
            $id = createInstance($bus_id, $status);
            respond('1', 'Trip started sucessfully', [
                'trip_id' => $id
            ]);
        }

    } catch (Exception $e) {
        respond(500, $e->getMessage());
    }
}
