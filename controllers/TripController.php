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
        respond(400, 'Missing required fields: ' . implode(', ', $missing));
        return;
    }

    $bus_id = $data['bus_id'];
    $status = $data['status'];

    if (!checkBusExists($bus_id)) {
        respond(404, 'Bus not found');
        return;
    }

    if (!in_array($status, ['active', 'complete'])) {
        respond(400, 'Invalid status. Must be "active" or "complete".');
        return;
    }

    try {
        if ($status === 'complete') {
            $updated = completeInstatnce($bus_id, $status);
            if (!$updated) {
                respond(404, 'No active trip found for the bus');
                return;
            }
        } elseif ($status === 'active') {
            if (checkBusifActive($bus_id)) {
                respond(400, 'Bus is already active');
                return;
            }
            $updated = createInstance($bus_id, $status);
        }

        if ($updated) {
            respond(200, 'Trip status updated successfully', ['status' => $status]);
        } else {
            respond(500, 'Failed to update trip status');
        }
    } catch (Exception $e) {
        respond(500, $e->getMessage());
    }
}
