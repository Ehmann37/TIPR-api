<?php
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../models/TicketModel.php';
require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../utils/ValidationUtils.php'; 
require_once __DIR__ . '/../utils/ResponseUtils.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/QueryUtils.php';


function handleTrip($queryParams){
    $trip_id = $queryParams['trip_id'] ?? null;
    $route_id = $queryParams['route_id'] ?? null;

    if ($trip_id !== null){
        if (!checkTripExist($trip_id)){
            respond('01', 'Trip not Found');
        }

        $tripDetails = getTripDetails($trip_id);

        $filters = buildFilters($queryParams, ['trip_id']);

        
        $tickets = getTickets($filters, 1);

        respond('1', 'Trip Summary Fetched', [
            'trip_details' => $tripDetails,
            'tickets' => $tickets
        ]);
    } else {
        $trip_id = getActiveTrip() ?? null;

     
        if ($trip_id === null) {
            if ($route_id === null) {
                respond('01', 'Route ID is required to start a trip');
                return;
            }

            $id = createInstance($route_id);
            respond('1', 'Trip started sucessfully', [
                'trip_id' => $id
            ]);
            return;
        } else {
            if (checkPassengerLeftBus($trip_id)) {
                respond('01', 'Cannot complete trip, passengers still on bus');
                return;
            }

            $updated = completeInstatnce($trip_id);
            if (!$updated) {
                respond('01', 'No active trip to complete');
                return;
            }
            respond('1', 'Trip completed successfully');
        }    
    }   
}
