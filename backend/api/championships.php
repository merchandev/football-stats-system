<?php
/**
 * Championships API Endpoint
 */

require_once '../models/Championship.php';

$championship = new Championship($db);
$action = $route_parts[1] ?? null;

switch($request_method) {
    case 'GET':
        if($action) {
            // Get single championship or statistics
            if($action === 'standings' && isset($route_parts[2])) {
                // Get standings for a championship
                $stmt = $championship->getStandings($route_parts[2]);
                $standings = $stmt->fetchAll();
                echo json_encode($standings);
            } elseif($action === 'scorers' && isset($route_parts[2])) {
                // Get top scorers
                $stmt = $championship->getTopScorers($route_parts[2]);
                $scorers = $stmt->fetchAll();
                echo json_encode($scorers);
            } elseif($action === 'assisters' && isset($route_parts[2])) {
                // Get top assisters
                $stmt = $championship->getTopAssisters($route_parts[2]);
                $assisters = $stmt->fetchAll();
                echo json_encode($assisters);
            } elseif($action === 'cards' && isset($route_parts[2])) {
                // Get cards statistics
                $stmt = $championship->getCardsStats($route_parts[2]);
                $cards = $stmt->fetchAll();
                echo json_encode($cards);
            } else {
                // Get single championship by ID
                $item = $championship->getById($action);
                if($item) {
                    echo json_encode($item);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Championship not found'));
                }
            }
        } else {
            // Get all championships
            $stmt = $championship->getAll();
            $items = $stmt->fetchAll();
            echo json_encode($items);
        }
        break;
        
    case 'POST':
        // Create new championship
        $data = json_decode(file_get_contents("php://input"));
        $championship->name = $data->name;
        $championship->format = $data->format;
        $championship->year = $data->year;
        $championship->start_date = $data->start_date ?? null;
        $championship->end_date = $data->end_date ?? null;
        $championship->description = $data->description ?? null;
        $championship->country = $data->country ?? null;
        
        $id = $championship->create();
        if($id) {
            http_response_code(201);
            echo json_encode(array('message' => 'Championship created', 'id' => $id));
        } else {
            http_response_code(500);
            echo json_encode(array('message' => 'Unable to create championship'));
        }
        break;
        
    case 'PUT':
        // Update championship
        if($action) {
            $data = json_decode(file_get_contents("php://input"));
            $championship->id = $action;
            $championship->name = $data->name;
            $championship->format = $data->format;
            $championship->year = $data->year;
            $championship->start_date = $data->start_date ?? null;
            $championship->end_date = $data->end_date ?? null;
            $championship->description = $data->description ?? null;
            $championship->country = $data->country ?? null;
            
            if($championship->update()) {
                echo json_encode(array('message' => 'Championship updated'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to update championship'));
            }
        }
        break;
        
    case 'DELETE':
        // Delete championship
        if($action) {
            $championship->id = $action;
            if($championship->delete()) {
                echo json_encode(array('message' => 'Championship deleted'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to delete championship'));
            }
        }
        break;
}
