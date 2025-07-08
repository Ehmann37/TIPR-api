<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/TicketModel.php';
require_once __DIR__ . '/../../models/PaymentModel.php';
require_once __DIR__ . '/../../models/TripModel.php';
require_once __DIR__ . '/../../models/BusModel.php';
require_once __DIR__ . '/../../models/StopModel.php';


checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = !empty($_GET['id']) ? $_GET['id'] : null;
$busId = !empty($_GET['busId']) ? $_GET['busId'] : null;
$passengerStatus = !empty($_GET['passengerStatus']) ? $_GET['passengerStatus'] : null;
$payment_status = !empty($_GET['paymentStatus']) ? $_GET['paymentStatus'] : null;
$latitude = !empty($_GET['latitude']) ? $_GET['latitude'] : null;
$longitude = !empty($_GET['longitude']) ? $_GET['longitude'] : null;

switch ($method) {
    case 'GET':
        if ($id !== null){
            $ticket = getTicketById($id);
            if ($ticket) {
                echo json_encode(['status' => 'success', 'data' => $ticket]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Ticket not found']);
            }
        } else {
            if ($latitude !== null && $longitude !== null){
                if ($busId !== null){
                    $tripId = getActiveTrip($busId);
                    if ($tripId == null) {
                        http_response_code(404);
                        echo json_encode(['status' => 'error', 'message' => 'No active trip found for the bus']);
                        exit;
                    } 

                    $currentStopId = findNearestStop($latitude, $longitude)['stop_id'] ?? null;

                    if ($currentStopId === null) {
                        http_response_code(404);
                        echo json_encode(['status' => 'error', 'message' => 'Location Provided has no nearby stop']);
                        exit;
                    }

                    $stops = getStopsByBusId($busId, $currentStopId);

                    $data = [];

                    foreach ($stops as $stop){
                        $tickets = getTicketsByLocation($stop['stop_id'], $tripId);

                        $data[] = [
                            'destination' => $stop['stop_name'],
                            'ticket_count' => count($tickets),
                            'tickets' => $tickets
                        ];
                    }

                    echo json_encode(['status' => 'success', 'data' => $data]);
                } else{
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Trip ID is required when getting tickets by location']);
                    exit;
                }
            } else {
                $filters = [];

                if ($busId !== null) {
                    $filters['bus_id'] = $busId;
                }
    
                if ($passengerStatus !== null) {
                    $filters['pass$passengerStatus'] = $passengerStatus;
                }
    
                if ($payment_status !== null) {
                    $filters['payment_status'] = $payment_status;
                }
    
                $tickets = getTickets($filters);
                echo json_encode(['status' => 'success', 'data' => $tickets]);
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        

        if (isset($data['payment'])) {
            $ticketData = $data;
            $paymentData = $data['payment'];
            unset($ticketData['payment']);

            $requiredTicketFields = ['bus_id', 'origin_stop_id', 'destination_stop_id', 'full_name', 'seat_number', 'passenger_category', 'passenger_status', 'boarding_time', 'trip_id'];

            foreach ($requiredTicketFields as $field) {
                if (!isset($ticketData[$field])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "Missing required ticket field: $field"]);
                    exit;
                }
            }

            $requiredPaymentFields = ['fare_amount', 'payment_id'];
            foreach ($requiredPaymentFields as $field) {
                if (!isset($paymentData[$field])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "Missing required payment field: $field"]);
                    exit;
                }
            }

            
            try {
                $result = createTicketWithPayment($ticketData, $paymentData);

                if ($result !== null) {
                    incrementTotalPassengers($ticketData['trip_id']);
                    incrementBusPassengerCount($ticketData['bus_id']);
                }

                echo json_encode(['status' => 'success', 'data' => $result]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Payment data is required for ticket creation']);
        }
        break;

    case 'PUT':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing ticket ID']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if ($data === null){
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No data provided to update']);
            exit;
        }

        
        try {
            $result = updateTicket($id, $data);
            if ($result['success']){
                if ($data['passenger_status'] === "left_bus"){
                    decrementBusPassengerCount($id);
                }

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Update Successful',
                    'details' => [
                        'ticket_updated' => $result['ticket_updated'],
                        'payment_updated' => $result['payment_updated']
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No changes were made to ticket or payment.',
                    'details' => [
                        'ticket_updated' => $result['ticket_updated'],
                        'payment_updated' => $result['payment_updated']
                    ]
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing ticket ID']);
            exit;
        }

        try {
            $deleted = deleteTicket($id);
            if ($deleted) {
                echo json_encode(['status' => 'success', 'message' => 'Ticket deleted']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Ticket not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
}