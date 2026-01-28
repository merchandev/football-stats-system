<?php
/**
 * Teams API Endpoint
 */

require_once '../models/Team.php';

$team = new Team($db);
$action = $route_parts[1] ?? null;

switch($request_method) {
    case 'GET':
        if($action) {
            if($action === 'stats' && isset($route_parts[2])) {
                // Get team statistics
                $team_id = $route_parts[2];
                $championship_id = isset($route_parts[3]) ? $route_parts[3] : null;
                
                if($championship_id) {
                    $stats = $team->getChampionshipStats($team_id, $championship_id);
                    echo json_encode($stats);
                } else {
                    $stmt = $team->getChampionships($team_id);
                    $championships = $stmt->fetchAll();
                    echo json_encode($championships);
                }
            } elseif($action === 'titles' && isset($route_parts[2])) {
                // Get team titles
                $stmt = $team->getTitles($route_parts[2]);
                $titles = $stmt->fetchAll();
                echo json_encode($titles);
            } elseif($action === 'head-to-head' && isset($route_parts[2]) && isset($route_parts[3])) {
                // Get head to head statistics
                $h2h = $team->getHeadToHead($route_parts[2], $route_parts[3]);
                echo json_encode($h2h);
            } elseif($action === 'top-scorers' && isset($route_parts[2])) {
                // Get top scorers for team
                $stmt = $team->getTopScorers($route_parts[2]);
                $scorers = $stmt->fetchAll();
                echo json_encode($scorers);
            } else {
                // Get single team by ID
                $item = $team->getById($action);
                if($item) {
                    echo json_encode($item);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Team not found'));
                }
            }
        } else {
            // Get all teams
            $stmt = $team->getAll();
            $items = $stmt->fetchAll();
            echo json_encode($items);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $team->name = $data->name;
        $team->short_name = $data->short_name ?? null;
        $team->founded_year = $data->founded_year ?? null;
        $team->city = $data->city ?? null;
        $team->country = $data->country ?? null;
        $team->stadium_home = $data->stadium_home ?? null;
        $team->logo_url = $data->logo_url ?? null;
        $team->description = $data->description ?? null;
        
        $id = $team->create();
        if($id) {
            http_response_code(201);
            echo json_encode(array('message' => 'Team created', 'id' => $id));
        } else {
            http_response_code(500);
            echo json_encode(array('message' => 'Unable to create team'));
        }
        break;
        
    case 'PUT':
        if($action) {
            $data = json_decode(file_get_contents("php://input"));
            $team->id = $action;
            $team->name = $data->name;
            $team->short_name = $data->short_name ?? null;
            $team->founded_year = $data->founded_year ?? null;
            $team->city = $data->city ?? null;
            $team->country = $data->country ?? null;
            $team->stadium_home = $data->stadium_home ?? null;
            $team->logo_url = $data->logo_url ?? null;
            $team->description = $data->description ?? null;
            
            if($team->update()) {
                echo json_encode(array('message' => 'Team updated'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to update team'));
            }
        }
        break;
        
    case 'DELETE':
        if($action) {
            $team->id = $action;
            if($team->delete()) {
                echo json_encode(array('message' => 'Team deleted'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to delete team'));
            }
        }
        break;
}
