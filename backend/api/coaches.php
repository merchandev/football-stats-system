<?php
/**
 * Coaches API Endpoint
 */

require_once '../models/Coach.php';

$coach = new Coach($db);
$action = $route_parts[1] ?? null;

switch($request_method) {
    case 'GET':
        if($action) {
            if($action === 'stats' && isset($route_parts[2])) {
                // Get coach statistics by team
                $stmt = $coach->getStatsByTeam($route_parts[2]);
                $stats = $stmt->fetchAll();
                echo json_encode($stats);
            } else {
                // Get single coach by ID
                $item = $coach->getById($action);
                if($item) {
                    echo json_encode($item);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Coach not found'));
                }
            }
        } else {
            // Get all coaches
            $stmt = $coach->getAll();
            $items = $stmt->fetchAll();
            echo json_encode($items);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $coach->first_name = $data->first_name;
        $coach->last_name = $data->last_name;
        $coach->birth_date = $data->birth_date ?? null;
        $coach->nationality = $data->nationality ?? null;
        $coach->photo_url = $data->photo_url ?? null;
        
        $id = $coach->create();
        if($id) {
            http_response_code(201);
            echo json_encode(array('message' => 'Coach created', 'id' => $id));
        } else {
            http_response_code(500);
            echo json_encode(array('message' => 'Unable to create coach'));
        }
        break;
        
    case 'PUT':
        if($action) {
            $data = json_decode(file_get_contents("php://input"));
            $coach->id = $action;
            $coach->first_name = $data->first_name;
            $coach->last_name = $data->last_name;
            $coach->birth_date = $data->birth_date ?? null;
            $coach->nationality = $data->nationality ?? null;
            $coach->photo_url = $data->photo_url ?? null;
            
            if($coach->update()) {
                echo json_encode(array('message' => 'Coach updated'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to update coach'));
            }
        }
        break;
        
    case 'DELETE':
        if($action) {
            $coach->id = $action;
            if($coach->delete()) {
                echo json_encode(array('message' => 'Coach deleted'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to delete coach'));
            }
        }
        break;
}
