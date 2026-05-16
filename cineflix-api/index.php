<?php
header('Content-Type: application/json');
require_once 'config.php';

// Autoload controllers
spl_autoload_register(function ($class) {
    include 'controllers/' . $class . '.php';
});

// Parse URL
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/cineflix-api', '', $path); // sesuaikan base path
$path = trim($path, '/');

$routes = [
    'users' => 'UserController',
    'films' => 'FilmController',
    'schedules' => 'ScheduleController',
    'bookings' => 'BookingController',
    'payments' => 'PaymentController',
    'statistics' => 'StatisticsController'
];

$segments = explode('/', $path);
$resource = $segments[0] ?? '';
$id = $segments[1] ?? null;

if (array_key_exists($resource, $routes)) {
    $controller = new $routes[$resource]();
    switch ($method) {
        case 'GET':
            if ($id) $controller->show($id);
            else $controller->index();
            break;
        case 'POST':
            $controller->store();
            break;
        case 'PUT':
            if ($id) $controller->update($id);
            else http_response_code(400);
            break;
        case 'DELETE':
            if ($id) $controller->destroy($id);
            else http_response_code(400);
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
    }
} else {
    http_response_code(404);
    echo json_encode(["error" => "Resource not found"]);
}