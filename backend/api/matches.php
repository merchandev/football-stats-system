<?php
/**
 * Matches API Endpoint
 */

require_once '../models/Match.php';

$match = new Match($db);
$action = $route_parts[1] ?? null;

switch($request_method) {
    case 'GET':
        if($action) {
            if($action === 'lineup' && isset($route_parts[2])) {
                // Get match lineup
                $stmt = $match->getLineup($route_parts[2]);
                $lineup = $stmt->fetchAll();
                echo json_encode($lineup);
            } elseif($action === 'goals' && isset($route_parts[2])) {
                // Get match goals
                $stmt = $match->getGoals($route_parts[2]);
                $goals = $stmt->fetchAll();
                echo json_encode($goals);
            } elseif($action === 'cards' && isset($route_parts[2])) {
                // Get match cards
                $stmt = $match->getCards($route_parts[2]);
                $cards = $stmt->fetchAll();
                echo json_encode($cards);
            } else {
                // Get single match by ID
                $item = $match->getById($action);
                if($item) {
                    echo json_encode($item);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Match not found'));
                }
            }
        } else {
            // Get all matches (optionally filtered by championship)
            $championship_id = $_GET['championship_id'] ?? null;
            $stmt = $match->getAll($championship_id);
            $items = $stmt->fetchAll();
            echo json_encode($items);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if($action === 'goal') {
            // Add goal to match
            $match->id = $data->match_id;
            if($match->addGoal(
                $data->player_id,
                $data->team_id,
                $data->minute,
                $data->goal_type ?? 'regular',
                $data->assist_player_id ?? null,
                $data->description ?? null
            )) {
                http_response_code(201);
                echo json_encode(array('message' => 'Goal added'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to add goal'));
            }
        } elseif($action === 'card') {
            // Add card to match
            $match->id = $data->match_id;
            if($match->addCard(
                $data->player_id,
                $data->team_id,
                $data->card_type,
                $data->minute,
                $data->reason ?? null
            )) {
                http_response_code(201);
                echo json_encode(array('message' => 'Card added'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to add card'));
            }
        } elseif($action === 'lineup') {
            // Add player to lineup
            $match->id = $data->match_id;
            if($match->addLineup(
                $data->player_id,
                $data->team_id,
                $data->position,
                $data->jersey_number,
                $data->is_starter ?? true
            )) {
                http_response_code(201);
                echo json_encode(array('message' => 'Player added to lineup'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to add player'));
            }
        } else {
            // Create new match
            $match->championship_id = $data->championship_id;
            $match->home_team_id = $data->home_team_id;
            $match->away_team_id = $data->away_team_id;
            $match->match_date = $data->match_date;
            $match->match_time = $data->match_time ?? null;
            $match->stadium_id = $data->stadium_id ?? null;
            $match->referee_id = $data->referee_id ?? null;
            $match->home_coach_id = $data->home_coach_id ?? null;
            $match->away_coach_id = $data->away_coach_id ?? null;
            $match->home_score = $data->home_score ?? 0;
            $match->away_score = $data->away_score ?? 0;
            $match->match_status = $data->match_status ?? 'scheduled';
            $match->round = $data->round ?? null;
            $match->notes = $data->notes ?? null;
            
            $id = $match->create();
            if($id) {
                http_response_code(201);
                echo json_encode(array('message' => 'Match created', 'id' => $id));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to create match'));
            }
        }
        break;
        
    case 'PUT':
        if($action && is_numeric($action)) {
            $data = json_decode(file_get_contents("php://input"));
            $match->id = $action;
            $match->championship_id = $data->championship_id;
            $match->home_team_id = $data->home_team_id;
            $match->away_team_id = $data->away_team_id;
            $match->match_date = $data->match_date;
            $match->match_time = $data->match_time ?? null;
            $match->stadium_id = $data->stadium_id ?? null;
            $match->referee_id = $data->referee_id ?? null;
            $match->home_coach_id = $data->home_coach_id ?? null;
            $match->away_coach_id = $data->away_coach_id ?? null;
            $match->home_score = $data->home_score ?? 0;
            $match->away_score = $data->away_score ?? 0;
            $match->match_status = $data->match_status ?? 'scheduled';
            $match->round = $data->round ?? null;
            $match->notes = $data->notes ?? null;
            
            if($match->update()) {
                echo json_encode(array('message' => 'Match updated'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to update match'));
            }
        }
        break;
        
    case 'DELETE':
        if($action && is_numeric($action)) {
            $match->id = $action;
            if($match->delete()) {
                echo json_encode(array('message' => 'Match deleted'));
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Unable to delete match'));
            }
        }
        break;
}
