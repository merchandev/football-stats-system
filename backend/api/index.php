<?php
/**
 * API Router
 * Main entry point for all API requests
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle OPTIONS request for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get the request URI and method
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Remove query string and base path
$request_uri = strtok($request_uri, '?');
$base_path = '/api/';
$route = str_replace($base_path, '', $request_uri);
$route_parts = explode('/', trim($route, '/'));

// Database connection
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Route to appropriate endpoint
try {
    $resource = $route_parts[0] ?? '';
    
    switch($resource) {
        case 'championships':
            require_once 'championships.php';
            break;
        case 'teams':
            require_once 'teams.php';
            break;
        case 'players':
            require_once 'players.php';
            break;
        case 'matches':
            require_once 'matches.php';
            break;
        case 'coaches':
            require_once 'coaches.php';
            break;
        case 'referees':
            require_once 'referees.php';
            break;
        case 'stats':
            require_once 'stats.php';
            break;
        case 'export':
            require_once 'export.php';
            break;
        default:
            http_response_code(404);
            echo json_encode(array('message' => 'Endpoint not found'));
            break;
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(array('message' => 'Server error', 'error' => $e->getMessage()));
}
