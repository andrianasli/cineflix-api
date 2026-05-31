<?php
header('Content-Type: application/json');
require_once 'config.php';

// Autoload controllers
spl_autoload_register(function ($class) {
    include 'controllers/' . $class . '.php';
});

// Parse URL & method
$request = $_SERVER['REQUEST_URI'];
$method  = $_SERVER['REQUEST_METHOD'];

$path = parse_url($request, PHP_URL_PATH);
$path = preg_replace('#^/[^/]+/[^/]+#', '', $path);
$path = trim($path, '/');

$segments = explode('/', $path);
$resource = $segments[0] ?? '';
$sub      = $segments[1] ?? null;   // misal: "login", "logout", "register" — atau ID angka
$id       = (isset($segments[1]) && is_numeric($segments[1])) ? $segments[1] : null;

// ---------------------------------------------------------------
// Route: /auth/login | /auth/logout | /auth/register
// Endpoint ini TIDAK memerlukan token
// ---------------------------------------------------------------
if ($resource === 'auth') {
    $authController = new AuthController();
    if ($method === 'POST') {
        switch ($sub) {
            case 'login':
                $authController->login();
                break;
            case 'logout':
                $authController->logout();
                break;
            case 'register':
                $authController->register();
                break;
            default:
                http_response_code(404);
                echo json_encode(["error" => "Auth endpoint tidak ditemukan"]);
        }
    } else {
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
    }
    exit;
}

// ---------------------------------------------------------------
// Seluruh route di bawah ini WAJIB menyertakan token yang valid
// ---------------------------------------------------------------
requireAuth();

$routes = [
    'users'      => 'UserController',
    'films'      => 'FilmController',
    'schedules'  => 'ScheduleController',
    'bookings'   => 'BookingController',
    'payments'   => 'PaymentController',
    'statistics' => 'StatisticsController'
];

if (array_key_exists($resource, $routes)) {
    $controller = new $routes[$resource]();
    switch ($method) {
        case 'GET':
            if ($id) $controller->show($id);
            else     $controller->index();
            break;
        case 'POST':
            $controller->store();
            break;
        case 'PUT':
            if ($id) $controller->update($id);
            else { http_response_code(400); echo json_encode(["error" => "ID wajib untuk update"]); }
            break;
        case 'DELETE':
            if ($id) $controller->destroy($id);
            else { http_response_code(400); echo json_encode(["error" => "ID wajib untuk delete"]); }
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
    }
} else {
    http_response_code(404);
    echo json_encode(["error" => "Resource tidak ditemukan"]);
}
