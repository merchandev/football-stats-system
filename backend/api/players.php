<?php
/**
 * Players API Endpoint
 */

require_once '../models/Player.php';

$player = new Player($db);
$action = $route_parts[1] ?? null;

switch($request_method) {
    case 'GET':
        if($action) {
            if($action === 'stats' && isset($route_parts[2])) {
                // Get player career statistics
                $stats = $player->getCareerStats($route_parts[2]);
                echo json_encode($stats);
            } elseif($action === 'matches' && isset($route_parts[2])) {
                // Get player matches
                $limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;
                $stmt = $player->getMatches($route_parts[2], $limit);
                $matches = $stmt->fetchAll();
                echo json_encode($matches);
            } elseif($action === 'goals-by-rival' && isset($route_parts[2])) {
                // Get goals scored by rival team
                $stmt = $player->getGoalsByRival($route_parts[2]);
                $goals = $stmt->fetchAll();
                echo json_encode($goals);
            } elseif($action === 'by-championship' && isset($route_parts[2])) {
                // Get stats by championship
                $stmt = $player->getStatsByChampionship($route_parts[2]);
                $stats = $stmt->fetchAll();
                echo json_encode($stats);
            } else {
                // Get single player by ID
                $item = $player->getById($action);
                if($item) {
                    echo json_encode($item);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Player not found'));
                }
            }
        } else {
            // Get all players
            $stmt = $player->getAll();
            $items = $stmt->fetchAll();
            echo json_encode($items);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $player->first_name = $data->first_name;
        $player->last_name = $data->last_name;
        $player->birth_date = $data->birth_date ?? null;
        $player->nationality = $data->nationality ?? null;
        $player->position = $data->position ?? null;
        $player->jersey_number = $data->jersey_number ?? null;
        $player->height = $data->height ?? null;
        $player->weight = $data->weight ?? null;
        $player->current_team_id = $data->current_team_id ?? null;
        $player->photo_url = $data->photo_url ?? null;
        
        $id = $player->create();
        if($id) {
            http_response_code(201);
            echo json_encode(array('message' => 'Player created', 'id' => $id));
        } else {
            http_response_code(500);
            echo json_encode(array('message' => 'Unable to create player'));
        }
        break;
        
    case 'PUT':
        if($action) {
            $data = json_decode(file_get_contents("php://input"));
            $player->id = $action;
            $player->first_name = $data->first_name;
            $player->last_name = $data->last_name;
            $player->birth_date = $data->birth_date ?? null;
            $player->nationality = $data->nationality ?? null;
            $player->position = $data->position ?? null;
            $player->jersey_number = $data->jersey_number ?? null;
            $player->height = $data->height ?? null;
            $player->weight = $data->weight ?? null;
            $player->current_team_id = $data->current_team_id ?? null;
            $player->photo_url = $data->photo_url ?? null;
            
            if($player->update()) {
                echo json_encode(array('message' => 'Player updated'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to update player'));
            }
        }
        break;
        
    case 'DELETE':
        if($action) {
            $player->id = $action;
            if($player->delete()) {
                echo json_encode(array('message' => 'Player deleted'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to delete player'));
            }
        }
        break;
}
