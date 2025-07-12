<?php
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';


function handleUpdateTripStatus() {
    $data = sanitizeInput(getRequestBody());

    $missing = validateFields($data, ['bus_id', 'status']);
    if (!empty($missing)) {
        respond(200, 'Missing required fields: ' . implode(', ', $missing));
        return;
    }

    $bus_id = $data['bus_id'];
    $status = $data['status'];

    if (!checkBusExists($bus_id)) {
        respond(200, 'Bus not found');
        return;
    }

    if (!in_array($status, ['active', 'complete'])) {
        respond(200, 'Invalid status. Must be "active" or "complete".');
        return;
    }

    try {
        if ($status === 'complete') {
            $updated = completeInstatnce($bus_id, $status);
            if (!$updated) {
                respond(200, '00');
                return;
            }
            respond(200, '0');
        } elseif ($status === 'active') {
            if (checkBusifActive($bus_id)) {
                respond(200, '10');
                return;
            }
            $updated = createInstance($bus_id, $status);
            respond(200, '1');
        }

    } catch (Exception $e) {
        respond(500, $e->getMessage());
    }
}
