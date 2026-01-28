<?php
/**
 * Referees API Endpoint
 */

require_once '../models/Referee.php';

$referee = new Referee($db);
$action = $route_parts[1] ?? null;

switch($request_method) {
    case 'GET':
        if($action) {
            if($action === 'stats' && isset($route_parts[2])) {
                // Get referee statistics
                $stats = $referee->getStats($route_parts[2]);
                echo json_encode($stats);
            } elseif($action === 'matches' && isset($route_parts[2])) {
                // Get matches refereed
                $stmt = $referee->getMatches($route_parts[2]);
                $matches = $stmt->fetchAll();
                echo json_encode($matches);
            } else {
                // Get single referee by ID
                $item = $referee->getById($action);
                if($item) {
                    echo json_encode($item);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Referee not found'));
                }
            }
        } else {
            // Get all referees
            $stmt = $referee->getAll();
            $items = $stmt->fetchAll();
            echo json_encode($items);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $referee->first_name = $data->first_name;
        $referee->last_name = $data->last_name;
        $referee->nationality = $data->nationality ?? null;
        $referee->birth_date = $data->birth_date ?? null;
        $referee->photo_url = $data->photo_url ?? null;
        
        $id = $referee->create();
        if($id) {
            http_response_code(201);
            echo json_encode(array('message' => 'Referee created', 'id' => $id));
        } else {
            http_response_code(500);
            echo json_encode(array('message' => 'Unable to create referee'));
        }
        break;
        
    case 'PUT':
        if($action) {
            $data = json_decode(file_get_contents("php://input"));
            $referee->id = $action;
            $referee->first_name = $data->first_name;
            $referee->last_name = $data->last_name;
            $referee->nationality = $data->nationality ?? null;
            $referee->birth_date = $data->birth_date ?? null;
            $referee->photo_url = $data->photo_url ?? null;
            
            if($referee->update()) {
                echo json_encode(array('message' => 'Referee updated'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to update referee'));
            }
        }
        break;
        
    case 'DELETE':
        if($action) {
            $referee->id = $action;
            if($referee->delete()) {
                echo json_encode(array('message' => 'Referee deleted'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to delete referee'));
            }
        }
        break;
}
